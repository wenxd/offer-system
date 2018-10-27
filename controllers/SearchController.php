<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/22
 * Time: 14:03
 */
namespace app\controllers;


use app\models\Goods;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use app\models\Inquiry;
use app\models\Stock;

/**报价查询
 * Class SearchController
 * @package backend\controllers
 */
class SearchController extends BaseController
{
    /**查询页
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**获取零件ID
     * @return string
     */
    public function actionGetGoodNumber()
    {
        $good_number = (string)Yii::$app->request->get('good_number');

        $goodsList = Goods::find()->filterWhere(['like', 'goods_number', $good_number])->all();
        $good_number_list = ArrayHelper::getColumn($goodsList, 'goods_number');

        return json_encode(['code' => 200, 'data' => $good_number_list]);
    }

    /**获取零件号B
     * @return string
     */
    public function actionGetGoodNumberB()
    {
        $good_number_b = (string)Yii::$app->request->get('good_number_b');

        $goodsList = Goods::find()->filterWhere(['like', 'goods_number_b', $good_number_b])->all();
        $good_number_list = ArrayHelper::getColumn($goodsList, 'goods_number_b');

        return json_encode(['code' => 200, 'data' => $good_number_list]);
    }

    /*
     * 搜索结果
     */
    public function actionSearch()
    {
        $data = [];
        $good_number = (string)Yii::$app->request->get('good_number');

        $goods = Goods::find()->where(['goods_number' => $good_number])->one();

        $data['inquiryNewest'] = [];
        $data['inquiryBetter'] = [];
        $data['stockList']     = [];
        $data['pages']         = $pages = new Pagination(['totalCount' => 0, 'pageSize' => 10]);
        $data['goods']         = $goods ? $goods : [];
        if ($goods) {
            //最新
            $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])
                ->andWhere(['good_id' => $goods->id]);
            //最优
            $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES])
                ->andWhere(['good_id' => $goods->id]);
            //库存记录
            $stockQuery = Stock::find()->andWhere(['good_id' => $goods->id]);
            $newCount    = $inquiryNewQuery->count();
            $betterCount = $inquiryBetterQuery->count();
            $stockCount  = $stockQuery->count();
            $count = $newCount > $betterCount ? ($newCount > $stockCount ? $newCount : $stockCount) : $betterCount;

            $pages = new Pagination(['totalCount' => $count, 'pageSize' => 10]);

            $data['inquiryNewest'] = $inquiryNewQuery->offset($pages->offset)->limit($pages->limit)->all();
            $data['inquiryBetter'] = $inquiryBetterQuery->offset($pages->offset)->limit($pages->limit)->all();
            $data['stockList']     = $stockQuery->offset($pages->offset)->limit($pages->limit)->all();
            $data['pages']         = $pages;
        }

        return $this->render('search', $data);
    }



    public function actionGet()
    {
        $session = Yii::$app->session;
        $name = $session->get('name');

        echo $name;
    }
}