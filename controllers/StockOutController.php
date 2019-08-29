<?php
namespace app\controllers;

use app\models\Inquiry;
use app\models\Order;
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
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $data = [];

        $order = Order::findOne($id);
        if (!$order){
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $orderGoods    = OrderGoods::findAll(['order_id' => $id]);

        $stockLog = StockLog::find()->where([
            'order_id' => $id,
            'type' => StockLog::TYPE_OUT
        ])->all();

        $data['model']         = $order;
        $data['orderGoods']    = $orderGoods;
        $data['stockLog']      = $stockLog;

        return $this->render('detail', $data);
    }

    public function actionOut()
    {
        $params = Yii::$app->request->post();

        $orderGoods = OrderGoods::findOne($params['id']);
        $orderGoods->is_out = OrderGoods::IS_OUT_YES;
        $orderGoods->save();

        $orderPurchase = OrderPurchase::findOne(['order_id' => $orderGoods->order_id]);

        $stockLog                    = new StockLog();
        $stockLog->order_id          = $orderGoods['order_id'];
        $stockLog->order_purchase_id = $orderPurchase['id'];
        $stockLog->purchase_sn       = $orderPurchase['purchase_sn'];
        $stockLog->goods_id          = $orderGoods['goods_id'];
        $stockLog->number            = $orderGoods['number'];
        $stockLog->type              = StockLog::TYPE_OUT;
        $stockLog->operate_time      = date('Y-m-d H:i:s');
        $stockLog->admin_id          = Yii::$app->user->identity->id;
        if ($stockLog->save()) {
            $stock = Stock::findOne(['good_id' => $orderGoods['goods_id']]);
            if (!$stock) {
                $purchaseGoods = PurchaseGoods::findOne([
                    'order_purchase_id' => $orderPurchase['id'],
                    'order_id'          => $orderGoods->order_id
                ]);
                $inquiry = Inquiry::findOne($purchaseGoods->relevance_id);
                $stock = new Stock();
                $stock->good_id     = $params['goods_id'];
                $stock->supplier_id = $inquiry->supplier_id;
                $stock->price       = $inquiry->price;
                $stock->tax_price   = $inquiry->tax_price;
                $stock->tax_rate    = $inquiry->tax_rate;
                $stock->number      = $params['number'];
                $stock->save();
            }
            $res = Stock::updateAllCounters(['number' => -$orderGoods['number']], ['good_id' => $orderGoods['goods_id']]);
            if ($res) {
                return json_encode(['code' => 200, 'msg' => '入库成功']);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }
}
