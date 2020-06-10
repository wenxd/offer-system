<?php

namespace app\controllers;

use app\models\PaymentGoods;
use app\models\PurchaseGoods;
use Yii;
use app\models\TempPaymentGoods;
use app\models\TempPaymentGoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TempPaymentGoodsController implements the CRUD actions for TempPaymentGoods model.
 */
class TempPaymentGoodsController extends Controller
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
     * Lists all TempPaymentGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempPaymentGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempPaymentGoods model.
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
     * Creates a new TempPaymentGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempPaymentGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TempPaymentGoods model.
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
     * Deletes an existing TempPaymentGoods model.
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
     * Finds the TempPaymentGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempPaymentGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempPaymentGoods::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionTemp($id)
    {
        TempPaymentGoods::deleteAll();
        $purchaseGoods = PurchaseGoods::findOne($id);
        $goods_id = $purchaseGoods->goods_id;

        $keys = ['id', 'order_id', 'order_payment_id', 'order_payment_sn', 'order_purchase_id', 'order_purchase_sn',
            'purchase_goods_id', 'serial', 'goods_id', 'type', 'relevance_id', 'number', 'tax_rate', 'price', 'tax_price',
            'all_price', 'all_tax_price', 'fixed_price', 'fixed_tax_price', 'fixed_all_price', 'fixed_all_tax_price',
            'fixed_number', 'inquiry_admin_id', 'updated_at', 'created_at', 'is_quality', 'supplier_id', 'delivery_time',
            'before_supplier_id', 'before_delivery_time', 'is_payment'];

        $paymentGoodsList = PaymentGoods::find()->where(['goods_id' => $goods_id])->asArray()->all();

        $res = Yii::$app->db->createCommand()->batchInsert(TempPaymentGoods::tableName(), $keys, $paymentGoodsList)->execute();

        return $this->redirect(['index']);
    }
}
