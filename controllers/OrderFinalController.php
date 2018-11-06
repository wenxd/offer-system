<?php

namespace app\controllers;

use Yii;
use app\models\OrderFinal;
use app\models\OrderFinalSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\FinalGoods;
use app\models\InquiryGoods;
use app\models\Order;
use app\models\OrderGoods;
use app\models\OrderFinalQuoteSearch;
use app\models\OrderPurchase;
use app\models\PurchaseGoods;

/**
 * OrderFinalController implements the CRUD actions for OrderFinal model.
 */
class OrderFinalController extends BaseController
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
     * Lists all OrderFinal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderFinalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderFinal model.
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
     * Creates a new OrderFinal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderFinal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderFinal model.
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
     * Deletes an existing OrderFinal model.
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
     * Finds the OrderFinal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderFinal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderFinal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //生成关联数据
    public function actionRelevance()
    {
        $params = Yii::$app->request->post();

        $finalGoods = FinalGoods::find()->where([
            'order_id'     => $params['order_id'],
            'goods_id'     => $params['goods_id'],
            'key'          => $params['key']
        ])->one();

        if (!$finalGoods) {
            $finalGoods = new FinalGoods();
            $finalGoods->order_id     = $params['order_id'];
            $finalGoods->goods_id     = $params['goods_id'];
            $finalGoods->key          = $params['key'];
        }
        //更新最新为准
        $finalGoods->type         = $params['type'];
        $finalGoods->relevance_id = $params['select_id'];

        if ($finalGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $finalGoods->getErrors()]);
        }
    }

    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderFinal                 = new OrderFinal();
        $orderFinal->final_sn       = $params['final_sn'];
        $orderFinal->order_id       = $params['order_id'];
        $orderFinal->goods_info     = json_encode($params['goods_ids']);
        $orderFinal->agreement_date = $params['agreement_date'];
        if ($orderFinal->save()) {
            $res = FinalGoods::updateAll(['order_final_id' => $orderFinal->primaryKey, 'final_sn' => $orderFinal->final_sn],
                ['order_id' => $params['order_id'], 'key' => $params['key']]);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderFinal->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $orderFinal   = OrderFinal::findOne($id);
        $order        = Order::findOne($orderFinal->order_id);
        $finalGoods   = FinalGoods::findAll(['order_final_id' => $id]);
        $inquiryGoods = InquiryGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        $purchaseGoods = PurchaseGoods::find()->where(['order_id' => $order->id, 'order_final_id' => $id])->indexBy('goods_id')->all();
        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        
        $data = [];
        $data['order']         = $order;
        $data['orderGoods']    = $orderGoods;
        $data['orderFinal']    = $orderFinal;
        $data['finalGoods']    = $finalGoods;
        $data['inquiryGoods']  = $inquiryGoods;
        $data['purchaseGoods'] = $purchaseGoods;
        $data['model']         = new OrderPurchase();

        return $this->render('detail', $data);
    }
}
