<?php

namespace app\controllers;

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
        $orderPurchase = OrderPurchase::findOne($id);
        $purchaseGoods = PurchaseGoods::findAll(['order_purchase_id' => $id]);
        $stockLog = StockLog::find()->where([
            'order_id' => $orderPurchase->order_id,
            'order_purchase_id' => $id,
            'type' => StockLog::TYPE_IN
        ])->all();

        $data = [];
        $data['orderPurchase'] = $data['model'] = $orderPurchase;
        $data['purchaseGoods'] = $purchaseGoods;
        $data['stockLog']      = $stockLog;

        return $this->render('detail', $data);
    }

    public function actionAddRemark()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->financial_remark = $params['remark'];

        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    public function actionChangeAdvance()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->is_advancecharge = OrderPurchase::IS_ADVANCECHARGE_YES;

        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    public function actionChangePayment()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->is_payment = OrderPurchase::IS_PAYMENT_YES;

        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    public function actionChangeBill()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->is_bill = OrderPurchase::IS_BILL_YES;

        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }
}
