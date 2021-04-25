<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/22
 * Time: 14:03
 */
namespace app\controllers;


use app\models\Brand;
use app\models\Customer;
use app\models\FirstParty;
use app\models\Goods;
use app\models\Order;
use app\models\OrderGoods;
use app\models\OrderPurchase;
use app\models\OrderQuote;
use app\models\PaymentGoods;
use app\models\PurchaseGoods;
use app\models\Supplier;
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

    /**获取零件号
     * @return string
     */
    public function actionGetGoodNumber()
    {
        $good_number = (string)trim(Yii::$app->request->get('good_number'));

        $goodsList = Goods::find()->select('id, goods_number, brand_id, material_code')
            ->filterWhere(['like', 'goods_number', $good_number])
            ->andWhere(['is_deleted' => Goods::IS_DELETED_NO])->asArray()->all();

        return json_encode(['code' => 200, 'data' => $goodsList]);
    }

    /**获取厂家号
     * @return string
     */
    public function actionGetGoodNumberB()
    {
        $good_number_b = (string)trim(Yii::$app->request->get('good_number_b'));

        $goodsList = Goods::find()->select('id, goods_number, brand_id, material_code')
            ->filterWhere(['like', 'goods_number_b', $good_number_b])
            ->andWhere(['is_deleted' => Goods::IS_DELETED_NO])->asArray()->all();

        return json_encode(['code' => 200, 'data' => $goodsList]);
    }

    /**获取零件号新方法
     * @return string
     */
    public function actionGetNewGoodNumber()
    {
        $good_number = (string)trim(Yii::$app->request->get('good_number'));
        $goodsList = Goods::find()->filterWhere(['like', 'goods_number', $good_number])
            ->andWhere(['is_deleted' => Goods::IS_DELETED_NO])->asArray()->all();
        $goodsIds = ArrayHelper::getColumn($goodsList, 'id');
        $stockList = Stock::find()->where(['good_id' => $goodsIds])->indexBy('good_id')->asArray()->all();
        $data = [];
        foreach ($goodsList as $key => $goods) {
            $data[$key] = $goods;
            $data[$key]['stock_position'] = '';
            if (isset($stockList[$goods['id']])) {
                $data[$key]['stock_position'] = $stockList[$goods['id']]['position'];
            }
        }
        return json_encode(['code' => 200, 'data' => $data]);
    }

    /**获取零件号并带上最后一次采购价格
     * @return string
     */
    public function actionGetGoodNumberInStock()
    {
        $good_number = (string)trim(Yii::$app->request->get('good_number'));
        $goodsList = Goods::find()->filterWhere(['like', 'goods_number', $good_number])
            ->andWhere(['is_deleted' => Goods::IS_DELETED_NO])->all();
        //获取最后一次采购单
        $goodsId = implode(',', ArrayHelper::getColumn($goodsList, 'id'));
        $paymentGoods = Yii::$app->db->createCommand("SELECT a.* FROM payment_goods AS a INNER JOIN (SELECT max(created_at) AS created_at FROM payment_goods WHERE goods_id in ($goodsId) GROUP BY  goods_id) AS b ON a.created_at = b.created_at GROUP BY  a.goods_id  ORDER BY   a.created_at DESC")->queryAll();

        $paymentGoodsList = [];
        foreach ($paymentGoods as $k => $v) {
            $paymentGoodsList[$v['goods_id']] = $v;
        }

        $data = [];
        foreach ($goodsList as $key => $value) {
            $item = $value->toArray();
            $item['price'] = 0;
            if (isset($paymentGoodsList[$value->id])) {
                $item['price'] = $paymentGoodsList[$value->id]['fixed_price'];
            }
            $data[] = $item;
        }

        return json_encode(['code' => 200, 'data' => $data]);
    }

    /**获取厂家号新方法
     * @return string
     */
    public function actionGetNewGoodNumberB()
    {
        $good_number_b = (string)trim(Yii::$app->request->get('good_number_b'));
        $goodsList = Goods::find()->filterWhere(['like', 'goods_number_b', $good_number_b])
            ->andWhere(['is_deleted' => Goods::IS_DELETED_NO])->asArray()->all();
        return json_encode(['code' => 200, 'data' => $goodsList]);
    }

    /**获取供应商
     * @return string
     */
    public function actionGetSupplier()
    {
        $supplier_name = (string)trim(Yii::$app->request->get('supplier_name'));

        $supplierList       = Supplier::find()->filterWhere(['like', 'name', $supplier_name])
            ->andWhere([
                'is_confirm' => Supplier::IS_CONFIRM_YES,
                'is_deleted' => Supplier::IS_DELETED_NO
            ])->all();
        $supplier_name_list = ArrayHelper::getColumn($supplierList, 'name');

        return json_encode(['code' => 200, 'data' => $supplier_name_list]);
    }

    /**
     * 去重供应商
     * @return string
     */
    public function actionGetSupplierName()
    {
        $name = (string)trim(Yii::$app->request->post('name'));
        $supplierList       = Supplier::find()
            ->orWhere(['name' => $name])
            ->orWhere(['short_name' => $name])
            ->andWhere(['is_deleted' => Supplier::IS_DELETED_NO])->asArray()->one();
        if ($supplierList) {
            return json_encode(['code' => 500, 'msg' => '供应商已存在']);
        }
        return json_encode(['code' => 200, 'msg' => '供应商不存在']);
    }

    /*
     * 搜索结果
     */
    public function actionSearch()
    {
        $data = [];
        $good_number = (string)trim(Yii::$app->request->get('good_number'));

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

    /**
     * 选择采购员时判断同一个订单同一个采购员下是否已经有采购单号
     */
    public function actionGetPurchaseSn($order_id = 239, $admin_id = 28)
    {
        $data = OrderPurchase::find()->where(['order_id' => $order_id, 'admin_id' => $admin_id])->orderBy(['id' => SORT_DESC])->asArray()->one();
        if (empty($data)) {
            return json_encode(['code' => 500,  'msg' => '数据未找到']);
        }
        return json_encode(['code' => 200,  'msg' => '成功', 'data' => ['purchase_sn' => $data['purchase_sn']]]);
    }

    public function actionGet()
    {
        $session = Yii::$app->session;
        $name = $session->get('name');

        echo $name;
    }

    public function actionUpdateAllSupplierAdmin()
    {
        $post = Yii::$app->request->post();
        $res = Supplier::updateAll(['admin_id' => $post['admin_id']], ['id' => $post['ids']]);
        if ($res) {
            return json_encode(['code' => 200, 'msg' => '更新申请人成功']);
        }
        return json_encode(['code' => 200, 'msg' => '失败']);
    }

    public function actionUpdateAllInquiryAdmin()
    {
        $post = Yii::$app->request->post();
        $res = Inquiry::updateAll(['admin_id' => $post['admin_id']], ['id' => $post['ids']]);
        if ($res) {
            return json_encode(['code' => 200, 'msg' => '更新询价员成功']);
        }
        return json_encode(['code' => 500, 'msg' => '失败']);
    }

    /**
     * 中标
     */
    public function actionExitMark()
    {
        $post = Yii::$app->request->post();
        $res = OrderQuote::updateAll(['is_mark' => $post['is_mark']], ['id' => $post['id']]);
        if ($res) {
            return json_encode(['code' => 200, 'msg' => '成功']);
        }
        return json_encode(['code' => 500, 'msg' => '失败']);
    }

    /**
     * 中标
     */
    public function actionOrderPurchaseTaxSave()
    {
        $goods_info = Yii::$app->request->post('goods_info', []);
        foreach ($goods_info as $item) {
            $model = PurchaseGoods::findOne($item['purchase_goods_id']);
            $model->tax_rate = $item['tax'];
//            $model->tax_price = $model->price * ($model->tax_rate / 100 + 1);
//            $model->all_tax_price = $model->tax_price * $model->fixed_number;
//            $model->tax_price = $item['tax_price'];
            $model->fixed_tax_price = $item['tax_price'];
            $model->all_tax_price = $item['all_tax_price'];
            if (!$model->save()) {
                return json_encode(['code' => 500, 'msg' => $model->getErrors()]);
            }
        }
        return json_encode(['code' => 200, 'msg' => '成功']);
    }

    // 获取客户下拉列表
    public function actionGetCustomerList($q = '')
    {
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = [];
        $list = Customer::find()->where(['like', 'name', $q])->all();
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        $out['results'] = array_values($data);
        return json_encode($out);
    }

    // 获取甲方采办人下拉列表
    public function actionGetFirstPartyList($q = '')
    {
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = [];
        $list = FirstParty::find()->where(['like', 'name', $q])->all();
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        $out['results'] = array_values($data);
        return json_encode($out);
    }

    // 获取甲方采办人下拉列表
    public function actionGetBrandList($q = '')
    {
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = [];
        $list = Brand::find()->where(['like', 'name', $q])->andWhere(['is_deleted' => Brand::IS_DELETED_NO])->all();
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        $out['results'] = array_values($data);
        return json_encode($out);
    }

    // 获取甲方采办人下拉列表
    public function actionShowOrderSn($order_sn)
    {
        $order = Order::find()->where(['order_sn' => $order_sn])->asArray()->one();
        if (empty($order)) {
            return json_encode(['code' => 500, 'msg' => '数据不存在']);
        } else {
            return json_encode(['code' => 200, 'msg' => '成功', 'data' => $order]);
        }
    }

    /**
     * 修改订单序号
     */
    public function actionUpdateOrderGoodsSerial()
    {
        $post = Yii::$app->request->post();
        $order_goods = OrderGoods::findOne($post['id']);
        $order_goods->serial = $post['serial'];
        if ($order_goods->save()) {
            return json_encode(['code' => 200, 'msg' => '成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '失败', 'data' => $order_goods->getErrors()]);
        }
    }
}