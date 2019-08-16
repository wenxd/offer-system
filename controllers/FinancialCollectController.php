<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\OrderAgreement;
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

    public function actionAddRemark()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->financial_remark = $params['remark'];

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    public function actionChangeAdvance()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->is_advancecharge = OrderPayment::IS_ADVANCECHARGE_YES;
        $orderPayment->advancecharge_at = date('Y-m-d H:i:s');
        if ($orderPayment->is_stock && $orderPayment->is_payment && $orderPayment->is_bill) {
            $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
        }
        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    public function actionChangePayment()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->is_payment = OrderPayment::IS_PAYMENT_YES;
        $orderPayment->payment_at = date('Y-m-d H:i:s');
        if ($orderPayment->is_stock && $orderPayment->is_advancecharge && $orderPayment->is_bill) {
            $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
        }

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }

    public function actionChangeBill()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['id']);
        $orderPayment->is_bill = OrderPayment::IS_BILL_YES;
        $orderPayment->bill_at = date('Y-m-d H:i:s');
        if ($orderPayment->is_stock && $orderPayment->is_payment && $orderPayment->is_advancecharge) {
            $orderPayment->is_complete = OrderPayment::IS_COMPLETE_YES;
        }

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }
}
