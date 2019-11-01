<?php

namespace app\controllers;

use app\models\Inquiry;
use Yii;
use app\models\AgreementGoods;
use app\models\AgreementGoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AgreementGoodsController implements the CRUD actions for AgreementGoods model.
 */
class AgreementGoodsController extends Controller
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
     * Lists all AgreementGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgreementGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgreementGoods model.
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
     * Creates a new AgreementGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AgreementGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AgreementGoods model.
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
     * Deletes an existing AgreementGoods model.
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
     * Finds the AgreementGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgreementGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgreementGoods::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionRelevance()
    {
        $params = Yii::$app->request->post();

        $inquiry = Inquiry::findOne($params['inquiry_id']);
        $agreementGoods = AgreementGoods::findOne($params['agreement_goods_id']);

        $agreementGoods->price              = $inquiry->price;
        $agreementGoods->tax_price          = $inquiry->tax_price;
        $agreementGoods->all_price          = $inquiry->all_price;
        $agreementGoods->all_tax_price      = $inquiry->all_tax_price;
        $agreementGoods->inquiry_admin_id   = $inquiry->admin_id;
        $agreementGoods->relevance_id       = $inquiry->id;

        if ($agreementGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        }
    }
}
