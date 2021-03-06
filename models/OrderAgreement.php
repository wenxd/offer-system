<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_agreement".
 *
 * @property int $id
 * @property string $agreement_sn 合同单号
 * @property int $order_id 订单ID
 * @property int $order_quote_id 报价订单ID
 * @property int $order_quote_sn 报价订单号
 * @property string $goods_info 零件信息 json，包括ID
 * @property string $agreement_date 合同截止时间
 * @property int $is_agreement 是否报价：0未报价 1已报价
 * @property int $admin_id 报价员ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $is_advancecharge
 * @property string $is_payment
 * @property string $is_bill
 * @property string $is_stock
 * @property string $advancecharge_at
 * @property string $payment_at
 * @property string $bill_at
 * @property string $stock_at
 * @property string $is_complete
 * @property string $sign_date
 * @property string $is_instock
 * @property string $customer_id
 * @property string $payment_price
 * @property string $payment_ratio
 * @property string $remain_price
 * @property string $is_purchase
 * @property string $stock_admin_id
 * @property string $financial_admin_id
 * @property string $expect_at
 * @property string $is_merge
 * @property string $is_all_stock
 * @property string $is_strategy_number
 * @property string $is_strategy
 * @property string $is_purchase_number
 */
class OrderAgreement extends \yii\db\ActiveRecord
{
    public $order_sn;
    public $quote_delivery_time;
    public $purchase_sn;
    public $price;

    const IS_STOCK_NO     = '0';
    const IS_STOCK_YES    = '1';

    const IS_ADVANCECHARGE_NO  = '0';
    const IS_ADVANCECHARGE_YES = '1';

    const IS_PAYMENT_NO  = '0';
    const IS_PAYMENT_YES = '1';

    const IS_BILL_NO     = '0';
    const IS_BILL_YES    = '1';

    const IS_COMPLETE_NO  = '0';
    const IS_COMPLETE_YES = '1';

    const IS_INSTOCK_NO  = '0';
    const IS_INSTOCK_YES = '1';

    const IS_PURCHASE_NO  = '0';
    const IS_PURCHASE_YES = '1';

    const IS_MERGE_NO    = '0';
    const IS_MERGE_YES   = '1';

    const IS_ALL_STOCK_NO  = '0';
    const IS_ALL_STOCK_YES = '1';

    public static $stock = [
        self::IS_STOCK_NO   => '否',
        self::IS_STOCK_YES  => '是',
    ];

    public static $advanceCharge = [
        self::IS_ADVANCECHARGE_NO   => '否',
        self::IS_ADVANCECHARGE_YES  => '是',
    ];

    public static $payment = [
        self::IS_PAYMENT_NO   => '否',
        self::IS_PAYMENT_YES  => '是',
    ];

    public static $bill = [
        self::IS_BILL_NO   => '否',
        self::IS_BILL_YES  => '是',
    ];

    public static $purchase = [
        self::IS_PURCHASE_NO   => '否',
        self::IS_PURCHASE_YES  => '是',
    ];

    public static $complete = [
        self::IS_COMPLETE_NO   => '否',
        self::IS_COMPLETE_YES  => '是',
    ];

    public static $allStock = [
        self::IS_ALL_STOCK_NO   => '否',
        self::IS_ALL_STOCK_YES  => '是',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_agreement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_quote_id', 'is_agreement', 'admin_id', 'is_deleted', 'is_advancecharge', 'is_payment',
                'is_bill', 'is_stock', 'is_complete', 'is_instock', 'customer_id', 'is_all_stock', 'is_purchase',
                'stock_admin_id', 'financial_admin_id', 'is_merge', 'is_strategy_number', 'is_strategy', 'is_purchase_number'], 'integer'],
            [['agreement_date', 'updated_at', 'created_at', 'sign_date', 'expect_at', 'payment_at', 'advancecharge_at',
                'stock_at', 'bill_at',], 'safe'],
            [['order_quote_sn', 'agreement_sn', 'order_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
            [['payment_price', 'remain_price', 'payment_ratio'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'agreement_sn'      => '收入合同单号',
            'order_id'          => '订单ID',
            'order_sn'          => '订单号',
            'order_quote_id'    => '报价订单ID',
            'order_quote_sn'    => '报价订单号',
            'goods_info'        => '零件信息 json，包括ID',
            'agreement_date'    => '收入合同交货时间',
            'is_agreement'      => '是否报价：0未报价 1已报价',
            'admin_id'          => '报价员ID',
            'is_deleted'        => '是否删除：0未删除 1已删除',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
            'advancecharge_at'  => '预收款时间',
            'payment_at'        => '收全款时间',
            'bill_at'           => '开发票时间',
            'stock_at'          => '出库时间',
            'financial_remark'  => '财务备注',
            'sign_date'         => '合同签订时间',
            'is_instock'        => '是否入库',
            'customer_id'       => '客户ID',
            'payment_ratio'     => '预收款比例',
            'payment_price'     => '收入合同金额',
            'remain_price'      => '收入订单剩余金额',
            'is_purchase'       => '是否生成采购单',
            'expect_at'         => '预计收全款时间',
            'is_merge'          => '是否合并过',
            'is_all_stock'      => '全部走库存',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    public function getAgreementStock()
    {
        return $this->hasOne(AgreementStock::className(), ['order_agreement_id' => 'id']);
    }

    //是否到齐（判断库存都够不够）
    public static function isEnoughStock($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where([
            'order_agreement_id' => $id,
        ])->all();
        $goods_id = ArrayHelper::getColumn($agreementGoodsList, 'goods_id');
        $stockGoods = Stock::find()->where(['good_id' => $goods_id])->indexBy('good_id')->asArray()->all();
        $isEnough = 1;
        foreach ($agreementGoodsList as $key => $value) {
            //订单需求数
            if ($value->order_number) {
                if (isset($stockGoods[$value->goods_id])) {
                    if ($stockGoods[$value->goods_id]['number'] < $value->order_number) {
                        $isEnough = 0;
                    }
                } else {
                    $isEnough = 0;
                    break;
                }
            }
        }
        return $isEnough;
    }
}
