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
}
