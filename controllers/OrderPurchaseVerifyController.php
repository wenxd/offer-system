<?php

namespace app\controllers;

use app\models\OrderPayment;
use app\models\OrderPurchase;
use app\models\PaymentGoods;
use app\models\PurchaseGoods;
use Yii;
use yii\filters\VerbFilter;
use app\models\OrderPaymentVerifySearch;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class OrderPurchaseVerifyController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderPaymentVerifySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 保存支出合同单
     */
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['order_purchase_id']);

        //保存支出单
        $orderPayment = new OrderPayment();
        $orderPayment->payment_sn        = $params['payment_sn'];
        $orderPayment->order_id          = $orderPurchase->order_id;
        $orderPayment->order_purchase_id = $orderPurchase->id;
        $orderPayment->order_purchase_sn = $orderPurchase->purchase_sn;
        $orderPayment->admin_id          = $params['admin_id'];
        if ($orderPayment->save()) {
            //payment_goods保存
            foreach ($params['goods_info'] as $key => $value) {
                $paymentGoods = new PaymentGoods();
                $paymentGoods->order_id             = $orderPurchase->order_id;
                $paymentGoods->order_payment_id     = $orderPayment->primaryKey;
                $paymentGoods->order_payment_sn     = $orderPayment->payment_sn;
                $paymentGoods->order_purchase_id    = $orderPurchase->id;
                $paymentGoods->order_purchase_sn    = $orderPurchase->purchase_sn;
                $paymentGoods->purchase_goods_id    = $value['purchase_goods_id'];

                $purchaseGoods = PurchaseGoods::findOne($value['purchase_goods_id']);

                $paymentGoods->serial               = $purchaseGoods->serial;
                $paymentGoods->goods_id             = $purchaseGoods->goods_id;;
                $paymentGoods->relevance_id         = $purchaseGoods->relevance_id;
                $paymentGoods->number               = $purchaseGoods->number;
                $paymentGoods->tax_rate             = $purchaseGoods->tax_rate;
                $paymentGoods->price                = $purchaseGoods->price;
                $paymentGoods->tax_price            = $purchaseGoods->tax_price;
                $paymentGoods->all_price            = $purchaseGoods->all_price;
                $paymentGoods->all_tax_price        = $purchaseGoods->all_tax_price;
                $paymentGoods->fixed_price          = $value['fix_price'];
                $paymentGoods->fixed_tax_price      = $value['fix_price'] * (1 + $purchaseGoods->tax_rate/100);
                $paymentGoods->fixed_all_price      = $value['fix_price'] * $value['fix_number'];
                $paymentGoods->fixed_all_tax_price  = $paymentGoods->fixed_tax_price * $value['fix_number'];
                $paymentGoods->fixed_number         = $value['fix_number'];
                $paymentGoods->inquiry_admin_id     = $params['admin_id'];
                $paymentGoods->save();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    /**采购审核详情
     * @param $id
     */
    public function actionDetail($id)
    {
        $orderPayment = OrderPayment::findOne($id);

        $paymentGoods = PaymentGoods::find()->where(['order_payment_id' => $id])->all();

        return $this->render('detail', [
            'model'        => $orderPayment,
            'orderPayment' => $orderPayment,
            'paymentGoods' => $paymentGoods,
        ]);
    }
}
