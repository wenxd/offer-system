<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\InquiryBetter;
use app\models\InquiryBetterSearch;

/**
 * InquiryController implements the CRUD actions for Inquiry model.
 */
class InquiryBetterController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new InquiryBetterSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => InquiryBetter::className(),
                'scenario'   => 'inquiry',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => InquiryBetter::className(),
                'scenario'   => 'inquiry',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => InquiryBetter::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => InquiryBetter::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => InquiryBetter::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => InquiryBetter::className(),
            ],
        ];
    }
}
