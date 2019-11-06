<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agreement_stock".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_agreement_id 收入合同订单ID
 * @property string $order_agreement_sn 收入合同订单号
 * @property int $order_purchase_id 采购单ID
 * @property string $order_purchase_sn 采购单号
 * @property int $order_payment_id 支出合同订单ID
 * @property string $order_payment_sn 支出合同订单号
 * @property int $goods_id 零件ID
 * @property string $price 单价
 * @property string $tax_price 含税单价
 * @property int $use_number 使用库存数量
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 */
class AgreementStock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_agreement_id', 'order_purchase_id', 'order_payment_id', 'goods_id', 'use_number'], 'integer'],
            [['price', 'tax_price', 'all_price', 'all_tax_price'], 'number'],
            [['order_agreement_sn', 'order_purchase_sn', 'order_payment_sn'], 'string', 'max' => 255],
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
            'order_agreement_id' => '收入合同订单ID',
            'order_agreement_sn' => '收入合同订单号',
            'order_purchase_id' => '采购单ID',
            'order_purchase_sn' => '采购单号',
            'order_payment_id' => '支出合同订单ID',
            'order_payment_sn' => '支出合同订单号',
            'goods_id' => '零件ID',
            'price' => '单价',
            'tax_price' => '含税单价',
            'use_number' => '使用库存数量',
            'all_price' => '未税总价',
            'all_tax_price' => '含税总价',
        ];
    }
}
