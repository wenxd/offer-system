<?php

namespace app\controllers;

use Yii;
use app\models\{
    OrderAgreement, OrderQuote, OrderFinal, QuoteGoods
};
use app\models\OrderQuoteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderQuoteController implements the CRUD actions for OrderQuote model.
 */
class OrderQuoteController extends Controller
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

    /**
     * Creates a new OrderQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderQuote model.
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
     * Deletes an existing OrderQuote model.
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
     * Finds the OrderQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //生成报价单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderFinal = OrderFinal::findOne($params['order_final_id']);

        $orderQuote                 = new OrderQuote();
        $orderQuote->quote_sn       = $params['quote_sn'];
        $orderQuote->order_id       = $orderFinal->order_id;
        $orderQuote->order_final_id = $params['order_final_id'];
        $orderQuote->goods_info     = json_encode($params['goods_info']);
        $orderQuote->admin_id       = $params['admin_id'];
        if ($orderQuote->save()) {
            $data = [];
            foreach ($params['goods_info'] as $item) {
                $row = [];

                $row[] = $orderFinal->order_id;
                $row[] = $params['order_final_id'];
                $row[] = $orderFinal->final_sn;
                $row[] = $orderQuote->primaryKey;
                $row[] = $orderQuote->quote_sn;
                $row[] = $item['goods_id'];
                $row[] = $item['type'];
                $row[] = $item['relevance_id'];
                $row[] = $item['number'];

                $data[] = $row;
            }
            self::insertQuoteGoods($data);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderQuote->getErrors()]);
        }
    }

    //批量插入
    public static function insertQuoteGoods($data)
    {
        $feild = ['order_id', 'order_final_id', 'order_final_sn', 'order_quote_id', 'order_quote_sn', 'goods_id',
            'type', 'relevance_id','number'];
        $num = Yii::$app->db->createCommand()->batchInsert(QuoteGoods::tableName(), $feild, $data)->execute();
    }

    //报价单详情
    public function actionDetail($id)
    {
        $orderQuote = OrderQuote::findOne($id);
        $quoteGoods = QuoteGoods::findAll(['order_quote_id' => $id]);

        $data = [];
        $data['orderQuote'] = $orderQuote;
        $data['quoteGoods'] = $quoteGoods;
        $data['model']      = new OrderAgreement();

        return $this->render('detail', $data);
    }
}
