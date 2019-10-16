<?php

namespace app\controllers;

use Yii;
use app\models\{Order, OrderAgreement, OrderPurchase, InquiryGoods, AgreementGoods, PurchaseGoods};
use app\models\OrderAgreementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderAgreementController implements the CRUD actions for OrderAgreement model.
 */
class OrderAgreementController extends Controller
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
     * Lists all OrderAgreement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderAgreementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderAgreement model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $agreementGoods = AgreementGoods::find()->where(['order_agreement_id' => $id])->orderBy('serial')->all();

        $date = date('ymd_');
        $orderI = OrderAgreement::find()->where(['like', 'agreement_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->agreement_sn, '_');
            $str = substr($orderI->agreement_sn, $num+1);
            $number = sprintf("%02d", $str+1);
        } else {
            $number = '01';
        }

        return $this->render('view', [
            'model'          => $model,
            'agreementGoods' => $agreementGoods,
            'number'         => $number,
        ]);
    }

    /**
     * Creates a new OrderAgreement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderAgreement();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderAgreement model.
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
     * Deletes an existing OrderAgreement model.
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
     * Finds the OrderAgreement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderAgreement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderAgreement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request->get();
        $orderAgreement = OrderAgreement::findOne($id);
        $agreementGoodsQuery = AgreementGoods::find()->from('agreement_goods ag')
            ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
            ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0]);
        if (isset($request['admin_id'])) {
            $agreementGoodsQuery->andFilterWhere(['inquiry_admin_id' => $request['admin_id']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $agreementGoodsQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }

        $agreementGoods = $agreementGoodsQuery->orderBy('serial')->all();
        $inquiryGoods   = InquiryGoods::find()->where(['order_id' => $orderAgreement->order_id])->indexBy('goods_id')->all();
        $purchaseGoods  = PurchaseGoods::find()
            ->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $id])
            ->indexBy('goods_id')
            ->all();

        $date = date('ymd_');
        $orderI = OrderPurchase::find()->where(['like', 'purchase_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->purchase_sn, '_');
            $str = substr($orderI->purchase_sn, $num + 1);
            $number = sprintf("%02d", $str + 1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['orderAgreement'] = $orderAgreement;
        $data['agreementGoods'] = $agreementGoods;
        $data['model']          = new OrderAgreement();
        $data['number']         = $number;
        $data['inquiryGoods']   = $inquiryGoods;
        $data['purchaseGoods']  = $purchaseGoods;
        $data['order']          = Order::findOne($orderAgreement->order_id);

        return $this->render('detail', $data);
    }
}
