<?php

namespace app\controllers;

use Yii;
use app\models\{AgreementGoodsBak,
    Inquiry,
    Order,
    OrderAgreement,
    OrderPurchase,
    InquiryGoods,
    AgreementGoods,
    PurchaseGoods,
    Stock,
    SystemConfig};
use app\models\OrderAgreementSearch;
use yii\helpers\ArrayHelper;
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
            ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1]);
        if (isset($request['admin_id'])) {
            $agreementGoodsQuery->andFilterWhere(['inquiry_admin_id' => $request['admin_id']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $agreementGoodsQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }

        $agreementGoods = $agreementGoodsQuery->orderBy('serial')->all();
        $inquiryGoods   = InquiryGoods::find()->where(['order_id' => $orderAgreement->order_id])->indexBy('goods_id')->all();
        $purchaseGoods  = PurchaseGoods::find()->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $id])->asArray()->all();
        $purchaseGoods  = ArrayHelper::index($purchaseGoods, null, 'goods_id');

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

    /**
     * 一键走最低
     */
    public function actionLow($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('price asc')->one();
            if ($inquiry) {
                $agreementGoods->price              = $inquiry->price;
                $agreementGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price          = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price      = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id   = $inquiry->admin_id;
                $agreementGoods->relevance_id       = $inquiry->id;
                $agreementGoods->delivery_time      = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键最短
     */
    public function actionShort($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('delivery_time asc')->one();
            if ($inquiry) {
                $agreementGoods->price              = $inquiry->price;
                $agreementGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price          = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price      = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id   = $inquiry->admin_id;
                $agreementGoods->relevance_id       = $inquiry->id;
                $agreementGoods->delivery_time      = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键优选
     */
    public function actionBetter($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where([
                'good_id'           => $agreementGoods->goods_id,
                'is_better'         => Inquiry::IS_BETTER_YES,
                'is_confirm_better' => 1
            ])->one();
            if ($inquiry) {
                $agreementGoods->price              = $inquiry->price;
                $agreementGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price          = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price      = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id   = $inquiry->admin_id;
                $agreementGoods->relevance_id       = $inquiry->id;
                $agreementGoods->delivery_time      = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键优选
     */
    public function actionNew($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('created_at Desc')->one();
            if ($inquiry) {
                $agreementGoods->price              = $inquiry->price;
                $agreementGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price          = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price      = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id   = $inquiry->admin_id;
                $agreementGoods->relevance_id       = $inquiry->id;
                $agreementGoods->delivery_time      = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键走库存
     */
    public function actionStock($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $stock = Stock::find()->where(['good_id' => $agreementGoods->goods_id])->one();
            if ($stock) {
                $agreementGoods->purchase_number = $agreementGoods->number > $stock->number ? $agreementGoods->number - $stock->number : 0;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键恢复
     */
    public function actionRecover($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();

        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $agreementGoodsBak = AgreementGoodsBak::find()->where(['order_agreement_id' => $id, 'agreement_goods_id' => $agreementGoods->id])->one();
            if ($agreementGoodsBak) {
                $agreementGoods->tax_rate         = $agreementGoodsBak->tax_rate;
                $agreementGoods->price            = $agreementGoodsBak->price;
                $agreementGoods->tax_price        = $agreementGoodsBak->tax_price;
                $agreementGoods->all_price        = $agreementGoodsBak->all_price;
                $agreementGoods->all_tax_price    = $agreementGoodsBak->all_tax_price;
                $agreementGoods->purchase_number  = $agreementGoodsBak->purchase_number;
                $agreementGoods->delivery_time    = $agreementGoodsBak->delivery_time;
                $agreementGoods->purchase_is_show = AgreementGoods::IS_SHOW_YES;
                $agreementGoods->order_number     = $agreementGoods->number;
                $agreementGoods->save();
            }
        }

        $orderAgreement = OrderAgreement::findOne($id);
        $orderAgreement->is_merge = OrderAgreement::IS_MERGE_NO;
        $orderAgreement->save();

        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }

    /**一键合并
     * @param $id
     * @return \yii\web\Response
     */
    public function actionMerge($id)
    {
        $orderAgreement = OrderAgreement::findOne($id);
        $agreementGoodsList = AgreementGoods::find()->where([
            'order_agreement_id' => $id,
            'is_deleted'         => 0,
            'purchase_is_show'   => AgreementGoods::IS_SHOW_YES,
        ])->all();

        $goods_ids     = [];
        $more_goods_id = [];
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            if (in_array($agreementGoods->goods_id, $goods_ids)) {
                $more_goods_id[] = $agreementGoods->goods_id;
            } else {
                $goods_ids[] = $agreementGoods->goods_id;
            }
        }

        foreach ($more_goods_id as $goods_id) {
            $agreementGoods = AgreementGoods::find()->where([
                'order_agreement_id' => $id,
                'is_deleted'         => 0,
                'goods_id'           => $goods_id,
                'purchase_is_show'   => AgreementGoods::IS_SHOW_YES,
            ])->one();

            $purchase_number = $agreementGoods->purchase_number;
            $order_number    = $agreementGoods->order_number;
            $agreementGoods->purchase_is_show = AgreementGoods::IS_SHOW_NO;
            $agreementGoods->save();

            $lastAgreementGoods = AgreementGoods::find()->where([
                'order_agreement_id' => $id,
                'is_deleted'         => 0,
                'goods_id'           => $goods_id,
                'purchase_is_show'   => AgreementGoods::IS_SHOW_YES,
            ])->one();
            $lastAgreementGoods->purchase_number += $purchase_number;
            $lastAgreementGoods->order_number    += $order_number;
            $lastAgreementGoods->save();
        }
        $orderAgreement->is_merge = OrderAgreement::IS_MERGE_YES;
        $orderAgreement->save();
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }
}
