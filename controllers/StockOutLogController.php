<?php

namespace app\controllers;

use app\models\Stock;
use app\models\SystemConfig;
use Yii;
use app\models\StockLog;
use app\models\StockOutLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockOutLogController implements the CRUD actions for StockLog model.
 */
class StockOutLogController extends Controller
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
     * Lists all StockLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockOutLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StockLog model.
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
     * Creates a new StockLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StockLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StockLog model.
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
     * Deletes an existing StockLog model.
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
     * Finds the StockLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**手动添加出库记录
     * @return false|string
     */
    public function actionAdd()
    {
        $params = Yii::$app->request->post();
        $stock = Stock::findOne(['good_id' => $params['goods_id']]);
        if (!$stock) {
            return json_encode(['code' => 500, 'msg' => '没有此库存记录，不能出库']);
        }
        if ($params['number'] > $stock->number) {
            return json_encode(['code' => 500, 'msg' => '库存数量不够，剩下' . $stock->number]);
        }
        $stockLog = new StockLog();
        $stockLog->goods_id     = $params['goods_id'];
        $stockLog->number       = $params['number'];
        $stockLog->type         = StockLog::TYPE_OUT;
        $stockLog->remark       = $params['remark'];
        $stockLog->operate_time = date('Y-m-d H:i:s');
        if ($stockLog->save()) {
            $stock->number -= $params['number'];
            $stock->save();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()]);
        }
    }
}
