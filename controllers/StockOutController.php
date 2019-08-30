<?php
namespace app\controllers;

use app\models\AgreementGoods;
use app\models\Inquiry;
use app\models\Order;
use app\models\OrderAgreement;
use app\models\OrderAgreementSearch;
use app\models\OrderGoods;
use app\models\OrderPurchase;
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
        $searchModel = new OrderAgreementSearch();
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

    public function actionOut()
    {
        $params = Yii::$app->request->post();

        $agreementGoods = AgreementGoods::findOne($params['id']);
        $agreementGoods->is_out = AgreementGoods::IS_OUT_YES;
        $agreementGoods->save();

        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);
        $order_id = $orderAgreement->order_id;

        $orderPurchase = OrderPurchase::find()->where(['order_id' => $order_id, 'order_agreement_id' => $orderAgreement->id])->one();

        //判断库存是否够
        $stock = Stock::findOne(['good_id' => $agreementGoods['goods_id']]);
        if (!$stock || ($stock && $stock->number < $agreementGoods['number'])) {
            return json_encode(['code' => 500, 'msg' => '库存不够了'], JSON_UNESCAPED_UNICODE);
        }

        $stockLog                    = new StockLog();
        $stockLog->order_id          = $orderAgreement['order_id'];
        $stockLog->order_purchase_id = $orderPurchase ? $orderPurchase : 0;
        $stockLog->purchase_sn       = $orderPurchase ? $orderPurchase['purchase_sn'] : '';
        $stockLog->order_payment_id  = $orderAgreement->id;
        $stockLog->purchase_sn       = $orderAgreement->agreement_sn;
        $stockLog->goods_id          = $agreementGoods['goods_id'];
        $stockLog->number            = $agreementGoods['number'];
        $stockLog->type              = StockLog::TYPE_OUT;
        $stockLog->operate_time      = date('Y-m-d H:i:s');
        $stockLog->admin_id          = Yii::$app->user->identity->id;
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
                return json_encode(['code' => 200, 'msg' => '出库成功']);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }
}
