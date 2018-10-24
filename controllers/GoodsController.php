<?php

namespace app\controllers;

use app\models\InquiryGoods;
use app\models\OrderGoods;
use app\models\OrderInquiry;
use app\models\PurchaseGoods;
use app\models\StockLog;
use Yii;
use app\actions;
use app\models\Stock;
use app\models\Goods;
use app\models\GoodsSearch;
use app\models\Inquiry;
use app\models\CompetitorGoods;
use yii\helpers\ArrayHelper;

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
        $good_number = (string)Yii::$app->request->get('good_number');
        $goods = Goods::find()->where(['goods_number' => $good_number])->one();
        if (!$goods) {
            yii::$app->getSession()->setFlash('error', '没有此零件');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $goods_id = $goods->id;

        //价格最优
        $inquiryPriceQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('price asc')->one();
        //同期最短
        $inquiryTimeQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('delivery_time asc')->one();
        //最新报价
        $inquiryNewQuery = Inquiry::find()->where(['good_id' => $goods_id, 'is_newest' => Inquiry::IS_NEWEST_YES])->orderBy('updated_at Desc')->one();
        //优选记录
        $inquiryBetterQuery = Inquiry::find()->where(['good_id' => $goods_id, 'is_better' => Inquiry::IS_BETTER_YES])->orderBy('updated_at Desc')->one();

        //库存记录
        $stockQuery = Stock::find()->andWhere(['good_id' => $goods_id])->orderBy('updated_at Desc')->one();

        //采购记录
        $purchaseInquiry = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id, 'type' => PurchaseGoods::TYPE_INQUIRY])->all();
        $price = 100000000;
        $offerDay = 10000000;
        $purchasePrice = '';
        $purchaseDay = '';
        foreach ($purchaseInquiry as $item) {
            if ($item->inquiry->price < $price) {
                $price = $item->inquiry->price;
                $purchasePrice = $item;
            }
            if ($item->inquiry->delivery_time < $offerDay) {
                $offerDay = $item->inquiry->delivery_time;
                $purchaseDay = $item;
            }
        }
        $purchaseStock = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id, 'type' => PurchaseGoods::TYPE_STOCK])->all();
        foreach ($purchaseStock as $item) {
            if ($item->stock->price < $price) {
                $price = $item->stock->price;
                $purchasePrice = $item;
            }
        }

        //最新采购
        $purchaseNew = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id])->orderBy('created_at Desc')->one();

        //竞争对手
        $competitorGoods = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('updated_at Desc')->one();


        //最后三条入库的

        $stockLog = StockLog::find()->where(['type' => StockLog::TYPE_IN, 'goods_id' => $goods_id])
            ->orderBy('operate_time Desc')->limit(3)->all();
        $order_ids = ArrayHelper::getColumn($stockLog, 'order_id');
        $order_purchase_ids = ArrayHelper::getColumn($stockLog, 'order_purchase_id');

        $purchaseGoods = PurchaseGoods::find()->where(['order_id' => $order_ids, 'order_purchase_id' => $order_purchase_ids, 'goods_id' => $goods_id])->all();

        $inquiry_ids = [];
        $stock_ids   = [];
        foreach ($purchaseGoods as $key => $item) {
            if ($item->type) {
                $stock_ids[] = $item->relevance_id;
            } else {
                $inquiry_ids[] = $item->relevance_id;
            }
        }

        $average = Inquiry::find()->where(['id' => $inquiry_ids])->average('price');

        $data = [];
        $data['goods']            = $goods ? $goods : [];
        $data['inquiryPrice']     = $inquiryPriceQuery;
        $data['inquiryTime']      = $inquiryTimeQuery;
        $data['inquiryNew']       = $inquiryNewQuery;
        $data['inquiryBetter']    = $inquiryBetterQuery;
        $data['stock']            = $stockQuery;

        $data['purchasePrice']    = $purchasePrice;
        $data['purchaseDay']      = $purchaseDay;
        $data['purchaseNew']      = $purchaseNew;

        $data['competitorGoods']  = $competitorGoods;
        $data['competitorGoods']  = $competitorGoods;

        $data['average']          = $average;

        return $this->render('search-result', $data);
    }

    public function actionGetInfo()
    {
        $goods_id = Yii::$app->request->get('goods_id');

        $goods        = Goods::findOne($goods_id);
        $orderGoods   = OrderGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->asArray()->one();
        $orderInquiry = OrderInquiry::find()->where(['order_id' => $orderGoods['order_id']])->orderBy('created_at Desc')->asArray()->one();

        $data                 = [];
        $data['goods']        = $goods->toArray();
        $data['orderGoods']   = $orderGoods;
        $data['orderInquiry'] = $orderInquiry;

        return json_encode(['code' => 200, 'data' => $data]);
    }
}
