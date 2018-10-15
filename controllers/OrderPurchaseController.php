<?php

namespace app\controllers;

use app\models\OrderFinal;
use app\models\PurchaseGoods;
use Yii;
use app\models\OrderPurchase;
use app\models\OrderPurchaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class OrderPurchaseController extends Controller
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
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderPurchase model.
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
     * Creates a new OrderPurchase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderPurchase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderPurchase model.
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
     * Deletes an existing OrderPurchase model.
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
     * Finds the OrderPurchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderPurchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderPurchase::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderFinal = OrderFinal::findOne($params['order_final_id']);

        $orderPurchase                 = new OrderPurchase();
        $orderPurchase->purchase_sn    = 'CGD' . date('YmdHis') . rand(10, 99);
        $orderPurchase->order_id       = $orderFinal->order_id;
        $orderPurchase->order_final_id = $params['order_final_id'];
        $orderPurchase->goods_info     = json_encode($params['goods_info']);
        $orderPurchase->end_date       = $params['end_date'];
        $orderPurchase->admin_id       = $params['admin_id'];
        if ($orderPurchase->save()) {
            $data = [];
            foreach ($params['goods_info'] as $item) {
                $row = [];

                $row[] = $orderFinal->order_id;
                $row[] = $params['order_final_id'];
                $row[] = $orderPurchase->primaryKey;
                $row[] = $orderPurchase->purchase_sn;
                $row[] = $item['goods_id'];
                $row[] = $item['type'];
                $row[] = $item['relevance_id'];
                $row[] = $item['number'];

                $data[] = $row;
            }
            self::insertPurcharseGoods($data);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }

    }

    //批量插入
    public static function insertPurcharseGoods($data)
    {
        $feild = ['order_id', 'order_final_id', 'order_purchase_id', 'order_purchase_sn', 'goods_id', 'type', 'relevance_id','number'];
        $num = Yii::$app->db->createCommand()->batchInsert(PurchaseGoods::tableName(), $feild, $data)->execute();
    }

    public function actionDetail($id)
    {
        $orderPurchase = OrderPurchase::findOne($id);
        $purchaseGoods = PurchaseGoods::findAll(['order_purchase_id' => $id]);

        $data = [];
        $data['orderPurchase'] = $data['model'] = $orderPurchase;
        $data['purchaseGoods'] = $purchaseGoods;

        return $this->render('detail', $data);
    }

    public function actionComplete()
    {
        $id = Yii::$app->request->post('id');

        $purchaseGoods = PurchaseGoods::findOne($id);
        if (!$purchaseGoods) {
            return json_encode(['code' => 500, 'msg' => '不存在此条数据']);
        }

        $purchaseGoods->is_purchase = PurchaseGoods::IS_PURCHASE_YES;
        if ($purchaseGoods->save()){
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $purchaseGoods->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function actionCompleteAll()
    {
        $params = Yii::$app->request->post();
        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->agreement_sn   = $params['agreement_sn'];
        $orderPurchase->agreement_date = $params['agreement_date'];
        $orderPurchase->agreement_time = date('Y-m-d H:i:s');
        $orderPurchase->is_purchase    = OrderPurchase::IS_PURCHASE_YES;
        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()], JSON_UNESCAPED_UNICODE);
        }

    }
}
