<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Goods;
use app\models\GoodsSearch;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new GoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Goods::className(),
                'scenario'   => 'goods',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Goods::className(),
                'scenario'   => 'goods',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Goods::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Goods::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Goods::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Goods::className(),
            ],
        ];
    }

    /**获取商品编号
     * @return string
     */
    public function actionGetNumber()
    {
        $goods_number = Yii::$app->request->get('goods_number');

        $goods = Goods::findOne(['goods_number' => $goods_number]);

        if ($goods) {
            return json_encode(['code' => 200, 'data' => $goods->id]);
        } else {
            return json_encode(['code' => 500, 'msg' => '没有数据']);
        }
    }


}
