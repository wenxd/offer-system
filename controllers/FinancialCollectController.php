<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\OrderAgreement;
use app\models\OrderAgreementSearch;
use Yii;
use app\models\OrderPayment;
use app\models\PaymentGoods;
use app\models\StockLog;
use app\models\OrderFinancialCollectSearch;

class FinancialCollectController extends BaseController
{
    /**收款合同列表
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderFinancialCollectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**收款合同详情
     * @param $id
     * @return string
     */
    public function actionDetail($id)
    {
        $orderAgreement = OrderAgreement::findOne($id);
        $agreementGoods = AgreementGoods::findAll(['order_agreement_id' => $id]);
        $stockLog = StockLog::find()->where([
            'order_id'           => $orderAgreement->order_id,
            'order_agreement_id' => $id,
            'type'               => StockLog::TYPE_OUT
        ])->all();

        $data = [];
        $data['orderAgreement'] = $data['model'] = $orderAgreement;
        $data['agreementGoods'] = $agreementGoods;
        $data['stockLog']       = $stockLog;

        return $this->render('detail', $data);
    }

    /**保存备注
     * @return false|string
     */
    public function actionAddRemark()
    {
        $params = Yii::$app->request->post();

        $orderAgreement = OrderAgreement::findOne($params['id']);
        $orderAgreement->financial_remark = $params['remark'];

        if ($orderAgreement->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    /**预收款
     * @return false|string
     */
    public function actionChangeAdvance()
    {
        $params = Yii::$app->request->post();

        $payment_ratio_price = $params['price'] * 100;

        $orderAgreement = OrderAgreement::findOne($params['id']);
        $orderAgreement->payment_ratio      = number_format($payment_ratio_price / $orderAgreement->payment_price, 2, '.', '');
        $orderAgreement->remain_price       = $orderAgreement->payment_price - $params['price'];
        $orderAgreement->is_advancecharge   = $orderAgreement::IS_ADVANCECHARGE_YES;
        $orderAgreement->advancecharge_at   = date('Y-m-d H:i:s');
        $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderAgreement->is_stock && $orderAgreement->is_payment && $orderAgreement->is_bill) {
            $orderAgreement->is_complete    = $orderAgreement::IS_COMPLETE_YES;
            $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        }
        if ($orderAgreement->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderAgreement->getErrors()]);
        }
    }

    /**收全款
     * @return false|string
     */
    public function actionChangePayment()
    {
        $params = Yii::$app->request->post();

        $orderAgreement = OrderAgreement::findOne($params['id']);
        $orderAgreement->is_payment = OrderPayment::IS_PAYMENT_YES;
        $orderAgreement->payment_at = date('Y-m-d H:i:s');
        if (!$orderAgreement->is_advancecharge) {
            $orderAgreement->advancecharge_at = date('Y-m-d H:i:s');
        }
        $orderAgreement->is_advancecharge   = OrderPayment::IS_ADVANCECHARGE_YES;
        $orderAgreement->remain_price       = 0;
        $orderAgreement->payment_ratio      = 100;
        $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderAgreement->is_stock && $orderAgreement->is_advancecharge && $orderAgreement->is_bill) {
            $orderAgreement->is_complete    = $orderAgreement::IS_COMPLETE_YES;
            $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        }
        $orderAgreement->remain_price = 0;
        if ($orderAgreement->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderAgreement->getErrors()]);
        }
    }

    /**开发票
     * @return false|string
     */
    public function actionChangeBill()
    {
        $params = Yii::$app->request->post();

        $orderAgreement = OrderAgreement::findOne($params['id']);
        $orderAgreement->is_bill            = OrderPayment::IS_BILL_YES;
        $orderAgreement->bill_at            = date('Y-m-d H:i:s');
        $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        if ($orderAgreement->is_stock && $orderAgreement->is_payment && $orderAgreement->is_advancecharge) {
            $orderAgreement->is_complete    = $orderAgreement::IS_COMPLETE_YES;
            $orderAgreement->financial_admin_id = Yii::$app->user->identity->id;
        }

        if ($orderAgreement->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderAgreement->getErrors()]);
        }
    }

    /**
     * 待收款汇总
     */
    public function actionList()
    {
        $searchModel = new OrderFinancialCollectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('agreement-list', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
