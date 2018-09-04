<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/24
 * Time: 14:02
 */
namespace app\controllers;

use app\models\QuoteRecord;
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

        $orderType = 1;
        if ($type == 1) {
            $order = new OrderQuote();
        } else {
            $order = new OrderInquiry();
            $orderType = 2;
        }

        $order->customer_id  = $params['customer_id'];
        $order->order_id     = $params['order_id'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->quote_price  = $params['quote_price'];
        $order->remark       = $params['remark'];

        $order->record_ids = json_encode([], JSON_UNESCAPED_UNICODE);
        if ($order->save()) {
            $cartList = Cart::find()->all();
            $data = [];
            foreach ($cartList as $key => $cart) {
                $row = [];

                $row[] = $cart->type;
                $row[] = $cart->inquiry_id;
                $row[] = $cart->goods_id;
                $row[] = $cart->quotation_price;
                $row[] = $cart->number;
                $row[] = $order->primaryKey;
                $row[] = $orderType;

                $data[] = $row;
            }
            $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_quote_id', 'order_type'];
            $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
            if ($num) {
                Cart::deleteAll();
            }
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