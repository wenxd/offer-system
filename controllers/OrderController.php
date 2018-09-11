<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\OrderSearch;
use app\models\Cart;
use app\models\QuoteRecord;
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
        $order->order_id     = $params['order_id'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->order_price  = $params['quote_price'];
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
            $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_quote_id', 'order_type', 'remark', 'offer_date'];
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

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        $list = QuoteRecord::findAll(['order_quote_id' => $id, 'order_type' => QuoteRecord::TYPE_QUOTE]);

        $model->loadDefaultValues();
        $data['model']     = $model;
        $data['quoteList'] = $list;

        return $this->render('detail', $data);
    }
}
