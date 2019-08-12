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
 * @property int $inquiry_admin_id 询价员ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class PaymentGoods extends \yii\db\ActiveRecord
{
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
            [['order_id', 'order_payment_id', 'order_purchase_id', 'goods_id', 'type', 'relevance_id', 'number', 'fixed_number', 'inquiry_admin_id'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'fixed_price', 'fixed_tax_price', 'fixed_all_price', 'fixed_all_tax_price'], 'number'],
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
            'id' => 'ID',
            'order_id' => '订单ID',
            'order_payment_id' => '支出合同订单ID',
            'order_payment_sn' => '支出合同订单号',
            'order_purchase_id' => '采购订单ID',
            'order_purchase_sn' => '采购订单号',
            'serial' => '序号',
            'goods_id' => '零件ID',
            'type' => '关联类型  0询价  1库存',
            'relevance_id' => '关联ID（询价或库存）',
            'number' => '采购数量',
            'tax_rate' => '税率',
            'price' => '未税单价',
            'tax_price' => '含税总价',
            'all_price' => '未税总价',
            'all_tax_price' => '含税总价',
            'fixed_price' => '修改后的未税单价',
            'fixed_tax_price' => '修改后的含税单价',
            'fixed_all_price' => '修改后的未税总价',
            'fixed_all_tax_price' => '修改后的含税总价',
            'fixed_number' => '修改后的数量',
            'inquiry_admin_id' => '询价员ID',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
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
}
