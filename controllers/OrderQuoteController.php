<?php

namespace app\controllers;

use app\models\QuoteRecord;
use Yii;
use app\models\Stock;
use app\models\OrderInquiry;
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
        $list = QuoteRecord::findAll(['order_quote_id' => $id, 'order_type' => QuoteRecord::TYPE_QUOTE]);

        $model->loadDefaultValues();
        $data['model']     = $model;
        $data['quoteList'] = $list;

        return $this->render('detail', $data);
    }
}
