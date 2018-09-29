<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\Goods;
use app\models\GoodsSearch;
use app\models\Inquiry;

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

    public function actionManage()
    {
        $data = [];
        return $this->render('manage', $data);
    }

    public function actionSearchResult()
    {
        $data = [];
        $good_number = (string)Yii::$app->request->get('good_number');

        $goods = Goods::find()->where(['goods_number' => $good_number])->one();

        $data['inquiryNewest'] = [];
        $data['inquiryBetter'] = [];
        $data['stockList']     = [];
        $data['goods']         = $goods ? $goods : [];
        if ($goods) {
            //价格最优
            $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES, 'good_id' => $goods->id])
                ->orderBy('updated_at Desc')->one();
//            $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])
//                ->andWhere(['good_id' => $goods->id]);


//            //库存记录
//            $stockQuery = Stock::find()->andWhere(['good_id' => $goods->id]);
//            $newCount    = $inquiryNewQuery->count();
//            $betterCount = $inquiryBetterQuery->count();
//            $stockCount  = $stockQuery->count();
//            $count = $newCount > $betterCount ? ($newCount > $stockCount ? $newCount : $stockCount) : $betterCount;
//
//            $pages = new Pagination(['totalCount' => $count, 'pageSize' => 10]);
//
//            $data['inquiryNewest'] = $inquiryNewQuery->offset($pages->offset)->limit($pages->limit)->all();
            $data['inquiryBetter'] = $inquiryBetterQuery;
//            $data['stockList']     = $stockQuery->offset($pages->offset)->limit($pages->limit)->all();
//            $data['pages']         = $pages;
        }

        return $this->render('search-result', $data);
    }
}
