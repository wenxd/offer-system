<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agreement_goods_bak".
 *
 * @property int $id
 * @property int $order_agreement_id 合同订单ID
 * @property string $order_agreement_sn 合同订单号
 * @property int $agreement_goods_id 合同零件表ID主键
 * @property string $tax_rate 税率
 * @property string $price 单价
 * @property string $tax_price 含税单价
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 * @property int $purchase_number 采购数量
 * @property int $delivery_time 成本货期
 */
class AgreementGoodsBak extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_goods_bak';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_agreement_id', 'agreement_goods_id', 'purchase_number'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'delivery_time'], 'number'],
            [['order_agreement_sn'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'order_agreement_id'    => '合同订单ID',
            'order_agreement_sn'    => '合同订单号',
            'agreement_goods_id'    => '合同零件表ID主键',
            'tax_rate'              => '税率',
            'price'                 => '单价',
            'tax_price'             => '含税单价',
            'all_price'             => '未税总价',
            'all_tax_price'         => '含税总价',
            'purchase_number'       => '采购数量',
            'delivery_time'         => '成本货期',
        ];
    }
}
