<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_payment_id 支出合同订单ID
 * @property string $order_payment_sn 支出合同订单号
 * @property int $order_purchase_id 采购订单ID
 * @property string $order_purchase_sn 采购订单号
 * @property int $purchase_goods_id 支出合同单商品主键
 * @property string $serial 序号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $relevance_id 关联ID（询价或库存）
 * @property int $number 采购数量
 * @property string $tax_rate 税率
 * @property string $price 未税单价
 * @property string $tax_price 含税总价
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 * @property string $fixed_price 修改后的未税单价
 * @property string $fixed_tax_price 修改后的含税单价
 * @property string $fixed_all_price 修改后的未税总价
 * @property string $fixed_all_tax_price 修改后的含税总价
 * @property int $fixed_number 修改后的数量
 * @property int $inquiry_admin_id 采购员ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property int $is_quality 是否质检
 * @property int $supplier_id 供应商ID
 * @property string $delivery_time 采购货期（周）
 * @property int $before_supplier_id 修改前供应商ID
 * @property string $before_delivery_time 修改前货期
 * @property string $is_payment 判断采购记录是否展示
 */
class PaymentGoods extends \yii\db\ActiveRecord
{
    const IS_QUALITY_NO  = '0';
    const IS_QUALITY_YES = '1';

    const IS_PAYMENT_NO  = '0';
    const IS_PAYMENT_YES = '1';

    public static $quality = [
        self::IS_QUALITY_NO  => '否',
        self::IS_QUALITY_YES => '是',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_payment_id', 'order_purchase_id', 'purchase_goods_id', 'goods_id', 'type',
                'relevance_id', 'number', 'fixed_number', 'inquiry_admin_id', 'is_quality', 'supplier_id',
                'before_supplier_id', 'is_payment'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'fixed_price', 'fixed_tax_price',
                'fixed_all_price', 'fixed_all_tax_price', 'delivery_time', 'before_delivery_time'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['order_payment_sn', 'order_purchase_sn', 'serial'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'order_id'              => '订单ID',
            'order_payment_id'      => '支出合同订单ID',
            'order_payment_sn'      => '支出合同订单号',
            'order_purchase_id'     => '采购订单ID',
            'order_purchase_sn'     => '采购订单号',
            'purchase_goods_id'     => '支出合同单商品主键',
            'serial'                => '序号',
            'goods_id'              => '零件ID',
            'type'                  => '关联类型  0询价  1库存',
            'relevance_id'          => '关联ID（询价或库存）',
            'number'                => '采购数量',
            'tax_rate'              => '税率',
            'price'                 => '未税单价',
            'tax_price'             => '含税总价',
            'all_price'             => '未税总价',
            'all_tax_price'         => '含税总价',
            'fixed_price'           => '修改后的未税单价',
            'fixed_tax_price'       => '修改后的含税单价',
            'fixed_all_price'       => '修改后的未税总价',
            'fixed_all_tax_price'   => '修改后的含税总价',
            'fixed_number'          => '修改后的数量',
            'inquiry_admin_id'      => '采购员ID',
            'updated_at'            => '更新时间',
            'created_at'            => '创建时间',
            'is_quality'            => '是否质检',
            'supplier_id'           => '供应商',
            'delivery_time'         => '采购货期（周）',
            'before_supplier_id'    => '修改前供应商ID',
            'before_delivery_time'  => '修改前货期',
            'is_payment'            => '判断是否生成合同用',
        ];
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::className(), ['id' => 'relevance_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getBeforeSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'before_supplier_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderPayment()
    {
        return $this->hasOne(OrderPayment::className(), ['id' => 'order_payment_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'inquiry_admin_id']);
    }

    public function getPurchaseGoods()
    {
        return $this->hasOne(PurchaseGoods::className(), ['id' => 'purchase_goods_id']);
    }
}
