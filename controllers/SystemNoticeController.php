<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\SystemNotice;
use app\models\SystemNoticeSearch;
use app\controllers\BaseController;

/**
 * SystemNoticeController implements the CRUD actions for SystemNotice model.
 */
class SystemNoticeController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new SystemNoticeSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
        ];
    }
}
