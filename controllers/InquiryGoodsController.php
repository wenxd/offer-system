<?php

namespace app\controllers;

use app\assets\Common;
use app\models\InquiryGoodsClarifySearch;
use Yii;
use app\models\InquiryGoods;
use app\models\InquiryGoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InquiryGoodsController implements the CRUD actions for InquiryGoods model.
 */
class InquiryGoodsController extends Controller
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
     * Lists all InquiryGoods models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        return $this->redirect(['clarify']);
//        $searchModel = new InquiryGoodsSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Lists all InquiryGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        // 更新澄清记录
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            $InquiryGoodsClarify = InquiryGoodsClarifySearch::findOne($params['id']);
            $InquiryGoodsClarify->clarify = $params['reason'];
            // todo 2021-02-23 添加澄清回复系统通知
            $msg = "询价单【{$InquiryGoodsClarify->inquiry_sn}】有新的澄清回复";
            Common::SendSystemMsg($InquiryGoodsClarify->admin_id, $msg);
            if ($InquiryGoodsClarify->save()) {
                return json_encode(['code' => 200, 'msg' => '成功']);
            } else {
                return json_encode(['code' => 500, 'msg' => $InquiryGoodsClarify->getErrors()]);
            }
        }
        $searchModel = new InquiryGoodsClarifySearch();
        $download = Yii::$app->request->get('download', false);
        $params = Yii::$app->request->queryParams;
        if ($download) {
            $params = json_decode(Yii::$app->session['clarify_sql'], true);
            $params['download'] = true;
        } else {
            Yii::$app->session['clarify_sql'] = json_encode($params);
        }
        $dataProvider = $searchModel->search($params);
        return $this->render('clarify', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InquiryGoods model.
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
     * Creates a new InquiryGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InquiryGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InquiryGoods model.
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
     * Deletes an existing InquiryGoods model.
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
     * Finds the InquiryGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InquiryGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InquiryGoods::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
