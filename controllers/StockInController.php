<?php

namespace app\controllers;

use app\models\Inquiry;
use app\models\InquiryGoods;
use app\models\OrderPayment;
use app\models\OrderPaymentSearch;
use app\models\PaymentGoods;
use app\models\Stock;
use app\models\StockLog;
use Yii;
use app\models\OrderFinal;
use app\models\PurchaseGoods;
use app\models\OrderPurchase;
use app\models\OrderPurchaseSearch;



/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class StockInController extends BaseController
{
    /**
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $orderPayment = OrderPayment::findOne($id);
        $paymentGoods = PaymentGoods::findAll(['order_payment_id' => $id]);
        $stockLog = StockLog::find()->where([
            'order_id'          => $orderPayment->order_id,
            'order_payment_id'  => $id,
            'type'              => StockLog::TYPE_IN
        ])->all();

        $data = [];
        $data['orderPayment'] = $data['model'] = $orderPayment;
        $data['paymentGoods'] = $paymentGoods;
        $data['stockLog']     = $stockLog;

        return $this->render('detail', $data);
    }

    //单个入库
    public function actionIn()
    {
        $params = Yii::$app->request->post();
        $orderPayment = OrderPayment::findOne($params['order_payment_id']);

        $stockLog = StockLog::find()->where([
            'order_id'          => $orderPayment['order_id'],
            'order_payment_id'  => $orderPayment->id,
            'goods_id'          => $params['goods_id'],
            'type'              => StockLog::TYPE_IN,
        ])->one();
        if (!$stockLog) {
            $stockLog                    = new StockLog();
        }
        $stockLog->order_id          = $orderPayment['order_id'];
        $stockLog->order_payment_id  = $orderPayment->id;
        $stockLog->payment_sn        = $orderPayment->payment_sn;
        $stockLog->goods_id          = $params['goods_id'];
        $stockLog->number            = $params['number'];
        $stockLog->type              = StockLog::TYPE_IN;
        $stockLog->operate_time      = date('Y-m-d H:i:s');
        $stockLog->admin_id          = Yii::$app->user->identity->id;
        if ($stockLog->save()) {
            $stock = Stock::findOne(['good_id' => $params['goods_id']]);
            $paymentGoods = PaymentGoods::findOne([
                'order_payment_id'  => $orderPayment->id,
                'order_id'          => $orderPayment['order_id'],
                'goods_id'          => $params['goods_id']
            ]);
            if (!$stock) {
                $inquiry = Inquiry::findOne($paymentGoods->relevance_id);
                $stock   = new Stock();
                $stock->good_id     = $params['goods_id'];
                $stock->supplier_id = $inquiry->supplier_id;
                $stock->price       = $paymentGoods->fixed_price;
                $stock->tax_price   = $paymentGoods->fixed_tax_price;
                $stock->tax_rate    = $paymentGoods->tax_rate;
                $stock->number      = 0;
                $stock->save();
            } else {
                $stock->price       = $paymentGoods->fixed_price;
                $stock->tax_price   = $paymentGoods->fixed_tax_price;
                $stock->tax_rate    = $paymentGoods->tax_rate;
                $stock->save();
            }
            //判断是否全部入库
            $paymentCount = PaymentGoods::find()->where(['order_payment_id' => $orderPayment->id])->count();
            $stockCount   = StockLog::find()->where(['order_payment_id' => $orderPayment->id])->count();
            if ($paymentCount == $stockCount) {
                $orderPayment->is_stock = OrderPayment::IS_STOCK_YES;
                $orderPayment->stock_at = date('Y-m-d H:i:s');
                if ($orderPayment->is_advancecharge && $orderPayment->is_payment && $orderPayment->is_bill) {
                    $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
                }
                $orderPayment->save();
                //进行对收入合同的入库字段更改

            }
            $res = Stock::updateAllCounters(['number' => $params['number']], ['good_id' => $params['goods_id']]);
            if ($res) {
                //对采购商品进行入库改变
                $purchaseGoods = PurchaseGoods::findOne($paymentGoods->purchase_goods_id);
                $purchaseGoods->is_stock = PurchaseGoods::IS_STOCK_YES;
                $purchaseGoods->save();
                //对采购单进行判断是否入库
                $purchaseCount = PurchaseGoods::find()->where(['order_purchase_id' => $orderPayment->order_purchase_id])
                    ->andWhere(['is_stock' => PurchaseGoods::IS_STOCK_NO])->one();
                if (!$purchaseCount) {
                    $purchaseOrder = OrderPurchase::findOne($orderPayment->order_purchase_id);
                    $purchaseOrder->is_stock = OrderPurchase::IS_STOCK_YES;
                    $purchaseOrder->save();
                }
                return json_encode(['code' => 200, 'msg' => '入库成功'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 批量入库
     */
    public function actionMoreIn()
    {
        $ids = Yii::$app->request->post('ids');
        try {
            $paymentGoods = PaymentGoods::findAll(['id' => $ids]);
            $orderPaymentId = $paymentGoods[0]->order_payment_id;
            $orderPayment = OrderPayment::findOne($orderPaymentId);
            foreach ($paymentGoods as $key => $paymentGood) {

                $stockLog = StockLog::find()->where([
                    'order_id' => $orderPayment['order_id'],
                    'order_payment_id' => $orderPayment->id,
                    'goods_id' => $paymentGood['goods_id'],
                    'type' => StockLog::TYPE_IN,
                ])->one();
                if (!$stockLog) {
                    $stockLog = new StockLog();
                }
                $stockLog->order_id         = $orderPayment['order_id'];
                $stockLog->order_payment_id = $orderPayment->id;
                $stockLog->payment_sn       = $orderPayment->payment_sn;
                $stockLog->goods_id         = $paymentGood['goods_id'];
                $stockLog->number           = $paymentGood['fixed_number'];
                $stockLog->type             = StockLog::TYPE_IN;
                $stockLog->operate_time     = date('Y-m-d H:i:s');
                $stockLog->admin_id         = Yii::$app->user->identity->id;
                $stockLog->save();
                $res = Stock::updateAllCounters(['number' => $paymentGood['fixed_number']], ['good_id' => $paymentGood['goods_id']]);
                //对采购商品进行入库改变
                $purchaseGoods = PurchaseGoods::findOne($paymentGood->purchase_goods_id);
                $purchaseGoods->is_stock = PurchaseGoods::IS_STOCK_YES;
                $purchaseGoods->save();
            }

            //判断是否全部入库
            $paymentCount = count($paymentGoods);
            $stockCount = StockLog::find()->where(['order_payment_id' => $orderPayment->id])->count();
            if ($paymentCount == $stockCount) {
                $orderPayment->is_stock = OrderPayment::IS_STOCK_YES;
                $orderPayment->stock_at = date('Y-m-d H:i:s');
                if ($orderPayment->is_advancecharge && $orderPayment->is_payment && $orderPayment->is_bill) {
                    $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
                }
                $orderPayment->save();
            }

            //对采购单进行判断是否入库
            $purchaseCount = PurchaseGoods::find()->where(['order_purchase_id' => $orderPayment->order_purchase_id])
                ->andWhere(['is_stock' => PurchaseGoods::IS_STOCK_NO])->one();
            if (!$purchaseCount) {
                $purchaseOrder = OrderPurchase::findOne($orderPayment->order_purchase_id);
                $purchaseOrder->is_stock = OrderPurchase::IS_STOCK_YES;
                $purchaseOrder->save();
            }
            return json_encode(['code' => 200, 'msg' => '入库成功'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 质检
     */
    public function actionQuality()
    {
        $id = Yii::$app->request->post('payment_goods_id');
        $paymentGoods = PaymentGoods::findOne($id);
        $paymentGoods->is_quality = PaymentGoods::IS_QUALITY_YES;
        if ($paymentGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '质检成功'], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => $paymentGoods->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 批量质检
     */
    public function actionMoreQuality()
    {
        $ids = Yii::$app->request->post('ids');
        $num = PaymentGoods::updateAll(['is_quality' => PaymentGoods::IS_QUALITY_YES], ['id' => $ids]);
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '质检成功'], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => '失败'], JSON_UNESCAPED_UNICODE);
        }
    }
}
