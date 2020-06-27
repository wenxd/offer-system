<?php

namespace app\controllers;

use Yii;
use app\models\OrderPayment;
use app\models\PaymentGoods;
use app\models\PaymentSearch;
use app\models\StockLog;
use app\models\OrderFinancialSearch;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class FinancialController extends BaseController
{
    /**
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderFinancialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $orderPayment = OrderPayment::findOne($id);
        $paymentGoods = PaymentGoods::findAll(['order_payment_id' => $id]);
        $stockLog = StockLog::find()->where([
            'order_id'          => $orderPayment->order_id,
            'order_payment_id'  => $id,
            'type'              => StockLog::TYPE_IN
        ])->all();

        $data = [];
        $data['orderPayment'] = $data['model'] = $orderPayment;
        $data['paymentGoods'] = $paymentGoods;
        $data['stockLog']     = $stockLog;

        return $this->render('detail', $data);
    }

    /**添加备注
     * @return false|string
     */
    public function actionAddRemark()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->financial_remark   = $params['remark'];
        $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    /**改变预付款
     * @return false|string
     */
    public function actionChangeAdvance()
    {
        $params = Yii::$app->request->post();

        $payment_ratio_price = $params['payment_ratio'] * 100;

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->payment_ratio      = number_format($payment_ratio_price / $orderPayment->payment_price, 2, '.', '');
        $orderPayment->remain_price       = $orderPayment->payment_price - $params['payment_ratio'];
        $orderPayment->is_advancecharge   = OrderPayment::IS_ADVANCECHARGE_YES;
        $orderPayment->advancecharge_at   = date('Y-m-d H:i:s');
        $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderPayment->is_stock && $orderPayment->is_payment && $orderPayment->is_bill) {
            $orderPayment->is_complete    = OrderPayment::IS_COMPLETE_YES;
            $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        }
        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    /**付全款
     * @return false|string
     */
    public function actionChangePayment()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->is_payment       = OrderPayment::IS_PAYMENT_YES;
        $orderPayment->payment_at       = date('Y-m-d H:i:s');
        if (!$orderPayment->is_advancecharge) {
            $orderPayment->advancecharge_at = date('Y-m-d H:i:s');
        }
        $orderPayment->is_advancecharge   = OrderPayment::IS_ADVANCECHARGE_YES;
        $orderPayment->remain_price       = 0;
        $orderPayment->payment_ratio      = 100;
        $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderPayment->is_stock && $orderPayment->is_advancecharge && $orderPayment->is_bill) {
            $orderPayment->is_complete        = OrderPayment::IS_COMPLETE_YES;
            $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        }

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    /**收到发票
     * @return false|string
     */
    public function actionChangeBill()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->is_bill            = OrderPayment::IS_BILL_YES;
        $orderPayment->bill_at            = date('Y-m-d H:i:s');
        $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderPayment->is_stock && $orderPayment->is_payment && $orderPayment->is_advancecharge) {
            $orderPayment->is_complete    = OrderPayment::IS_COMPLETE_YES;
            $orderPayment->financial_admin_id = Yii::$app->user->identity->id;
        }

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    /**
     * 待付款汇总
     */
    public function actionPaymentList()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('payment-list', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 修改时间
     */
    public function actionEditTime($id)
    {
        $model = OrderPayment::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['financial/detail', 'id' => $id]);
        }

        return $this->render('edit-time', [
            'model' => $model,
        ]);
    }
}
