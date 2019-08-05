<?php

namespace app\controllers;

use app\models\AgreementGoods;
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
        $orderQuote->quote_ratio    = $params['quote_ratio'];
        $orderQuote->delivery_ratio = $params['delivery_ratio'];
        if ($orderQuote->save()) {

            $orderFinal->is_quote = OrderFinal::IS_QUOTE_YES;
            $orderFinal->save();

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
                $row[] = $item['serial'];
                $row[] = $item['tax_rate'];
                $row[] = $item['price'];
                $row[] = $item['tax_price'];
                $row[] = $item['all_price'];
                $row[] = $item['all_tax_price'];
                $row[] = $item['quote_price'];
                $row[] = $item['quote_tax_price'];
                $row[] = $item['quote_all_price'];
                $row[] = $item['quote_all_tax_price'];
                $row[] = $item['delivery_time'];

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
            'type', 'relevance_id', 'number', 'serial', 'tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price',
             'quote_price', 'quote_tax_price', 'quote_all_price', 'quote_all_tax_price', 'delivery_time'];
        $num = Yii::$app->db->createCommand()->batchInsert(QuoteGoods::tableName(), $feild, $data)->execute();
    }

    //报价单详情
    public function actionDetail($id)
    {
        $orderQuote = OrderQuote::findOne($id);
        $quoteGoods = QuoteGoods::findAll(['order_quote_id' => $id]);

        $date = date('ymd_');
        $orderI = OrderAgreement::find()->where(['like', 'agreement_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->agreement_sn, '_');
            $str = substr($orderI->agreement_sn, $num+1);
            $number = sprintf("%02d", $str+1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['orderQuote'] = $orderQuote;
        $data['quoteGoods'] = $quoteGoods;
        $data['model']      = new OrderAgreement();
        $data['number']     = $number;

        return $this->render('detail', $data);
    }

    //完成报价
    public function actionComplete()
    {
        $params = Yii::$app->request->post();

        $quoteGoods = QuoteGoods::findOne($params['id']);
        $quoteGoods->is_quote = QuoteGoods::IS_QUOTE_YES;
        if ($quoteGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $quoteGoods->getErrors()]);
        }
    }

    //创建合同订单
    public function actionCreateAgreement()
    {
        $params = Yii::$app->request->post();

        //首先保存报价单
        $orderQuote = OrderQuote::findOne($params['id']);
        $orderQuote->is_quote = OrderQuote::IS_QUOTE_YES;
        $orderQuote->save();

        //创建合同单
        $orderAgreement = new OrderAgreement();
        $orderAgreement->agreement_sn    = $params['agreement_sn'];
        $orderAgreement->order_id        = $orderQuote->order_id;
        $orderAgreement->order_quote_id  = $orderQuote->id;
        $orderAgreement->order_quote_sn  = $orderQuote->quote_sn;

        $json = [];
        foreach ($params['goods_info'] as $goods) {
            $item = [];
            $item['goods_id']     = $goods['goods_id'];
            $item['number']       = $goods['number'];
            $item['price']        = $goods['price'];
            $item['is_agreement'] = 0;
            $json[] = $item;
        }

        $orderAgreement->goods_info      = json_encode($json, JSON_UNESCAPED_UNICODE);
        $orderAgreement->agreement_date  = $params['agreement_date'];
        $orderAgreement->admin_id        = $params['admin_id'];
        if ($orderAgreement->save()) {
            $data = [];
            foreach ($params['goods_info'] as $item) {
                $row = [];
                //批量数据
                $row[]  = $orderQuote->order_id;
                $row[]  = $orderAgreement->primaryKey;
                $row[]  = $orderAgreement->agreement_sn;
                $row[]  = $orderQuote->id;
                $row[]  = $orderQuote->quote_sn;
                $row[]  = $item['goods_id'];
                $row[]  = $item['type'];
                $row[]  = $item['relevance_id'];
                $row[]  = $item['price'];
                $row[]  = $item['tax_price'];
                $row[]  = $item['number'];
                $data[] = $row;
            }
            self::insertAgreementGoods($data);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderAgreement->getErrors()]);
        }
    }

    public static function insertAgreementGoods($data)
    {
        $feild = ['order_id', 'order_agreement_id', 'order_agreement_sn', 'order_quote_id', 'order_quote_sn', 'goods_id',
            'type', 'relevance_id', 'price', 'tax_price', 'number'];
        $num = Yii::$app->db->createCommand()->batchInsert(AgreementGoods::tableName(), $feild, $data)->execute();
    }
}
