<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\AgreementGoodsData;
use app\models\OrderAgreement;
use app\models\OrderPurchase;
use app\models\PurchaseGoods;
use Yii;
use app\models\AgreementStock;
use app\models\AgreementStockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AgreementStockController implements the CRUD actions for AgreementStock model.
 */
class AgreementStockController extends Controller
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
     * Lists all AgreementStock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgreementStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgreementStock model.
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
     * Creates a new AgreementStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AgreementStock();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AgreementStock model.
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
     * Deletes an existing AgreementStock model.
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
     * Finds the AgreementStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgreementStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgreementStock::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionConfirm($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $agreementStock = AgreementStock::findOne($id);
        $stock = $agreementStock->stock;

        // 减去临时库存
        $temp_number = $stock->temp_number - $agreementStock->use_number;
        $agreementStock->temp_number = $temp_number;
        $agreementStock->stock_number = $stock->number;
        $stock->temp_number = $temp_number;
        if (!$stock->save()) {
            Yii::$app->getSession()->setFlash('error', $stock->getErrors());
            return "<script>history.go(-1);</script>";
        }
        $agreementStock->is_confirm = AgreementStock::IS_CONFIRM_YES;
        $agreementStock->confirm_at = date('Y-m-d H:i:s');
        $agreementStock->admin_id   = Yii::$app->user->identity->id;
        if (!$agreementStock->save()) {
            Yii::$app->getSession()->setFlash('error', $agreementStock->getErrors());
        }
        // 判断来源(采购策略)
        if ($agreementStock->source == AgreementStock::STRATEGY) {
            // 判断订单类型（项目订单）
            if (isset($agreementStock->order->order_type) && $agreementStock->order->order_type == 1) {
                // 恢复收入合同单号与零件ID对应表 采购数量
                $agreementGoods = AgreementGoodsData::find()
                    ->where(['order_id' => $agreementStock->order_id,
                        'order_agreement_id' => $agreementStock->order_agreement_id,
                        'order_agreement_sn' => $agreementStock->order_agreement_sn,
                        'goods_id' => $agreementStock->goods_id])
                    ->one();
                $agreementGoods->is_strategy_stock = 9;
                if (!$agreementGoods->save()) {
                    Yii::$app->getSession()->setFlash('error', $agreementGoods->errors);
                    return "<script>history.go(-1);</script>";
                }
            }
        } elseif ($agreementStock->source == AgreementStock::PURCHASE) {
            // 判断订单类型（项目订单）
            if (isset($agreementStock->order->order_type) && $agreementStock->order->order_type == 1) {
                // 恢复收入合同单号与零件ID对应表 采购数量
                $agreementGoods = AgreementGoods::find()
                    ->where(['order_id' => $agreementStock->order_id,
                        'order_agreement_id' => $agreementStock->order_agreement_id,
                        'order_agreement_sn' => $agreementStock->order_agreement_sn,
                        'goods_id' => $agreementStock->goods_id])
                    ->one();
                $agreementGoods->is_purchase_stock = 9;
                if (!$agreementGoods->save()) {
                    Yii::$app->getSession()->setFlash('error', $agreementGoods->errors);
                    return "<script>history.go(-1);</script>";
                }
            }
        } elseif ($agreementStock->source == AgreementStock::PAYMENT) {
            // 支出合同
            $PurchaseGoods = PurchaseGoods::find()
                ->where(['order_id' => $agreementStock->order_id,
                    'order_purchase_id' => $agreementStock->order_purchase_id,
                    'goods_id' => $agreementStock->goods_id])
                ->one();
            $PurchaseGoods->is_fixed_stock = 9;
            if (!$PurchaseGoods->save()) {
                Yii::$app->getSession()->setFlash('error', $PurchaseGoods->errors);
                return "<script>history.go(-1);</script>";
            }
        }
        $transaction->commit();
        return "<script>history.go(-1);</script>";
    }

    /**
     * 驳回
     * @param $id
     */
    public function actionReject($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $agreementStock = AgreementStock::findOne($id);
//        $agreementStock->is_confirm = AgreementStock::IS_CONFIRM_REJECT;
//        $agreementStock->confirm_at = date('Y-m-d H:i:s');
//        $agreementStock->admin_id   = Yii::$app->user->identity->id;
        // 驳回则删除
        if (!$agreementStock->delete()) {
            Yii::$app->getSession()->setFlash('error', $agreementStock->errors);
            return "<script>history.go(-1);</script>";

        }
        //如果查询同数据
        $count = AgreementStock::find()->where([
            'order_id' => $agreementStock->order_id,
            'order_agreement_id' => $agreementStock->order_agreement_id,
            'source' => $agreementStock->source
        ])->count();
        // 判断来源(采购策略)
        if ($agreementStock->source == AgreementStock::STRATEGY) {
            // 判断订单类型（项目订单）
            if (isset($agreementStock->order->order_type) && $agreementStock->order->order_type == 1) {
                // 恢复收入合同单号与零件ID对应表 采购数量
                $agreementGoods = AgreementGoodsData::find()
                    ->where(['order_id' => $agreementStock->order_id,
                        'order_agreement_id' => $agreementStock->order_agreement_id,
                        'order_agreement_sn' => $agreementStock->order_agreement_sn,
                        'goods_id' => $agreementStock->goods_id])
                    ->one();
                $agreementGoods->strategy_number = $agreementGoods->strategy_number + $agreementStock->use_number;
                $agreementGoods->strategy_stock_number = $agreementGoods->strategy_stock_number - $agreementStock->use_number;
                $agreementGoods->is_strategy_stock = 4;
                if (!$agreementGoods->save()) {
                    Yii::$app->getSession()->setFlash('error', $agreementGoods->errors);
                    return "<script>history.go(-1);</script>";
                }
                // 如所有被驳回，更新成未点击报错使用库存
                if (!$count) {
                    OrderAgreement::updateAll(['is_strategy_number' => 0], ['id' => $agreementStock->order_agreement_id]);
                }
            }
        } elseif ($agreementStock->source == AgreementStock::PURCHASE) {
            // 采购
            if (isset($agreementStock->order->order_type) && $agreementStock->order->order_type == 1) {
                // 恢复收入合同单号与零件ID对应表 采购数量
                $agreementGoods = AgreementGoods::find()
                    ->where(['order_id' => $agreementStock->order_id,
                        'order_agreement_id' => $agreementStock->order_agreement_id,
                        'order_agreement_sn' => $agreementStock->order_agreement_sn,
                        'goods_id' => $agreementStock->goods_id])
                    ->one();
                $agreementGoods->purchase_number = $agreementGoods->purchase_number + $agreementStock->use_number;
                $agreementGoods->purchase_stock_number = $agreementGoods->purchase_stock_number - $agreementStock->use_number;
                $agreementGoods->is_purchase_stock = 4;
                if (!$agreementGoods->save()) {
                    Yii::$app->getSession()->setFlash('error', $agreementGoods->errors);
                    return "<script>history.go(-1);</script>";
                }
                // 如所有被驳回，更新成未点击报错使用库存
                if (!$count) {
                    OrderAgreement::updateAll(['is_purchase_number' => 0], ['id' => $agreementStock->order_agreement_id]);
                }
            }
        } elseif ($agreementStock->source == AgreementStock::PAYMENT) {
            // 支出合同
            $PurchaseGoods = PurchaseGoods::find()
                ->where(['order_id' => $agreementStock->order_id,
                    'order_purchase_id' => $agreementStock->order_purchase_id,
                    'goods_id' => $agreementStock->goods_id])
                ->one();
            $PurchaseGoods->fixed_number = $PurchaseGoods->fixed_number + $agreementStock->use_number;
            $PurchaseGoods->fixed_stock_number = $PurchaseGoods->fixed_stock_number - $agreementStock->use_number;
            $PurchaseGoods->is_fixed_stock = 4;
            if (!$PurchaseGoods->save()) {
                Yii::$app->getSession()->setFlash('error', $PurchaseGoods->errors);
                return "<script>history.go(-1);</script>";
            }
            // 如所有被驳回，更新成未点击报错使用库存
            if (!$count) {
                OrderPurchase::updateAll(['is_purchase_number' => 0], ['id' => $agreementStock->order_purchase_id]);
            }
        }
        $transaction->commit();
        Yii::$app->getSession()->setFlash('success', '驳回成功');
        return "<script>history.go(-1);</script>";
    }
}
