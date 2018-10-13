<?php

namespace app\controllers;

use app\models\FinalGoods;
use app\models\Goods;
use app\models\Inquiry;
use app\models\OrderFinal;
use app\models\OrderInquiry;
use app\models\OrderPurchase;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use app\models\OrderFinalQuoteSearch;
use app\models\OrderPurchaseSearch;
use app\models\Cart;
use app\models\QuoteRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BaseController
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

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
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
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSubmit()
    {
        $params = Yii::$app->request->get();
        $ids    = Yii::$app->request->get('ids');

        $order = new Order();
        $order->customer_id  = $params['customer_id'];
        $order->admin_id     = $params['admin_id'];
        $order->order_sn     = $params['order_sn'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->order_price  = $params['order_price'];
        $order->remark       = $params['remark'];
        $order->type         = $params['type'];
        $order->status       = Order::STATUS_NO;
        if ($order->save()) {
            $cartList = Cart::find()->where(['id' => $ids])->all();
            $data = [];
            foreach ($cartList as $key => $cart) {
                $row = [];

                $row[] = $cart->type;
                $row[] = $cart->inquiry_id;
                $row[] = $cart->goods_id;
                $row[] = $cart->quotation_price;
                $row[] = $cart->number;
                $row[] = $order->primaryKey;
                $row[] = $params['type'];
                $row[] = $params['remark'];
                $row[] = $params['provide_date'];

                $data[] = $row;
            }
            $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'remark', 'offer_date'];
            $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
            if ($num) {
                Cart::deleteAll(['id' => $ids]);
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $data = [];

        $order = Order::findOne($id);
        if (!$order){
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }

        $orderInquiry = OrderInquiry::findAll(['order_id' => $id]);
        $orderFinal = OrderFinal::findAll(['order_id' => $id]);
        $orderPurchase = OrderPurchase::findAll(['order_id' => $id]);

        $data['model']         = $order;
        $data['orderInquiry']  = $orderInquiry;
        $data['orderFinal']    = $orderFinal;
        $data['orderPurchase'] = $orderPurchase;

        return $this->render('detail', $data);
    }

    //生成最终报价单
    public function actionFinalQuote()
    {
        $ids = Yii::$app->request->post('ids');

        $orderRecord = QuoteRecord::findAll(['id' => $ids]);

        $oldOrder = Order::findOne($orderRecord['0']->order_id);
        $order = new Order();
        $order->customer_id   = $oldOrder->customer_id ;
        $order->order_sn      = $oldOrder->order_sn    ;
        $order->description   = $oldOrder->description ;
        $order->order_price   = $oldOrder->order_price ;
        $order->remark        = $oldOrder->remark      ;
        $order->type          = Order::TYPE_FINAL      ;
        $order->status        = $oldOrder->status      ;
        $order->provide_date  = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type       ;
            $row[] = $value->inquiry_id ;
            $row[] = $value->goods_id   ;
            $row[] = $value->quote_price;
            $row[] = $value->number     ;
            $row[] = $order->id;
            $row[] = Order::TYPE_FINAL;
            $row[] = $value->status     ;
            $row[] = $value->remark     ;
            $row[] = $value->offer_date ;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num   = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '保存失败']);
        }
    }

    //最终询价单列表
    public function actionQuoteList()
    {
        $searchModel = new OrderFinalQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('final-quote-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //最终询价单详情
    public function actionFinalQuoteDetail($id)
    {
        $data = [];

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list']  = $list;

        return $this->render('final-quote-detail', $data);
    }

    //保存为采购单
    public function actionSubmitPurchase()
    {
        $ids      = Yii::$app->request->post('ids');
        $admin_id = Yii::$app->request->post('admin_id');

        $orderRecord = QuoteRecord::findAll(['id' => $ids]);

        $oldOrder = Order::findOne($orderRecord['0']->order_id);
        $order = new Order();
        $order->customer_id   = $oldOrder->customer_id ;
        $order->order_sn      = $oldOrder->order_sn    ;
        $order->admin_id      = $admin_id;
        $order->description   = $oldOrder->description ;
        $order->order_price   = $oldOrder->order_price ;
        $order->remark        = $oldOrder->remark      ;
        $order->type          = Order::TYPE_PURCHASE   ;
        $order->status        = $oldOrder->status      ;
        $order->provide_date  = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type       ;
            $row[] = $value->inquiry_id ;
            $row[] = $value->goods_id   ;
            $row[] = $value->quote_price;
            $row[] = $value->number     ;
            $row[] = $order->id;
            $row[] = Order::TYPE_PURCHASE;
            $row[] = $value->status     ;
            $row[] = $value->remark     ;
            $row[] = $value->offer_date ;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num   = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '保存失败']);
        }
    }

    //最采购单列表
    public function actionPurchaseList()
    {
        $searchModel = new OrderPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('purchase-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //最终询价单详情
    public function actionPurchaseDetail($id)
    {
        $data = [];

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list']  = $list;

        return $this->render('purchase-detail', $data);
    }

    //创建订单第二步 添加零件页面
    public function actionGenerate()
    {
        $params = Yii::$app->request->get();

        return $this->render('add-goods', $params);
    }

    //创建订单  添加动作
    public function actionAddGoods()
    {
        $goods_id = (string)Yii::$app->request->post('goods_id');
        $goods = Goods::find()->where(['goods_number' => $goods_id])->asArray()->one();
        if ($goods) {
            return json_encode(['code' => 200, 'data' => $goods]);
        } else {
            return json_encode(['code' => 500, 'msg' => '没有此零件']);
        }
    }

    //保存订单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->get();
        $goodsIds = Yii::$app->request->post();

        $order = new Order();
        $order->customer_id = $params['customer_id'];
        $order->order_sn = $params['order_sn'];
        $order->manage_name = $params['manage_name'];
        $order->goods_ids = json_encode($goodsIds['goodsIds']);
        $order->order_type = $params['order_type'];
        $order->provide_date = $params['provide_date'];
        $order->created_at = $params['created_at'];

        if ($order->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    public function actionCreateInquiry($id)
    {
        $data      = [];
        $order     = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }
        $goods_ids     = json_decode($order->goods_ids, true);
        $goods         = Goods::find()->where(['id' => $goods_ids])->all();

        $orderInquiry         = OrderInquiry::find()->where(['order_id' => $order->id])->all();

        $data['orderInquiry'] = $orderInquiry;
        $data['goods']        = $goods;
        $data['model']        = new OrderInquiry();
        $data['order']        = $order;
        return $this->render('create-inquiry', $data);
    }

    public function actionCreateFinal($id, $key = 0)
    {
        $data      = [];
        $order     = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }
        $goods_ids     = json_decode($order->goods_ids, true);
        $goods         = Goods::find()->where(['id' => $goods_ids])->all();
        $finalGoods    = FinalGoods::find()->where(['order_id' => $id, 'key' => $key])->indexBy('goods_id')->all();

        $data['goods']        = $goods;
        $data['order']        = $order;
        $data['finalGoods']   = $finalGoods;

        return $this->render('create-final', $data);
    }
}
