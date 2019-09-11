<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_payment".
 *
 * @property int $id
 * @property string $payment_sn 支出合同单号
 * @property int $order_id 订单ID
 * @property int $order_purchase_id 采购订单ID
 * @property string $order_purchase_sn 采购单号
 * @property string $goods_info 零件信息 json，包括ID
 * @property int $is_payment 是否支付：0未 1已
 * @property int $admin_id 采购员ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $is_verify
 * @property string $is_stock
 * @property string $is_advancecharge
 * @property string $is_bill
 * @property string $payment_at
 * @property string $advancecharge_at
 * @property string $stock_at
 * @property string $financial_remark
 * @property string $bill_at
 * @property string $is_complete
 * @property string $purchase_status
 * @property string $payment_ratio
 */
class OrderPayment extends \yii\db\ActiveRecord
{
    public $reason;

    const PURCHASE_STATUS_CREATE = '0'; // 新的
    const PURCHASE_STATUS_PASS   = '1'; // 通过
    const PURCHASE_STATUS_REBUT  = '2'; // 驳回

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

    const IS_VERIFY_NO    = '0';
    const IS_VERIFY_YES   = '1';

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

    public static $verify = [
        self::IS_VERIFY_NO   => '否',
        self::IS_VERIFY_YES  => '是',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_purchase_id', 'admin_id', 'purchase_status', 'is_payment', 'is_stock',
                'is_advancecharge', 'is_bill', 'is_complete'], 'integer'],
            [['updated_at', 'created_at', 'payment_at', 'advancecharge_at', 'stock_at', 'bill_at', 'payment_ratio'], 'safe'],
            [['payment_sn', 'order_purchase_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'payment_sn'        => '支出合同单号',
            'order_id'          => '订单ID',
            'order_purchase_id' => '采购订单ID',
            'order_purchase_sn' => '采购单号',
            'goods_info'        => '零件信息 json，包括ID',
            'admin_id'          => '采购员ID',
            'purchase_status'   => '采购审核状态',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
            'reason'            => '驳回原因',
            'is_verify'         => '是否审核：0否 1是',
            'is_payment'        => '是否支付：0否 1已',
            'is_stock'          => '是否入库',
            'is_advancecharge'  => '是否预付款：0未 1已',
            'is_bill'           => '是否接收发票：0未 1已',
            'payment_at'        => '付全款时间',
            'advancecharge_at'  => '预付款时间',
            'stock_at'          => '入库时间',
            'bill_at'           => '收到发票时间',
            'financial_remark'  => '财务备注',
            'payment_ratio'     => '预付款比例',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
