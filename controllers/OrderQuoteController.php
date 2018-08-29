<?php

namespace app\controllers;

use Yii;
use app\models\Stock;
use app\models\Inquiry;
use app\models\OrderQuote;
use app\models\OrderQuoteSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderQuoteController implements the CRUD actions for OrderQuote model.
 */
class OrderQuoteController extends BaseController
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
     * Lists all OrderQuote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderQuote model.
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

    public function actionDetail($id)
    {
        $data = [];

        $model = OrderQuote::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        $jsonList = json_decode($model->inquirys, true);

        foreach ($jsonList as $key => $value) {
            if ($value['type'] == '0') {
                $newList = $value['list'];
            }
            if ($value['type'] == '1') {
                $betterList = $value['list'];
            }
            if ($value['type'] == '2') {
                $stockList = $value['list'];
            }
        }
        //最新
        $newIds = ArrayHelper::getColumn($newList, 'id');
        $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])->andWhere(['in', 'id', $newIds])->asArray()->all();
        foreach ($inquiryNewQuery as $key => $inquiry) {
            foreach ($newList as $new) {
                if ($inquiry['id'] == $new['id']) {
                    $inquiryNewQuery[$key]['number'] = $new['number'];
                }
            }
        }

        //最优
        $betterIds = ArrayHelper::getColumn($betterList, 'id');
        $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES])->andWhere(['in', 'id', $betterIds])->asArray()->all();
        foreach ($inquiryBetterQuery as $key => $inquiry) {
            foreach ($betterList as $better) {
                if ($inquiry['id'] == $better['id']) {
                    $inquiryBetterQuery[$key]['number'] = $better['number'];
                }
            }
        }

        //库存记录
        $stockIds = ArrayHelper::getColumn($stockList, 'id');
        $stockQuery = Stock::find()->andWhere(['in', 'id', $stockIds])->asArray()->all();
        foreach ($stockQuery as $key => $inquiry) {
            foreach ($stockList as $stock) {
                if ($inquiry['id'] == $stock['id']) {
                    $stockQuery[$key]['number'] = $stock['number'];
                }
            }
        }

        $data['inquiryNewest'] = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;
        $data['stockList']     = $stockQuery;
        $data['model']         = $model;

        return $this->render('detail', $data);
    }
}
