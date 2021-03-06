<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\SystemNotice;
use Yii;
use app\models\OrderPayment;
use app\models\PaymentGoods;
use app\models\OrderPaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderPaymentController implements the CRUD actions for OrderPayment model.
 */
class OrderPaymentController extends Controller
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
     * Lists all OrderPayment models.
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

    /**
     * Lists all OrderPayment models.
     * @return mixed
     */
    public function actionIndex2()
    {
        $searchModel = new OrderPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 0);

        return $this->render('index2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderPayment model.
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
     * Creates a new OrderPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderPayment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderPayment model.
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
     * Deletes an existing OrderPayment model.
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
     * Finds the OrderPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderPayment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDetail($id)
    {
        $orderPayment = OrderPayment::findOne($id);

        $paymentGoods = PaymentGoods::find()->where(['order_payment_id' => $id])->orderBy('serial')->all();

        return $this->render('detail', [
            'model'        => $orderPayment,
            'orderPayment' => $orderPayment,
            'paymentGoods' => $paymentGoods,
        ]);
    }

    /**
     * 给付款财务发通知
     */
    public function actionNotice($id)
    {
        $orderPayment = OrderPayment::findOne($id);
        $orderPayment->is_notice = 1;
        $orderPayment->save();

        //发通知
        $financialAdmin = AuthAssignment::find()->where(['item_name' => '付款财务'])->all();
        foreach ($financialAdmin as $key => $value) {
            $systemNotice = new SystemNotice();
            $systemNotice->admin_id  = $value->user_id;
            $systemNotice->content   = $orderPayment->payment_sn . '支出合同已到货，需要付全款';
            $systemNotice->notice_at = date('Y-m-d H:i:s');
            $systemNotice->save();
        }

        return $this->redirect(['index']);
    }

    /**
     * 报销操作
     */
    public function actionReim()
    {
        $post = Yii::$app->request->post();
        $orderPayment = OrderPayment::findOne($post['order_id']);
        if (empty($orderPayment)) {
            return json_encode(['code' => 404, 'msg' => '数据未找到']);
        }
        if ($orderPayment->is_reim == 1) {
            return json_encode(['code' => 500, 'msg' => '已报销成功']);
        }
        $payment_price = $post['payment_price'];
        $data = [
            'reim_price' => $post['reim_price'],
            'reim_ratio' => $post['reim_ratio'],
            'reim_time' => time(),
        ];
        $reim_info = [];
        if (!empty($orderPayment->reim_info)) {
            $reim_info = json_decode($orderPayment->reim_info, true);
        }
        $reim_info[] = $data;
        foreach ($reim_info as $item) {
            $payment_price = bcsub($payment_price, $item['reim_price'], 2);
        }
        if ($payment_price <= 0) {
            $orderPayment->is_reim = 1;
            $orderPayment->reim_date = date('Y-m-d H:i:s');
        }
        $orderPayment->reim_info = json_encode($reim_info, JSON_UNESCAPED_UNICODE);
        if (!$orderPayment->save()) {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        } else {
            return json_encode(['code' => 200, 'msg' => '报销成功']);
        }
    }
}
