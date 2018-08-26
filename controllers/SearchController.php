<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/22
 * Time: 14:03
 */
namespace app\controllers;


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
    public function actionGetGoodId()
    {
        $good_id = (string)Yii::$app->request->get('good_id');

        $good_id_list = [];
        $inquiryList = Inquiry::find()->filterWhere(['like', 'good_id', $good_id])->all();
        if ($inquiryList) {
            $inquiry_goodIds = ArrayHelper::getColumn($inquiryList, 'good_id');
            $good_id_list = array_merge($good_id_list, $inquiry_goodIds);
        }

        $stockList = Stock::find()->filterWhere(['like', 'good_id', $good_id])->all();
        if ($stockList) {
            $stock_goodIds = ArrayHelper::getColumn($stockList, 'good_id');
            $good_id_list = array_merge($good_id_list, $stock_goodIds);
        }

        $good_id_list = array_unique($good_id_list);

        return json_encode(['code' => 200, 'data' => $good_id_list]);
    }

    /*
     * 搜索结果
     */
    public function actionSearch()
    {
        $data = [];
        $good_id = (string)Yii::$app->request->get('good_id');

        //最新
        $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])
            ->andWhere(['like', 'good_id', $good_id]);
        //最优
        $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES])
            ->andWhere(['like', 'good_id', $good_id]);
        //库存记录
        $stockQuery = Stock::find()->andWhere(['like', 'good_id', $good_id]);

        $newCount    = $inquiryNewQuery->count();
        $betterCount = $inquiryBetterQuery->count();
        $stockCount  = $stockQuery->count();

        $count = $newCount > $betterCount ? ($newCount > $stockCount ? $newCount : $stockCount) : $betterCount;

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 20]);

        $data['inquiryNewest'] = $inquiryNewQuery->offset($pages->offset)->limit($pages->limit)->all();
        $data['inquiryBetter'] = $inquiryBetterQuery->offset($pages->offset)->limit($pages->limit)->all();
        $data['stockList']     = $stockQuery->offset($pages->offset)->limit($pages->limit)->all();
        $data['pages']         = $pages;

        return $this->render('search', $data);
    }



    public function actionGet()
    {
        $session = Yii::$app->session;
        $name = $session->get('name');

        echo $name;
    }
}