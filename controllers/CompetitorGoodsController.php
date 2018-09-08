<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\CompetitorGoods;
use app\models\CompetitorGoodsSearch;

/**
 * CompetitorGoodsController implements the CRUD actions for CompetitorGoods model.
 */
class CompetitorGoodsController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new CompetitorGoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => CompetitorGoods::className(),
                'scenario'   => 'competitor_goods',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => CompetitorGoods::className(),
                'scenario'   => 'competitor_goods',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
        ];
    }
}
