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

    public function actionIn()
    {
        $params = Yii::$app->request->post();
        $orderPurchase = OrderPurchase::findOne($params['order_purchase_id']);

        $stockLog = StockLog::find()->where([
            'order_id'          => $orderPurchase['order_id'],
            'order_purchase_id' => $params['order_purchase_id'],
            'goods_id'          => $params['order_purchase_id'],
            'type'              => StockLog::TYPE_IN,
        ])->one();
        if (!$stockLog) {
            $stockLog                    = new StockLog();
        }
        $stockLog->order_id          = $orderPurchase['order_id'];
        $stockLog->order_purchase_id = $params['order_purchase_id'];
        $stockLog->purchase_sn       = $orderPurchase['purchase_sn'];
        $stockLog->goods_id          = $params['goods_id'];
        $stockLog->number            = $params['number'];
        $stockLog->type              = StockLog::TYPE_IN;
        $stockLog->operate_time      = date('Y-m-d H:i:s');
        if ($stockLog->save()) {
            $stock = Stock::findOne(['good_id' => $params['goods_id']]);
            if (!$stock) {
                $purchaseGoods = PurchaseGoods::findOne([
                    'order_purchase_id' => $params['order_purchase_id'],
                    'order_id'          => $orderPurchase['order_id'],
                    'goods_id'          => $params['goods_id']
                ]);
                $inquiry = Inquiry::findOne($purchaseGoods->relevance_id);
                $stock = new Stock();
                $stock->good_id     = $params['goods_id'];
                $stock->supplier_id = $inquiry->supplier_id;
                $stock->price       = $inquiry->price;
                $stock->tax_price   = $inquiry->tax_price;
                $stock->tax_rate    = $inquiry->tax_rate;
                $stock->number      = 0;
                $stock->save();
            }
            $purchaseCount = PurchaseGoods::find()->where(['order_purchase_id' => $params['order_purchase_id']])->count();
            $stockCount    = StockLog::find()->where(['order_purchase_id' => $params['order_purchase_id']])->count();
            if ($purchaseCount == $stockCount) {
                $orderPurchase->is_stock = OrderPurchase::IS_STOCK_YES;
                $orderPurchase->save();
            }
            $res = Stock::updateAllCounters(['number' => $params['number']], ['good_id' => $params['goods_id']]);
            if ($res) {
                return json_encode(['code' => 200, 'msg' => '入库成功']);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }
}
