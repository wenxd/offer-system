<?php

namespace app\controllers;

use app\models\AgreementStock;
use app\models\Inquiry;
use app\models\InquiryGoods;
use app\models\OrderAgreement;
use app\models\OrderPayment;
use app\models\OrderPaymentSearch;
use app\models\PaymentGoods;
use app\models\Stock;
use app\models\StockLog;
use app\models\SystemConfig;
use Yii;
use app\models\OrderFinal;
use app\models\PurchaseGoods;
use app\models\OrderPurchase;
use app\models\OrderPurchaseSearch;
use yii\helpers\ArrayHelper;


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

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderPayment = OrderPayment::findOne($params['order_payment_id']);
            $orderPayment->stock_admin_id = Yii::$app->user->identity->id;
            $orderPayment->save();

            //系统税率
            $system_tax = SystemConfig::find()->select('value')->where([
                'title' => SystemConfig::TITLE_TAX,
                'is_deleted' => SystemConfig::IS_DELETED_NO,
            ])->scalar();

            $stockLog = StockLog::find()->where([
                'order_id' => $orderPayment['order_id'],
                'order_payment_id' => $orderPayment->id,
                'goods_id' => $params['goods_id'],
                'type' => StockLog::TYPE_IN,
            ])->one();
            if (!$stockLog) {
                $stockLog = new StockLog();
            }
            $stockLog->order_id = $orderPayment['order_id'];
            $stockLog->order_payment_id = $orderPayment->id;
            $stockLog->payment_sn = $orderPayment->payment_sn;
            $stockLog->goods_id = $params['goods_id'];
            $stockLog->number = $params['number'];
            $stockLog->type = StockLog::TYPE_IN;
            $stockLog->operate_time = date('Y-m-d H:i:s');
            $stockLog->admin_id = Yii::$app->user->identity->id;
            $stockLog->position = $params['position'];
            if ($stockLog->save()) {
                $stock = Stock::findOne(['good_id' => $params['goods_id']]);
                $paymentGoods = PaymentGoods::findOne([
                    'order_payment_id' => $orderPayment->id,
                    'order_id' => $orderPayment['order_id'],
                    'goods_id' => $params['goods_id']
                ]);
                if (!$stock) {
                    $inquiry = Inquiry::findOne($paymentGoods->relevance_id);
                    $stock = new Stock();
                    $stock->good_id = $params['goods_id'];
                    $stock->supplier_id = $inquiry->supplier_id;
                    $stock->price = $paymentGoods->fixed_price;
                    $stock->tax_price = (1 + $system_tax / 100) * $paymentGoods->fixed_price;
                    $stock->tax_rate = $system_tax;
                    $stock->number = 0;
                    $stock->position = trim($params['position']);
                    $stock->save();
                } else {
                    $stock->price = $paymentGoods->fixed_price;
                    $stock->tax_price = (1 + $system_tax / 100) * $paymentGoods->fixed_price;
                    $stock->tax_rate = $system_tax;
                    $stock->position = trim($params['position']);
                    $stock->save();
                }
                //判断是否全部入库
                $paymentCount = PaymentGoods::find()->where(['order_payment_id' => $orderPayment->id])->count();
                $stockCount = StockLog::find()->where(['order_payment_id' => $orderPayment->id])->count();
                if ($paymentCount == $stockCount) {
                    $orderPayment->is_stock = OrderPayment::IS_STOCK_YES;
                    $orderPayment->stock_at = date('Y-m-d H:i:s');
                    if ($orderPayment->is_advancecharge && $orderPayment->is_payment && $orderPayment->is_bill) {
                        $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
                    }
                    $orderPayment->save();
                    //进行对收入合同的入库字段更改
                    //TODO $orderAgreement = OrderAgreement::findOne();
                }
                $res = Stock::updateAllCounters(['number' => $params['number'], 'temp_number' => $params['number']], ['good_id' => $params['goods_id']]);
                // 计算库存前，添加使用库存(项目入库)，不用确认和驳回，直接默认是已确认未出库状态
                // 加入使用库存列表
                $stock_model = new AgreementStock();
                $tax_price = (1 + $system_tax / 100) * $paymentGoods->fixed_price;
                $stock_data = [
                    'order_id' => $orderPayment->order_id,
                    'order_purchase_id' => $orderPayment->order_purchase_id,
                    'order_purchase_sn' => $orderPayment->order_purchase_sn,
                    'goods_id' => $stock->good_id,
                    'price' => $paymentGoods->fixed_price,
                    'tax_price' => $tax_price,
                    'use_number' => $params['number'],
                    'all_price' => $paymentGoods->fixed_price * $params['number'],
                    'all_tax_price' => $tax_price * $params['number'],
                    'source' => AgreementStock::PROJECT,
                    'confirm_at' => date('Y-m-d H:i:s'),
                    'admin_id' => Yii::$app->user->identity->id,
                    'is_confirm' => AgreementStock::IS_CONFIRM_YES,
                    'stock_number' => $stock->number,
                    'temp_number' => $stock->temp_number,
                ];
                if (!$stock_model->load(['AgreementStock' => $stock_data]) || !$stock_model->save()) {
                    $transaction->rollBack();
                    return json_encode(['code' => 502, 'msg' => $stock_model->getErrors()], JSON_UNESCAPED_UNICODE);
                }
                Stock::countTempNumber([$stock->good_id]);
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
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '入库成功'], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode(['code' => 500, 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 批量入库
     */
    public function actionMoreIn()
    {
        $paymentGoods_info = Yii::$app->request->post('paymentGoods_info');

        //系统税率
        $system_tax = SystemConfig::find()->select('value')->where([
            'title'      => SystemConfig::TITLE_TAX,
            'is_deleted' => SystemConfig::IS_DELETED_NO,
        ])->scalar();

        try {
            $ids = ArrayHelper::getColumn($paymentGoods_info, 'payment_goods_id');
            $paymentGoodsList = PaymentGoods::find()->where(['id' => $ids])->indexBy('id')->asArray()->all();
            foreach ($paymentGoods_info as $key => $value) {
                if (isset($paymentGoodsList[$value['payment_goods_id']])) {
                    $paymentGoods   = $paymentGoodsList[$value['payment_goods_id']];
                    if ($key == 0) {
                        $orderPaymentId = $paymentGoods['order_payment_id'];
                        $orderPayment   = OrderPayment::findOne($orderPaymentId);
                        $orderPayment->stock_admin_id = Yii::$app->user->identity->id;
                        $orderPayment->save();
                    }
                    $stockLog = StockLog::find()->where([
                        'order_id'         => $orderPayment['order_id'],
                        'order_payment_id' => $orderPayment->id,
                        'goods_id'         => $paymentGoods['goods_id'],
                        'type'             => StockLog::TYPE_IN,
                    ])->one();
                    if (!$stockLog) {
                        $stockLog = new StockLog();
                    }
                    $stockLog->order_id         = $orderPayment['order_id'];
                    $stockLog->order_payment_id = $orderPayment->id;
                    $stockLog->payment_sn       = $orderPayment->payment_sn;
                    $stockLog->goods_id         = $paymentGoods['goods_id'];
                    $stockLog->number           = $paymentGoods['fixed_number'];
                    $stockLog->type             = StockLog::TYPE_IN;
                    $stockLog->operate_time     = date('Y-m-d H:i:s');
                    $stockLog->admin_id         = Yii::$app->user->identity->id;
                    $stockLog->position         = $value['position'];
                    if ($stockLog->save()) {
                        $stock = Stock::findOne(['good_id' => $paymentGoods['goods_id']]);
                        if (!$stock) {
                            $stock   = new Stock();
                            $stock->good_id     = $paymentGoods['goods_id'];
                            $stock->price       = $paymentGoods['fixed_price'];
                            $stock->tax_price   = (1+$system_tax/100) * $paymentGoods['fixed_price'];
                            $stock->tax_rate    = $system_tax;
                            $stock->number      = 0;
                            $stock->position    = $value['position'];
                            $stock->save();
                        } else {
                            $stock->price       = $paymentGoods['fixed_price'];
                            $stock->tax_price   = (1+$system_tax/100) * $paymentGoods['fixed_price'];
                            $stock->tax_rate    = $system_tax;
                            $stock->position    = $value['position'];
                            $stock->save();
                        }
                    }
                    $res = Stock::updateAllCounters(['number' => $paymentGoods['fixed_number'], 'temp_number' => $paymentGoods['fixed_number']], ['good_id' => $paymentGoods['goods_id']]);
                    Stock::countTempNumber([$stock->good_id]);
                    //对采购商品进行入库改变
                    $purchaseGoods = PurchaseGoods::findOne($paymentGoods['purchase_goods_id']);
                    $purchaseGoods->is_stock = PurchaseGoods::IS_STOCK_YES;
                    $purchaseGoods->save();
                }
            }
            //判断是否全部入库
            $paymentCount = count($paymentGoodsList);
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
