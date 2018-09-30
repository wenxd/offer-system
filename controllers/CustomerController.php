<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Customer;
use app\models\CustomerSearch;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new CustomerSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Customer::className(),
                'scenario'   => 'customer',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Customer::className(),
                'scenario'   => 'customer',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Customer::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Customer::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Customer::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Customer::className(),
            ],
        ];
    }

    public function actionInfo($id)
    {
        $customer = Customer::find()->where(['id' => $id])->asArray()->one();
        if ($customer) {
            return json_encode(['code' => 200, 'data' => $customer]);
        } else {
            return json_encode(['code' => 500, 'msg' => '']);
        }
    }
}
