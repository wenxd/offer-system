<?php

namespace app\controllers;

use app\models\Inquiry;
use app\models\OrderQuote;
use app\models\QuoteGoods;
use app\models\SystemConfig;
use Yii;
use app\models\OrderFinal;
use app\models\OrderFinalSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\FinalGoods;
use app\models\InquiryGoods;
use app\models\Order;
use app\models\OrderGoods;
use app\models\OrderFinalQuoteSearch;
use app\models\OrderPurchase;
use app\models\PurchaseGoods;

/**
 * OrderFinalController implements the CRUD actions for OrderFinal model.
 */
class OrderFinalController extends BaseController
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
     * Lists all OrderFinal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderFinalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderFinal model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $orderFinal = $this->findModel($id);
        $finalGoods = FinalGoods::find()->where([
            'order_id'       => $orderFinal->order_id,
            'order_final_id' => $orderFinal->id,
        ])->orderBy('serial')->all();


        return $this->render('view2', [
            'model'      => $orderFinal,
            'finalGoods' => $finalGoods
        ]);
    }

    /**
     * Creates a new OrderFinal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderFinal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderFinal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderFinal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderFinal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderFinal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderFinal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //生成关联数据
    public function actionRelevance()
    {
        $params = Yii::$app->request->post();

        $orderGoods = OrderGoods::find()->where([
            'order_id'     => $params['order_id'],
            'goods_id'     => $params['goods_id'],
            'serial'       => $params['serial'],
        ])->one();

        $finalGoods = FinalGoods::find()->where([
            'order_id'     => $params['order_id'],
            'goods_id'     => $params['goods_id'],
            'key'          => $params['key'],
            'serial'       => $params['serial'],
        ])->one();

        if (!$finalGoods) {
            $finalGoods = new FinalGoods();
            $finalGoods->order_id     = $params['order_id'];
            $finalGoods->goods_id     = $params['goods_id'];
            $finalGoods->key          = $params['key'];
            $finalGoods->serial       = $params['serial'];
            $finalGoods->number       = $orderGoods ? $orderGoods->number : 0;
        }
        //更新最新为准
        $finalGoods->relevance_id = $params['select_id'];

        if ($finalGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $finalGoods->getErrors()]);
        }
    }

    /**保存成本单
     * @return false|string
     */
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $order = Order::findOne($params['order_id']);

        $orderFinal                 = new OrderFinal();
        $orderFinal->final_sn       = $params['final_sn'];
        $orderFinal->order_id       = $params['order_id'];
        if ($order) {
            $orderFinal->customer_id = $order->customer_id;
        }
        $orderFinal->goods_info     = json_encode($params['goods_ids']);
        if ($orderFinal->save()) {
            foreach ($params['goods_info'] as $key => $value) {
                $finalGoods = FinalGoods::find()->where([
                    'order_id'  => $params['order_id'],
                    'key'       => $params['key'],
                    'serial'    => $value['serial'],
                    'goods_id'  => $value['goods_id'],
                ])->one();
                if ($finalGoods) {
                    $finalGoods->order_final_id = $orderFinal->id;
                    $finalGoods->final_sn       = $orderFinal->final_sn;
                    $finalGoods->tax            = $value['tax'];
                    $finalGoods->price          = $value['price'];
                    $finalGoods->tax_price      = $value['tax_price'];
                    $finalGoods->all_price      = $value['all_price'];
                    $finalGoods->all_tax_price  = $value['all_tax_price'];
                    $finalGoods->delivery_time  = $value['delivery_time'];
                    $finalGoods->save();
                }
            }
            $order->is_final = Order::IS_FINAL_YES;
            $order->save();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderFinal->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $orderFinal    = OrderFinal::findOne($id);
        $order         = Order::findOne($orderFinal->order_id);
        $finalGoods    = FinalGoods::findAll(['order_final_id' => $id]);
        $inquiryGoods  = InquiryGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        $quoteGoods    = QuoteGoods::find()->where(['order_id' => $order->id, 'order_final_id' => $id])->indexBy('goods_id')->all();
        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();

        $date = date('ymd_');
        $orderI = OrderQuote::find()->where(['like', 'quote_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->quote_sn, '_');
            $str = substr($orderI->quote_sn, $num+1);
            $number = sprintf("%02d", $str+1);
        } else {
            $number = '01';
        }

        $purchaseGoods  = PurchaseGoods::find()
            ->where(['order_id' => $orderFinal->order_id])
            ->indexBy('goods_id')
            ->all();

        $data = [];
        $data['order']          = $order;
        $data['orderGoods']     = $orderGoods;
        $data['orderFinal']     = $orderFinal;
        $data['finalGoods']     = $finalGoods;
        $data['inquiryGoods']   = $inquiryGoods;
        $data['quoteGoods']     = $quoteGoods;
        $data['model']          = new OrderQuote();
        $data['number']         = $number;
        $data['purchaseGoods']  = $purchaseGoods;

        return $this->render('detail', $data);
    }

    /**成本单直接生成采购单的页面
     * @param $id
     * @return string
     */
    public function actionCreatePurchase($id)
    {
        $request = Yii::$app->request->get();
        $orderFinal      = OrderFinal::findOne($id);
        $order           = Order::findOne($orderFinal->order_id);
        $finalGoodsQuery = FinalGoods::find()
            ->from('final_goods fg')
            ->select('fg.*')->leftJoin('goods g', 'fg.goods_id=g.id')
            ->leftJoin('inquiry i', 'fg.relevance_id=i.id')
            ->where(['order_final_id' => $id]);
        if (isset($request['admin_id'])) {
            $finalGoodsQuery->andFilterWhere(['i.admin_id' => $request['admin_id']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $finalGoodsQuery->andWhere(['like', 'g.original_company', $request['original_company']]);
        }
        $finalGoodsQuery = $finalGoodsQuery->all();

        $inquiryGoods  = InquiryGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        $purchaseGoods  = PurchaseGoods::find()
            ->where(['order_id' => $orderFinal->order_id])
            ->indexBy('goods_id')
            ->all();

        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();

        $date = date('ymd_');
        $orderI = OrderPurchase::find()->where(['like', 'purchase_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->purchase_sn, '_');
            $str = substr($orderI->purchase_sn, $num + 1);
            $number = sprintf("%02d", $str + 1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['orderFinal']     = $orderFinal;
        $data['finalGoods']     = $finalGoodsQuery;
        $data['model']          = new OrderPurchase();
        $data['number']         = $number;
        $data['inquiryGoods']   = $inquiryGoods;
        $data['purchaseGoods']  = $purchaseGoods;
        $data['order']          =   $order;

        return $this->render('create-purchase', $data);
    }

    /**保存为采购单的动作(非项目订单直接生成采购单)
     * @return false|string
     */
    public function actionSavePurchase()
    {
        $params = Yii::$app->request->post();

        $orderFinal = OrderFinal::findOne($params['order_final_id']);
        $orderFinal->is_purchase = OrderFinal::IS_PURCHASE_YES;
        $orderFinal->save();

        $orderPurchase                     = new OrderPurchase();
        $orderPurchase->purchase_sn        = $params['purchase_sn'];
        $orderPurchase->order_id           = $orderFinal->order_id;
        $orderPurchase->order_agreement_id = 0;
        $orderPurchase->goods_info         = json_encode([], JSON_UNESCAPED_UNICODE);
        $orderPurchase->end_date           = $params['end_date'];
        $orderPurchase->admin_id           = $params['admin_id'];
        if ($orderPurchase->save()) {
            foreach ($params['goods_info'] as $item) {
                $finalGoods = FinalGoods::findOne($item['final_goods_id']);
                if ($finalGoods) {
                    $purchaseGoods = new PurchaseGoods();

                    $purchaseGoods->order_id            = $orderFinal->order_id;
                    $purchaseGoods->order_final_id      = $orderFinal->id;
                    $purchaseGoods->order_agreement_id  = 0;
                    $purchaseGoods->order_purchase_id   = $orderPurchase->primaryKey;
                    $purchaseGoods->order_purchase_sn   = $orderPurchase->purchase_sn;
                    $purchaseGoods->serial              = $finalGoods->serial;
                    $purchaseGoods->goods_id            = $finalGoods->goods_id;
                    $purchaseGoods->type                = $finalGoods->type;
                    $purchaseGoods->relevance_id        = $finalGoods->relevance_id;
                    $purchaseGoods->number              = $finalGoods->number;
                    $purchaseGoods->tax_rate            = $finalGoods->tax;
                    $purchaseGoods->price               = $finalGoods->price;
                    $purchaseGoods->tax_price           = $finalGoods->tax_price;
                    $purchaseGoods->all_price           = $item['number'] * $finalGoods->price;
                    $purchaseGoods->all_tax_price       = $item['number'] * $finalGoods->tax_price;
                    $purchaseGoods->fixed_price         = $finalGoods->price;
                    $purchaseGoods->fixed_tax_price     = $finalGoods->tax_price;
                    $purchaseGoods->fixed_number        = $item['number'];
                    $purchaseGoods->inquiry_admin_id    = $params['admin_id'];
                    $purchaseGoods->delivery_time       = $item['delivery_time'];

                    //$purchaseGoods->agreement_sn        = $orderAgreement->order_id;
                    $purchaseGoods->save();
                }
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    public function actionRelevancePurchase()
    {
        $params = Yii::$app->request->post();

        $inquiry = Inquiry::findOne($params['inquiry_id']);
        $finalGoods = FinalGoods::findOne($params['final_goods_id']);

        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();

        $finalGoods->price              = $inquiry->price;
        $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax/100), 2, '.', '');
        $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
        $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
        $finalGoods->delivery_time      = $inquiry->delivery_time;
        $finalGoods->relevance_id       = $inquiry->id;

        if ($finalGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        }
    }
}
