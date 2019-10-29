<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Supplier;
use app\models\SupplierSearch;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new SupplierSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Supplier::className(),
                'scenario'   => 'supplier',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Supplier::className(),
            ],
        ];
    }

    public function actionDetail()
    {
        $id = Yii::$app->request->get('id');
        $supplier = Supplier::find()->where(['id' => $id])->asArray()->one();

        return json_encode(['code' => 200, 'data' => $supplier]);
    }

    public function actionCreate()
    {
        $model = new Supplier();
        $model->scenario = 'supplier';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
