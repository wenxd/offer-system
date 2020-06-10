<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Brand;
use app\models\BrandSearch;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class BrandController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new BrandSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Brand::className(),
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Brand::className(),
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Brand::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Brand::className(),
            ],
        ];
    }
}
