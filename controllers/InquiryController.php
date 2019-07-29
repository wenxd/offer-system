<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\{Goods, Stock, Inquiry, PurchaseGoods, InquirySearch};

/**
 * InquiryController implements the CRUD actions for Inquiry model.
 */
class InquiryController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new InquirySearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Inquiry::className(),
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Inquiry();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Inquiry::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSearch($goods_id)
    {
        $goods = Goods::findOne($goods_id);
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

        $data = [];
        $data['goods']         = $goods ? $goods : [];
        $data['inquiryPrice']  = $inquiryPriceQuery;
        $data['inquiryTime']   = $inquiryTimeQuery;
        $data['inquiryNew']    = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;
        $data['stock']         = $stockQuery;

        $data['purchasePrice']    = $purchasePrice;
        $data['purchaseDay']      = $purchaseDay;
        $data['purchaseNew']      = $purchaseNew;

        return $this->render('search-result', $data);
    }
}
