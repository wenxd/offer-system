<?php

namespace app\controllers;

use app\models\Admin;
use app\models\AgreementGoods;
use app\models\AgreementStock;
use app\models\AuthAssignment;
use app\models\Inquiry;
use app\models\InquiryGoods;
use app\models\Order;
use app\models\OrderPayment;
use app\models\OrderPurchase;
use app\models\PaymentGoods;
use app\models\PurchaseGoods;
use app\models\Stock;
use app\models\SystemNotice;
use Yii;
use yii\filters\VerbFilter;
use app\models\OrderPaymentVerifySearch;
use yii\helpers\ArrayHelper;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class OrderPurchaseVerifyController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderPaymentVerifySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 保存支出合同单
     */
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderPurchase = OrderPurchase::findOne($params['order_purchase_id']);
//        $orderPurchase->supplier_id  = $params['supplier_id'];
//        $orderPurchase->apply_reason = trim($params['apply_reason']);
//        $orderPurchase->save();
        //保存支出单
        $orderPayment = new OrderPayment();
        $orderPayment->payment_sn           = $params['payment_sn'];
        $orderPayment->order_id             = $orderPurchase->order_id;
        $orderPayment->order_purchase_id    = $orderPurchase->id;
        $orderPayment->order_purchase_sn    = $orderPurchase->purchase_sn;
        $orderPayment->admin_id             = $params['admin_id'];
        $orderPayment->take_time            = date('Y-m-d H:i:s', (time() + $params['long_delivery_time'] * 7 * 3600 * 24));
        $orderPayment->apply_reason         = trim($params['apply_reason']);
        $orderPayment->agreement_at         = $params['agreement_date'];
        $orderPayment->delivery_date        = $params['delivery_date'];
        $orderPayment->supplier_id          = $params['supplier_id'];
        $orderPayment->payment_ratio        = $params['payment_ratio'];
        $orderPayment->is_contract        = $params['is_contract'] ?? 0;
        if ($orderPayment->save()) {
            $noticeOpen         = false;
            $noticeDeliveryOpen = false;
            $noticeSupplierOpen = false;
            $noticeNumberOpen   = false;
            $noticeStockOpen    = false;
            //payment_goods保存
            $money = 0;
            foreach ($params['goods_info'] as $key => $value) {
                $paymentGoods = new PaymentGoods();
                $paymentGoods->order_id             = $orderPurchase->order_id;
                $paymentGoods->order_payment_id     = $orderPayment->primaryKey;
                $paymentGoods->order_payment_sn     = $orderPayment->payment_sn;
                $paymentGoods->order_purchase_id    = $orderPurchase->id;
                $paymentGoods->order_purchase_sn    = $orderPurchase->purchase_sn;
                $paymentGoods->purchase_goods_id    = $value['purchase_goods_id'];

                $purchaseGoods = PurchaseGoods::findOne($value['purchase_goods_id']);
                //先把采购零件的数量保存到支出合同的零件数量
                $paymentGoods->number               = $purchaseGoods->fixed_number;
                $paymentGoods->before_delivery_time = $purchaseGoods->delivery_time;

                //$purchaseGoods->fixed_price         = $value['fix_price'];
                //$purchaseGoods->fixed_tax_price     = $value['fix_price'] * (1 + $purchaseGoods->tax_rate/100);
                //$purchaseGoods->fixed_number        = $value['fix_number'];
                //$purchaseGoods->reason              = '';
                //$purchaseGoods->apply_status        = PurchaseGoods::APPLY_STATUS_CREATE;
                //$purchaseGoods->fixed_delivery_time = $value['delivery_time'];
                //$purchaseGoods->save();

                $paymentGoods->serial               = $purchaseGoods->serial;
                $paymentGoods->goods_id             = $purchaseGoods->goods_id;
                $paymentGoods->relevance_id         = $purchaseGoods->relevance_id;

                $paymentGoods->tax_rate             = $purchaseGoods->tax_rate;
                $paymentGoods->price                = $purchaseGoods->price;
                $paymentGoods->tax_price            = $purchaseGoods->tax_price;
                $paymentGoods->all_price            = $purchaseGoods->all_price;
                $paymentGoods->all_tax_price        = $purchaseGoods->all_tax_price;
                $paymentGoods->fixed_price          = $value['fix_price'];
                $paymentGoods->fixed_tax_price      = $value['fix_tax_price'];
                $paymentGoods->fixed_all_price      = $value['fix_price'] * $value['fix_number'];
                $paymentGoods->fixed_all_tax_price  = $paymentGoods->fixed_tax_price * $value['fix_number'];
                $paymentGoods->fixed_number         = $value['fix_number'];
                $paymentGoods->inquiry_admin_id     = $params['admin_id'];
                $paymentGoods->supplier_id          = $params['supplier_id'];
                $paymentGoods->before_supplier_id   = $purchaseGoods->inquiry->supplier_id;
                $paymentGoods->delivery_time        = $value['delivery_time'];
                $paymentGoods->save();

                //项目订单处理保存使用库存记录
                if ($orderPurchase->order->order_type) {
                    $use_stock_number = $purchaseGoods->number >= $value['fix_number'] ? $purchaseGoods->number - $value['fix_number'] : 0;
                    $stock = Stock::find()->where(['good_id' => $purchaseGoods->goods_id])->one();
                    $agreementStock = AgreementStock::find()->where([
                        'order_id' => $orderPurchase->order_id,
                        'order_agreement_id' => $orderPurchase->order_agreement_id,
                        'order_purchase_id' => $orderPurchase->id,
                        'goods_id' => $purchaseGoods->goods_id
                    ])->one();
                    if ($use_stock_number) {
                        $noticeStockOpen = true;
                        if (!$agreementStock) {
                            $agreementStock = new AgreementStock();
                            $agreementStock->order_id = $orderPurchase->order_id;
                            $agreementStock->order_agreement_id = $orderPurchase->order_agreement_id;
                            $agreementStock->order_agreement_sn = $orderPurchase->agreement_sn;
                            $agreementStock->order_purchase_id = $orderPurchase->id;
                            $agreementStock->order_purchase_sn = $orderPurchase->purchase_sn;
                            $agreementStock->goods_id = $purchaseGoods->goods_id;
                        }
                        $agreementStock->order_payment_id = $orderPayment->id;
                        $agreementStock->order_payment_sn = $orderPayment->payment_sn;
                        $agreementStock->price = $stock ? $stock->price : 0;
                        $agreementStock->tax_price = $stock ? $stock->tax_price : 0;
                        $agreementStock->use_number = $use_stock_number;
                        $agreementStock->all_price = $agreementStock->price * $use_stock_number;
                        $agreementStock->all_tax_price = $agreementStock->tax_price * $use_stock_number;
                        $agreementStock->is_confirm = AgreementStock::IS_CONFIRM_NO;
//                        $agreementStock->save();
                    } else {
                        if ($agreementStock) {
                            $agreementStock->delete();
                        }
                    }
                }
                if ($paymentGoods->price != $paymentGoods->fixed_price) {
                    $noticeOpen = true;
                }

                if ($paymentGoods->before_delivery_time != $paymentGoods->delivery_time) {
                    $noticeDeliveryOpen = true;
                }

                if ($paymentGoods->before_supplier_id != $paymentGoods->supplier_id) {
                    $noticeSupplierOpen = true;
                }

                if ($paymentGoods->number != $paymentGoods->fixed_number) {
                    $noticeNumberOpen = true;
                }

                $money += $paymentGoods->fixed_all_tax_price;
            }

            $orderPayment->payment_price = $money;
            $orderPayment->remain_price  = $money;
            $orderPayment->save();

            //是否全部生成了支出申请
            $paymentGoodsCount  = PaymentGoods::find()->where(['order_purchase_id' => $orderPurchase->id])->count();
            $purchaseGoodsCount = PurchaseGoods::find()->where(['order_purchase_id' => $orderPurchase->id])->count();
            if ($purchaseGoodsCount == $paymentGoodsCount) {
                $orderPurchase->is_complete = 1;
                $orderPurchase->save();
            }
            //给管理员发送系统消息
//            if ($noticeOpen) {
//                $systemNotice = new SystemNotice();
//                $systemNotice->admin_id  = $params['admin_id'];
//                $systemNotice->content   = '采购生成支出合同有价格修改,支出合同号为' . $orderPayment->payment_sn;
//                $systemNotice->notice_at = date('Y-m-d H:i:s');
//                $systemNotice->save();
//            }
//            //给管理员发送系统消息(货期修改)
//            if ($noticeDeliveryOpen) {
//                $systemNotice = new SystemNotice();
//                $systemNotice->admin_id  = $params['admin_id'];
//                $systemNotice->content   = '采购生成支出合同有货期修改,支出合同号为' . $orderPayment->payment_sn;
//                $systemNotice->notice_at = date('Y-m-d H:i:s');
//                $systemNotice->save();
//            }
//            //给管理员发送系统消息(供应商修改)
//            if ($noticeSupplierOpen) {
//                $systemNotice = new SystemNotice();
//                $systemNotice->admin_id  = $params['admin_id'];
//                $systemNotice->content   = '采购生成支出合同有供应商修改,支出合同号为' . $orderPayment->payment_sn;
//                $systemNotice->notice_at = date('Y-m-d H:i:s');
//                $systemNotice->save();
//            }
//            //给管理员发送系统消息(数量修改)
//            if ($noticeNumberOpen) {
//                $systemNotice = new SystemNotice();
//                $systemNotice->admin_id  = $params['admin_id'];
//                $systemNotice->content   = '采购生成支出合同有数量修改,支出合同号为' . $orderPayment->payment_sn;
//                $systemNotice->notice_at = date('Y-m-d H:i:s');
//                $systemNotice->save();
//            }
            $superAdmin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
            $admin   = Admin::findOne($params['admin_id']);
            if ($noticeOpen || $noticeDeliveryOpen || $noticeSupplierOpen || $noticeNumberOpen) {
                $systemNotice = new SystemNotice();
                $systemNotice->admin_id  = $superAdmin->user_id;
                $systemNotice->content   = $admin->username . '生成支出合同有修改,支出合同号为' . $orderPayment->payment_sn;
                $systemNotice->notice_at = date('Y-m-d H:i:s');
                $systemNotice->save();
            }

            if (strtotime($params['delivery_date']) > strtotime($params['order_agreement_date'])) {
                $systemNotice = new SystemNotice();
                $systemNotice->admin_id  = $superAdmin->user_id;
                $systemNotice->content   = $admin->username . '支出合同交货时间比收入合同交货时间晚,支出合同号为' . $orderPayment->payment_sn;
                $systemNotice->notice_at = date('Y-m-d H:i:s');
                $systemNotice->save();
            }

            //给库管员发通知
            if ($noticeStockOpen) {
                $stockAdmin = AuthAssignment::find()->where(['item_name' => ['库管员', '库管员B']])->all();
                foreach ($stockAdmin as $key => $value) {
                    $systemNotice = new SystemNotice();
                    $systemNotice->admin_id  = $value->user_id;
                    $systemNotice->content   = '支出合同单号' . $orderPayment->payment_sn . '需要确认库存';
                    $systemNotice->notice_at = date('Y-m-d H:i:s');
                    $systemNotice->save();
                }
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    /**采购审核查看
     * @param $id
     */
    public function actionView($id)
    {
        $orderPayment = OrderPayment::findOne($id);

        $paymentGoods = PaymentGoods::find()->where(['order_payment_id' => $id])->orderBy('serial')->all();

        return $this->render('view', [
            'model'        => $orderPayment,
            'orderPayment' => $orderPayment,
            'paymentGoods' => $paymentGoods,
        ]);
    }

    /**采购审核详情
     * @param $id
     */
    public function actionDetail($id)
    {
        $orderPayment = OrderPayment::findOne($id);

        $paymentGoods = PaymentGoods::find()->where(['order_payment_id' => $id])->orderBy('serial')->all();

        return $this->render('detail', [
            'model'        => $orderPayment,
            'orderPayment' => $orderPayment,
            'paymentGoods' => $paymentGoods,
        ]);
    }

    /**
     * 审核通过
     */
    public function actionVerifyPass()
    {
        $params = Yii::$app->request->post();

        $orderPayment = OrderPayment::findOne($params['order_payment_id']);
        $orderPayment->is_verify = OrderPayment::IS_VERIFY_YES;
        $orderPayment->purchase_status = OrderPayment::PURCHASE_STATUS_PASS;
        try {
            $orderPayment->save();

            foreach ($params['goods_info'] as $paymentGoodsId) {
                $paymentGoods = PaymentGoods::findOne($paymentGoodsId);
                if ($paymentGoods) {
                    $purchaseGoods = PurchaseGoods::findOne($paymentGoods->purchase_goods_id);
                    $purchaseGoods->apply_status = PurchaseGoods::APPLY_STATUS_PASS;
                    $purchaseGoods->save();
                    //更新采购单与零件ID对应表后添加零件状态
                    PurchaseGoods::updateAll(['after' => 9], ['order_id' => $paymentGoods->order_id, 'goods_id' => $paymentGoods->goods_id, 'after' => 1]);
                }
            }

//            $hasNoApply = PurchaseGoods::find()->where(['order_purchase_id' => $orderPayment->order_purchase_id])
//                ->andWhere(['!=', 'apply_status', PurchaseGoods::APPLY_STATUS_PASS])->one();
//            if (!$hasNoApply) {
//                $orderPurchase = OrderPurchase::findOne($orderPayment->order_purchase_id);
//                $orderPurchase->is_complete = 1;
//                $orderPurchase->save();
//            }
            return json_encode(['code' => 200, 'msg' => '保存成功'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 审核驳回
     */
    public function actionVerifyReject()
    {
        $params = Yii::$app->request->post();
        try {
            //循环支出合同零件ID
            foreach ($params['goods_info'] as $paymentGoodsId) {
                $paymentGoods = PaymentGoods::findOne($paymentGoodsId);
                if ($paymentGoods) {
                    $purchaseGoods = PurchaseGoods::findOne($paymentGoods->purchase_goods_id);
                    $purchaseGoods->reason              = $params['reason'];
                    $purchaseGoods->apply_status        = PurchaseGoods::APPLY_STATUS_REJECT;
                    $purchaseGoods->fixed_price         = $purchaseGoods->price;
                    $purchaseGoods->fixed_tax_price     = $purchaseGoods->tax_price;
                    $purchaseGoods->fixed_number        = $paymentGoods->number;
                    $purchaseGoods->fixed_delivery_time = 0;

                    $purchaseGoods->save();
                }
                $paymentGoods->delete();
            }

            $orderPayment = OrderPayment::findOne($params['order_payment_id']);

            //恢复重新提出支出合同
            $orderPurchase = OrderPurchase::findOne($orderPayment->order_purchase_id);
            $orderPurchase->is_complete = OrderPurchase::IS_COMPLETE_NO;
            $orderPurchase->save();

            //给采购员发通知
            $systemNotice = new SystemNotice();
            $systemNotice->admin_id  = $orderPayment->admin_id;
            $systemNotice->content   = '你提交的支出申请被驳回,采购合同单号为' . $orderPurchase->purchase_sn;
            $systemNotice->notice_at = date('Y-m-d H:i:s');
            $systemNotice->save();

            $orderPayment->delete();
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        return json_encode(['code' => 200, 'msg' => '保存成功'], JSON_UNESCAPED_UNICODE);
    }

    /**生成支出合同
     * @param $id
     * @return \yii\web\Response
     */
    public function actionComplete($id)
    {
        $orderPayment = OrderPayment::findOne($id);
        $orderPayment->is_agreement = OrderPayment::IS_ADVANCECHARGE_YES;
        $orderPayment->save();

        PaymentGoods::updateAll(['is_payment' => PaymentGoods::IS_PAYMENT_YES], ['order_payment_id' => $id]);

        //是否全部生成了支出合同
        $isNotOrderPayment = OrderPayment::find()->where([
            'order_purchase_id' => $orderPayment->order_purchase_id,
            'is_agreement'      => OrderPayment::IS_AGREEMENT_NO
        ])->one();
        //采购单是否全部生成支出申请
        $orderPurchase = OrderPurchase::findOne($orderPayment->order_purchase_id);

        if (!$isNotOrderPayment && $orderPurchase->is_complete == OrderPurchase::IS_COMPLETE_YES) {
            $orderPurchase->is_agreement = OrderPurchase::IS_AGREEMENT_YES;
            $orderPurchase->save();
        }

        //获取采购零件列表保存为询价记录
        $paymentGoodsList = PaymentGoods::find()->where(['order_payment_id' => $id])->all();
        $data = [];
        $goods_ids = ArrayHelper::getColumn($paymentGoodsList, 'goods_id');
        Inquiry::updateAll(['is_newest' => 0], ['good_id' => $goods_ids]);
        foreach ($paymentGoodsList as $key => $paymentGoods) {
            $item = [];

            $item[] = $paymentGoods['goods_id'];
            $item[] = $paymentGoods['supplier_id'];
            $item[] = $paymentGoods['fixed_price'];
            $item[] = $paymentGoods['fixed_tax_price'];
            $item[] = $paymentGoods['tax_rate'];
            $item[] = $paymentGoods['fixed_all_tax_price'];
            $item[] = $paymentGoods['fixed_all_price'];
            $item[] = $paymentGoods['fixed_number'];
            $item[] = $paymentGoods['created_at'];
            $item[] = $paymentGoods['delivery_time'];
            $item[] = $paymentGoods['inquiry_admin_id'];
            $item[] = $paymentGoods['order_id'];
            $item[] = 1;
            $item[] = date('Y-m-d H:i:s');
            $item[] = date('Y-m-d H:i:s');
            $item[] = Inquiry::IS_PURCHASE_YES;

            $data[] = $item;
        }

        $keys = [
            'good_id',
            'supplier_id',
            'price',
            'tax_price',
            'tax_rate',
            'all_tax_price',
            'all_price',
            'number',
            'inquiry_datetime',
            'delivery_time',
            'admin_id',
            'order_id',
            'is_newest',
            'created_at',
            'updated_at',
            'is_purchase',
        ];

        $res = Yii::$app->db->createCommand()->batchInsert(Inquiry::tableName(), $keys, $data)->execute();

        return $this->redirect(['index']);
    }
}
