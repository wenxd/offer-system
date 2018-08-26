<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/24
 * Time: 14:02
 */
namespace app\controllers;

use Yii;
use app\actions;
use app\models\Cart;
use app\models\Stock;
use app\models\Inquiry;
use app\models\OrderInquiry;
use app\models\OrderInquirySearch;
use app\models\OrderQuote;
use yii\helpers\ArrayHelper;

class OrderInquiryController extends BaseController
{

    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new OrderInquirySearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => OrderInquiry::className(),
            ],
        ];
    }

    public function actionSubmit()
    {
        $params = Yii::$app->request->get('OrderInquiry');
        $type   = Yii::$app->request->get('type');

        if ($type == 1) {
            $order = new OrderInquiry();
        } else {
            $order = new OrderQuote();
        }

        $order->order_id     = $params['order_id'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->quote_price  = $params['quote_price'];
        $order->remark       = $params['remark'];

        $cartList = Cart::find()->all();
        $ids_new    = [];
        $ids_better = [];
        $ids_stock  = [];
        foreach ($cartList as $key => $cart) {
            if ($cart->type == Cart::TYPE_NEW) {
                $row = [];
                $row['id']     = $cart->inquiry_id;
                $row['number'] = $cart->number;
                $ids_new[]     = $row;
            }
            if ($cart->type == Cart::TYPE_BETTER) {
                $row = [];
                $row['id']     = $cart->inquiry_id;
                $row['number'] = $cart->number;
                $ids_better[]  = $row;
            }
            if ($cart->type == Cart::TYPE_STOCK) {
                $row = [];
                $row['id']     = $cart->inquiry_id;
                $row['number'] = $cart->number;
                $ids_stock[]   = $row;
            }
        }
        $new = [
            'type' => Cart::TYPE_NEW,
            'list' => $ids_new,
        ];
        $better = [
            'type' => Cart::TYPE_BETTER,
            'list' => $ids_better,
        ];
        $stock = [
            'type' => Cart::TYPE_STOCK,
            'list' => $ids_stock,
        ];
        $json = [$new, $better, $stock];
        $order->inquirys = json_encode($json, JSON_UNESCAPED_UNICODE);
        if ($order->save()) {
            Cart::deleteAll();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $data = [];

        $model = OrderInquiry::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        $jsonList = json_decode($model->inquirys, true);

        foreach ($jsonList as $key => $value) {
            if ($value['type'] == '0') {
                $newList = $value['list'];
            }
            if ($value['type'] == '1') {
                $betterList = $value['list'];
            }
            if ($value['type'] == '2') {
                $stockList = $value['list'];
            }
        }
        //最新
        $newIds = ArrayHelper::getColumn($newList, 'id');
        $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])->andWhere(['in', 'id', $newIds])->asArray()->all();
        foreach ($inquiryNewQuery as $key => $inquiry) {
            foreach ($newList as $new) {
                if ($inquiry['id'] == $new['id']) {
                    $inquiryNewQuery[$key]['number'] = $new['number'];
                }
            }
        }

        //最优
        $betterIds = ArrayHelper::getColumn($betterList, 'id');
        $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES])->andWhere(['in', 'id', $betterIds])->asArray()->all();
        foreach ($inquiryBetterQuery as $key => $inquiry) {
            foreach ($betterList as $better) {
                if ($inquiry['id'] == $better['id']) {
                    $inquiryBetterQuery[$key]['number'] = $better['number'];
                }
            }
        }

        //库存记录
        $stockIds = ArrayHelper::getColumn($stockList, 'id');
        $stockQuery = Stock::find()->andWhere(['in', 'id', $stockIds])->asArray()->all();
        foreach ($stockQuery as $key => $inquiry) {
            foreach ($stockList as $stock) {
                if ($inquiry['id'] == $stock['id']) {
                    $stockQuery[$key]['number'] = $stock['number'];
                }
            }
        }

        $data['inquiryNewest'] = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;
        $data['stockList']     = $stockQuery;
        $data['model']         = $model;

        return $this->render('detail', $data);
    }
}