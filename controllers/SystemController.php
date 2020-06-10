<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\SystemConfig;
use app\models\SystemConfigSearch;

/**
 * SystemController implements the CRUD actions for SystemConfig model.
 */
class SystemController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new SystemConfigSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => SystemConfig::className(),
            ],
        ];
    }
}
