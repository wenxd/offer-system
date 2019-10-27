<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_purchase".
 *
 * @property int $id
 * @property string $purchase_sn 采购单号
 * @property int $order_id 订单ID
 * @property int $order_final_id 最终订单ID
 * @property string $goods_info 零件信息 json，包括ID
 * @property string $end_date 采购截止时间
 * @property int $admin_id 采购员ID
 * @property int $is_purchase 是否询价：0未询价 1已询价
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $order_agreement_id 收入合同ID
 * @property string $is_stock
 * @property string $is_complete
 * @property string $is_agreement
 * @property string $supplier_id
 * @property string $apply_reason
 */
class OrderPurchase extends \yii\db\ActiveRecord
{
    public $delivery_date;

    const IS_PURCHASE_NO  = '0';
    const IS_PURCHASE_YES = '1';

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

    const IS_AGREEMENT_NO  = '0';
    const IS_AGREEMENT_YES = '1';

    public static $purchase = [
        self::IS_PURCHASE_NO   => '否',
        self::IS_PURCHASE_YES  => '是',
    ];

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

    public static $complete = [
        self::IS_COMPLETE_NO   => '否',
        self::IS_COMPLETE_YES  => '是',
    ];

    public static $agreement = [
        self::IS_AGREEMENT_NO   => '否',
        self::IS_AGREEMENT_YES  => '是',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_purchase';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_final_id', 'order_agreement_id', 'admin_id', 'is_purchase', 'is_stock',
                'is_advancecharge', 'is_payment', 'is_bill', 'is_deleted', 'is_agreement', 'supplier_id'], 'integer'],
            [['end_date'], 'required'],
            [['end_date', 'updated_at', 'created_at', 'agreement_date', 'apply_reason'], 'safe'],
            [['purchase_sn', 'financial_remark'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                       => 'ID',
            'purchase_sn'              => '采购单号',
            'agreement_sn'             => '合同号',
            'agreement_date'           => '合同交货日期',
            'order_id'                 => '订单ID',
            'order_sn'                 => '订单编号',
            'order_final_id'           => '最终订单ID',
            'order_agreement_id'       => '合同订单ID',
            'order_agreement_sn'       => '合同订单号',
            'goods_info'               => '零件信息 json，包括ID',
            'end_date'                 => '收入合同交货日期',
            'admin_id'                 => '采购员ID',
            'financial_remark'         => '财务备注',
            'is_purchase'              => '完成采购',
            'is_stock'                 => '入库',
            'is_advancecharge'         => '预付款完成',
            'is_payment'               => '全单付款完成',
            'is_bill'                  => '收到发票',
            'is_deleted'               => '是否删除：0未删除 1已删除',
            'updated_at'               => '更新时间',
            'created_at'               => '创建时间',
            'payment_sn'               => '支出合同号',
            'supplier_id'              => '供应商',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderAgreement()
    {
        return $this->hasOne(OrderAgreement::className(), ['id' => 'order_agreement_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getAgreement()
    {
        return $this->hasOne(OrderAgreement::className(), ['id' => 'order_agreement_id']);
    }
}
