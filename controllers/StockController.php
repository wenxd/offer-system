<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Stock;
use app\models\StockSearch;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new StockSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Stock::className(),
                'scenario'   => 'stock',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Stock::className(),
                'scenario'   => 'stock',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Stock::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Stock::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Stock::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Stock::className(),
            ],
        ];
    }

    public function actionAddress()
    {
        $params = Yii::$app->request->post();
        $num = Stock::updateAll(['position' => $params['address']], ['id' => $params['list']]);
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '失败']);
        }
    }
}
