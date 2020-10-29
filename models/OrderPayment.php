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
 * @property string $take_time
 * @property string $remain_price
 * @property string $payment_price
 * @property string $is_agreement
 * @property string $apply_reason
 * @property string $agreement_at
 * @property string $delivery_date
 * @property string $supplier_id
 * @property string $stock_admin_id
 * @property string $financial_admin_id
 * @property string $is_notice
 * @property string $is_contract
 * @property string $is_reim
 * @property string $reim_date
 */
class OrderPayment extends \yii\db\ActiveRecord
{
    public $reason;
    public $purchase_id;
    public $price;
    public $income_deliver_time; //收入合同交货日期

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

    public static $verify = [
        self::IS_VERIFY_NO   => '否',
        self::IS_VERIFY_YES  => '是',
    ];

    public static $agreement = [
        self::IS_ADVANCECHARGE_NO   => '否',
        self::IS_ADVANCECHARGE_YES  => '是',
    ];

    public static $complete = [
        self::IS_COMPLETE_NO   => '否',
        self::IS_COMPLETE_YES  => '是',
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
                'is_advancecharge', 'is_bill', 'is_complete', 'is_agreement', 'supplier_id', 'stock_admin_id',
                'financial_admin_id', 'is_notice', 'is_contract', 'is_reim'], 'integer'],
            [['updated_at', 'created_at', 'payment_at', 'advancecharge_at', 'stock_at', 'bill_at', 'take_time',
                'payment_ratio', 'payment_price', 'remain_price', 'delivery_date', 'reim_date'], 'safe'],
            [['payment_sn', 'order_purchase_sn', 'apply_reason'], 'string', 'max' => 255],
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
            'payment_price'     => '合同金额',
            'remain_price'      => '待付款金额',
            'take_time'         => '合同尾款付款时间',
            'is_agreement'      => '是否生成合同',
            'apply_reason'      => '采购审核支出备注',
            'agreement_at'      => '支出合同签订时间',
            'delivery_date'     => '支出合同交货时间',
            'supplier_id'       => '供应商',
            'is_notice'         => '是否发通知',
            'is_reim'         => '是否报销',
            'reim_date'         => '报销时间',
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

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getPurchase()
    {
        return $this->hasOne(OrderPurchase::className(), ['id' => 'order_purchase_id']);
    }

    public function getAgreementStock()
    {
        return $this->hasOne(AgreementStock::className(), ['order_payment_id' => 'id']);
    }

    public static function isConfirm($id)
    {
        $res = AgreementStock::find()->where(['order_payment_id' => $id, 'is_confirm' => 0])->one();
        return $res;
    }
}
