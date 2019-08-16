<?php

namespace app\controllers;

use app\models\OrderPayment;
use app\models\PaymentGoods;
use Yii;
use app\models\StockLog;
use app\models\OrderPurchase;
use app\models\OrderFinancialSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\OrderFinal;
use app\models\PurchaseGoods;

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
        $orderPayment->is_advancecharge = $orderPayment::IS_ADVANCECHARGE_YES;
        $orderPayment->advancecharge_at = date('Y-m-d H:i:s');

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
        $orderPayment->is_payment = $orderPayment::IS_PAYMENT_YES;
        $orderPayment->payment_at = date('Y-m-d H:i:s');

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

        if ($orderPayment->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPayment->getErrors()]);
        }
    }
}
