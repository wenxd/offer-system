<?php

namespace app\controllers;

use app\models\Inquiry;
use app\models\InquiryGoods;
use Yii;
use app\models\InquiryTemp;
use app\models\InquiryTempSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InquiryTempController implements the CRUD actions for InquiryTemp model.
 */
class InquiryTempController extends Controller
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
     * Lists all InquiryTemp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InquiryTempSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InquiryTemp model.
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
     * Creates a new InquiryTemp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InquiryTemp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InquiryTemp model.
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
     * Deletes an existing InquiryTemp model.
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
     * Finds the InquiryTemp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InquiryTemp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InquiryTemp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionInquiry($id)
    {
        InquiryTemp::deleteAll();
        $inquiry = InquiryGoods::findOne($id);
        $goods_id = $inquiry->goods_id;

        $keys = ['id', 'good_id', 'supplier_id', 'price', 'tax_price', 'tax_rate', 'all_tax_price', 'all_price', 'number', 'inquiry_datetime', 'sort', 'is_better', 'is_newest', 'is_priority', 'is_deleted', 'offer_date', 'remark', 'better_reason', 'delivery_time', 'admin_id', 'order_id', 'order_inquiry_id', 'inquiry_goods_id', 'updated_at', 'created_at', 'is_upload'];

        $inquiryList = Inquiry::find()->where(['good_id' => $goods_id])->asArray()->all();

        $res = Yii::$app->db->createCommand()->batchInsert(InquiryTemp::tableName(), $keys, $inquiryList)->execute();

        return $this->redirect(['index']);
    }
}
