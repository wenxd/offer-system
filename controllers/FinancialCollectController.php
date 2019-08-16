<?php

namespace app\controllers;

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
}
