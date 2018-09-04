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
        $inquiryId       = Yii::$app->request->post('inquiryId');
        $number          = Yii::$app->request->post('number');
        $type            = Yii::$app->request->post('type');
        $goods_id        = Yii::$app->request->post('goods_id');
        $quotation_price = Yii::$app->request->post('quotation_price');

        if ($inquiryId) {
            if ($type == 2) {
                $stock = Stock::findOne($inquiryId);
                if ($stock->number < $number){
                    return json_encode(['code' => 500, 'msg' => '本地库存不足']);
                }
            }
            $cart = Cart::findOne(['goods_id' => $goods_id]);
            if ($cart) {
                return json_encode(['code' => 500, 'msg' => '此商品已加入报价单']);
            } else {
                $cart              = new Cart();
                $cart->goods_id    = $goods_id;
            }
            $cart->inquiry_id      = $inquiryId;
            $cart->type            = $type;
            $cart->number          = $number;
            $cart->quotation_price = $quotation_price;

            if ($cart->save()) {
                return json_encode(['code' => 200, 'data' => '']);
            } else {
                return json_encode(['code' => 500, 'msg' => $cart->getErrors()]);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => '缺少ID']);
        }
    }

    public function actionList()
    {
        $data = [];

        $cartList = Cart::find()->all();

        $model = new OrderInquiry();
        $model->loadDefaultValues();
        $data['model']    = $model;
        $data['cartList'] = $cartList;

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

    public function actionEditPrice()
    {
        $id    = Yii::$app->request->post('cart_id');
        $price = Yii::$app->request->post('price');
        $cart = Cart::findOne($id);
        if (!$cart) {
            return json_encode(['code' => 500, 'msg' => '没有此条记录']);
        }
        $cart->quotation_price = $price;
        if ($cart->save()) {
            return json_encode(['code' => 200, 'msg' => '修改成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $cart->getErrors()]);
        }
    }
}