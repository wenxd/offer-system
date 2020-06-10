<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Competitor;
use app\models\CompetitorSearch;

/**
 * CompetitorController implements the CRUD actions for Competitor model.
 */
class CompetitorController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new CompetitorSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Competitor::className(),
                'scenario'   => 'competitor',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Competitor::className(),
                'scenario'   => 'competitor',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Competitor::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Competitor::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Competitor::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Competitor::className(),
            ],
        ];
    }
}
