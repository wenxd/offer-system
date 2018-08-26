<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/23
 * Time: 19:58
 */
namespace app\controllers;

use Yii;
use app\models\Cart;
use app\models\Stock;
use app\models\Inquiry;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use app\models\OrderInquiry;

/*
 * 购物车（询价单确认页）
 */
class CartController extends BaseController
{
    public function actionAddList()
    {
        $inquiryId = Yii::$app->request->post('inquiryId');
        $number    = Yii::$app->request->post('number');
        $type      = Yii::$app->request->post('type');

        if ($inquiryId) {
            if ($type == 2) {
                $stock = Stock::findOne($inquiryId);
                if ($stock->number < $number){
                    return json_encode(['code' => 500, 'msg' => '本地库存不足']);
                }
            }
            $inquiryCart = Cart::find()->where(['inquiry_id' => $inquiryId, 'type' => $type])->one();
            if (!$inquiryCart) {
                $inquiryCart = new Cart();
                $inquiryCart->inquiry_id = $inquiryId;
            }
            $inquiryCart->type           = $type;
            $inquiryCart->number         = $number;
            if ($inquiryCart->save()) {
                return json_encode(['code' => 200, 'data' => '']);
            } else {
                return json_encode(['code' => 500, 'msg' => $inquiryCart->getErrors()]);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => '缺少ID']);
        }
    }

    public function actionList()
    {
        $data = [];

        //最新
        $newList = Cart::findAll(['type' => Cart::TYPE_NEW]);
        $newIds  = ArrayHelper::getColumn($newList, 'inquiry_id');
        $inquiryNewQuery = Inquiry::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'inquiry.id = c.inquiry_id')
            ->where(['is_newest' => Inquiry::IS_NEWEST_YES, 'c.type' => Cart::TYPE_NEW])->andWhere(['in', 'inquiry.id', $newIds]);

        //最优
        $betterList = Cart::findAll(['type' => Cart::TYPE_BETTER]);
        $betterIds  = ArrayHelper::getColumn($betterList, 'inquiry_id');
        $inquiryBetterQuery = Inquiry::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'inquiry.id = c.inquiry_id')
            ->where(['is_better' => Inquiry::IS_BETTER_YES, 'c.type' => Cart::TYPE_BETTER])->andWhere(['in', 'inquiry.id', $betterIds]);

        //库存记录
        $stockList  = Cart::findAll(['type' => Cart::TYPE_STOCK]);
        $stockIds   = ArrayHelper::getColumn($stockList, 'inquiry_id');
        $stockQuery = Stock::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'stock.id = c.inquiry_id')
            ->where(['c.type' => Cart::TYPE_STOCK])->andWhere(['in', 'stock.id', $stockIds]);

        $newCount    = $inquiryNewQuery->count();
        $betterCount = $inquiryBetterQuery->count();
        $stockCount  = $stockQuery->count();

        $count = $newCount > $betterCount ? ($newCount > $stockCount ? $newCount : $stockCount) : $betterCount;

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 1000000]);

        $data['inquiryNewest'] = $inquiryNewQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['inquiryBetter'] = $inquiryBetterQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['stockList']     = $stockQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['pages']         = $pages;

        $model = new OrderInquiry();
        $model->loadDefaultValues();
        $data['model']         = $model;

        return $this->render('list', $data);
    }

    public function actionDelete($id)
    {
        $cart = Cart::findOne($id);
        if ($cart) {
            if ($cart->delete()) {
                return json_encode(['code' => 200, 'msg' => '删除成功']);
            } else {
                return json_encode(['code' => 500, 'msg' => $cart->getErrors()]);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => '没有此报价信息']);
        }
    }
}