<?php
namespace app\controllers;

use app\models\AgreementGoods;
use app\models\Inquiry;
use app\models\Order;
use app\models\OrderAgreement;
use app\models\OrderAgreementStockOutSearch;
use app\models\OrderGoods;
use app\models\OrderPayment;
use app\models\OrderPurchase;
use app\models\PaymentGoods;
use app\models\PurchaseGoods;
use app\models\Stock;
use app\models\StockLog;
use Yii;
use app\models\OrderSearch;

class StockOutController extends BaseController
{
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderAgreementStockOutSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $data = [];

        $orderAgreement = OrderAgreement::findOne($id);
        if (!$orderAgreement){
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $agreementGoods    = AgreementGoods::findAll(['order_agreement_id' => $id]);

        $stockLog = StockLog::find()->where([
            'order_id' => $orderAgreement->order_id,
            'type' => StockLog::TYPE_OUT
        ])->all();

        $data['model']         = $orderAgreement;
        $data['orderGoods']    = $agreementGoods;
        $data['stockLog']      = $stockLog;

        return $this->render('detail', $data);
    }

    /**出库
     * @return false|string
     */
    public function actionOut()
    {
        $params = Yii::$app->request->post();

        $agreementGoods = AgreementGoods::findOne($params['id']);

        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);
        $order_id = $orderAgreement->order_id;

        //采购
        $purchaseGoods = PurchaseGoods::find()->where([
            'order_id'           => $order_id,
            'order_agreement_id' => $orderAgreement->id,
            'serial'             => $agreementGoods->serial,
            'goods_id'           => $agreementGoods->goods_id,
        ])->one();

        //支出
        $paymentGoods = PaymentGoods::find()->where([
            'purchase_goods_id' => ($purchaseGoods ? $purchaseGoods->id : 0),
        ])->one();

        //判断库存是否够
        $stock = Stock::findOne(['good_id' => $agreementGoods['goods_id']]);
        if (!$stock || ($stock && $stock->number < $agreementGoods['number'])) {
            return json_encode(['code' => 500, 'msg' => '库存不够了'], JSON_UNESCAPED_UNICODE);
        }

        $stockLog                       = new StockLog();
        $stockLog->order_id             = $orderAgreement['order_id'];

        $stockLog->order_payment_id     = $paymentGoods ? $paymentGoods->order_payment_id : 0;
        $stockLog->payment_sn           = $paymentGoods ? $paymentGoods->order_payment_sn : '';

        $stockLog->order_agreement_id   = $orderAgreement->id;
        $stockLog->agreement_sn         = $orderAgreement->agreement_sn;

        $stockLog->order_purchase_id    = $purchaseGoods ? $purchaseGoods->order_purchase_id : 0;
        $stockLog->purchase_sn          = $purchaseGoods ? $purchaseGoods->order_purchase_sn : '';

        $stockLog->goods_id             = $agreementGoods['goods_id'];
        $stockLog->number               = $agreementGoods['number'];
        $stockLog->type                 = StockLog::TYPE_OUT;
        $stockLog->operate_time         = date('Y-m-d H:i:s');
        $stockLog->admin_id             = Yii::$app->user->identity->id;
        if ($stockLog->save()) {
            if (!$stock) {
                $inquiry = Inquiry::findOne($agreementGoods->relevance_id);
                $stock = new Stock();
                $stock->good_id     = $agreementGoods->goods_id;
                $stock->supplier_id = $inquiry->supplier_id;
                $stock->price       = $agreementGoods->quote_price;
                $stock->tax_price   = $agreementGoods->quote_tax_price;
                $stock->tax_rate    = $agreementGoods->tax_rate;
                $stock->number      = $agreementGoods->number;
                $stock->save();
            }
            $res = Stock::updateAllCounters(['number' => -$agreementGoods->number], ['good_id' => $agreementGoods->goods_id]);
            if ($res) {
                $agreementGoods->is_out = AgreementGoods::IS_OUT_YES;
                $agreementGoods->save();
                return json_encode(['code' => 200, 'msg' => '出库成功']);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 批量出库
     */
    public function actionMoreOut()
    {
        $params = Yii::$app->request->post();

        $agreementGoods = AgreementGoods::findAll(['id' => $params['ids']]);

        $orderAgreement = OrderAgreement::findOne($agreementGoods[0]->order_agreement_id);
        $order_id = $orderAgreement->order_id;

        foreach ($agreementGoods as $agreementGood) {
            //采购
            $purchaseGoods = PurchaseGoods::find()->where([
                'order_id'           => $order_id,
                'order_agreement_id' => $orderAgreement->id,
                'serial'             => $agreementGood->serial,
                'goods_id'           => $agreementGood->goods_id,
            ])->one();

            //支出
            $paymentGoods = PaymentGoods::find()->where([
                'purchase_goods_id' => ($purchaseGoods ? $purchaseGoods->id : 0),
            ])->one();

            $stock = Stock::findOne(['good_id' => $agreementGood['goods_id']]);
            if (!$stock || ($stock && $stock->number < $agreementGood['number'])) {
                return json_encode(['code' => 500, 'msg' => $agreementGood->goods->goods_number . '库存不够了'], JSON_UNESCAPED_UNICODE);
            }
            //采购
            $orderPurchase = OrderPurchase::find()->where(['order_id' => $order_id, 'order_agreement_id' => $orderAgreement->id])->one();
            $stockLog                    = new StockLog();
            $stockLog->order_id          = $orderAgreement['order_id'];

            $stockLog->order_payment_id     = $paymentGoods ? $paymentGoods->order_payment_id : 0;
            $stockLog->payment_sn           = $paymentGoods ? $paymentGoods->order_payment_sn : '';

            $stockLog->order_agreement_id   = $orderAgreement->id;
            $stockLog->agreement_sn         = $orderAgreement->agreement_sn;

            $stockLog->order_purchase_id    = $purchaseGoods ? $purchaseGoods->order_purchase_id : 0;
            $stockLog->purchase_sn          = $purchaseGoods ? $purchaseGoods->order_purchase_sn : '';

            $stockLog->goods_id          = $agreementGood['goods_id'];
            $stockLog->number            = $agreementGood['number'];
            $stockLog->type              = StockLog::TYPE_OUT;
            $stockLog->operate_time      = date('Y-m-d H:i:s');
            $stockLog->admin_id          = Yii::$app->user->identity->id;
            if ($stockLog->save()) {
                if (!$stock) {
                    $inquiry = Inquiry::findOne($agreementGood->relevance_id);
                    $stock = new Stock();
                    $stock->good_id = $agreementGood->goods_id;
                    $stock->supplier_id = $inquiry->supplier_id;
                    $stock->price = $agreementGood->quote_price;
                    $stock->tax_price = $agreementGood->quote_tax_price;
                    $stock->tax_rate = $agreementGood->tax_rate;
                    $stock->number = $agreementGood->number;
                    $stock->save();
                }
                $res = Stock::updateAllCounters(['number' => -$agreementGood->number], ['good_id' => $agreementGood->goods_id]);
                if ($res) {
                    $agreementGood->is_out = AgreementGoods::IS_OUT_YES;
                    $agreementGood->save();
                }
            }
        }

        return json_encode(['code' => 200, 'msg' => '出库成功']);
    }
}
