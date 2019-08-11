<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\OrderAgreement;
use Yii;
use app\models\OrderPurchase;
use app\models\OrderPurchaseSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\OrderFinal;
use app\models\PurchaseGoods;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class OrderPurchaseController extends BaseController
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

        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);

        $orderPurchase                     = new OrderPurchase();
        $orderPurchase->purchase_sn        = $params['purchase_sn'];
        $orderPurchase->order_id           = $orderAgreement->order_id;
        $orderPurchase->order_agreement_id = $params['order_agreement_id'];
        $orderPurchase->goods_info         = json_encode($params['goods_info'], JSON_UNESCAPED_UNICODE);
        $orderPurchase->end_date           = $params['end_date'];
        $orderPurchase->admin_id           = $params['admin_id'];
        if ($orderPurchase->save()) {
            foreach ($params['goods_info'] as $item) {
                $agreementGoods = AgreementGoods::findOne($item['agreement_goods_id']);
                if ($agreementGoods) {
                    $purchaseGoods = new PurchaseGoods();

                    $purchaseGoods->order_id            = $orderAgreement->order_id;
                    $purchaseGoods->order_agreement_id  = $orderAgreement->id;
                    $purchaseGoods->order_purchase_id   = $orderPurchase->primaryKey;
                    $purchaseGoods->order_purchase_sn   = $orderPurchase->purchase_sn;
                    $purchaseGoods->serial              = $agreementGoods->serial;
                    $purchaseGoods->goods_id            = $agreementGoods->goods_id;
                    $purchaseGoods->type                = $agreementGoods->type;
                    $purchaseGoods->relevance_id        = $agreementGoods->relevance_id;
                    $purchaseGoods->number              = $agreementGoods->number;
                    $purchaseGoods->tax_rate            = $agreementGoods->tax_rate;
                    $purchaseGoods->price               = $agreementGoods->price;
                    $purchaseGoods->tax_price           = $agreementGoods->tax_price;
                    $purchaseGoods->all_price           = $agreementGoods->all_price;
                    $purchaseGoods->all_tax_price       = $agreementGoods->all_tax_price;
                    $purchaseGoods->fixed_price         = $agreementGoods->price;
                    $purchaseGoods->fixed_number        = $agreementGoods->number;
                    $purchaseGoods->inquiry_admin_id    = $agreementGoods->inquiry_admin_id;

                    //$purchaseGoods->agreement_sn        = $orderAgreement->order_id;
                    $purchaseGoods->purchase_date       = $params['end_date'];
                    $purchaseGoods->save();
                }
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }

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
        $params = Yii::$app->request->post();

        $purchaseGoods = PurchaseGoods::findOne($params['id']);
        if (!$purchaseGoods) {
            return json_encode(['code' => 500, 'msg' => '不存在此条数据']);
        }

        $purchaseGoods->is_purchase   = PurchaseGoods::IS_PURCHASE_YES;
        $purchaseGoods->agreement_sn  = $params['this_agreement_sn'];
        $purchaseGoods->purchase_date = $params['this_delivery_date'];
        if ($purchaseGoods->save()){
            $purchaseComplete = PurchaseGoods::find()
                ->where(['order_purchase_id' => $purchaseGoods->order_purchase_id])
                ->andWhere('is_purchase = 0')->one();
            if (!$purchaseComplete) {
                $orderPurchase = OrderPurchase::findOne($purchaseGoods->order_purchase_id);
                $orderPurchase->is_purchase = OrderPurchase::IS_PURCHASE_YES;
                $orderPurchase->save();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $purchaseGoods->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function actionCompleteAll()
    {
        $params = Yii::$app->request->post();
        $orderPurchase = OrderPurchase::findOne($params['id']);
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
