<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agreement_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_agreement_id 合同订单ID
 * @property string $order_agreement_sn 合同订单号
 * @property string $order_quote_id 报价ID
 * @property string $order_quote_sn 报价单号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $relevance_id 关联ID（询价或库存）
 * @property string $price 单价
 * @property string $tax_price 含税单价
 * @property int $number 数量
 * @property int $is_agreement 是否报价 0否 1是
 * @property string $agreement_sn 单条合同号
 * @property string $purchase_date 采购时间
 * @property string $agreement_date 采购时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $serial 序号
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 * @property string $quote_price 报价未税单价
 * @property string $quote_tax_price 报价含税单价
 * @property string $quote_all_price 报价未税总价
 * @property string $quote_all_tax_price 报价含税总价
 * @property string $inquiry_admin_id 报价员ID
 * @property string $tax_rate 报价员ID
 * @property string $quote_delivery_time
 */
class AgreementGoods extends \yii\db\ActiveRecord
{
    const IS_OUT_NO  = '0';
    const IS_OUT_YES = '1';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_agreement_id', 'goods_id', 'type', 'relevance_id', 'number', 'is_agreement',
                'is_deleted', 'order_quote_id'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price',
                'quote_all_price', 'quote_all_tax_price'], 'number'],
            [['updated_at', 'created_at', 'quote_delivery_time'], 'safe'],
            [['order_agreement_sn', 'order_quote_sn', 'agreement_sn', 'purchase_date',
                'agreement_date', 'serial'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'order_id'           => '订单ID',
            'order_agreement_id' => '合同订单ID',
            'order_agreement_sn' => '合同订单号',
            'order_quote_id'     => '报价ID',
            'order_quote_sn'     => '报价单号',
            'goods_id'           => '零件ID',
            'type'               => '关联类型  0询价  1库存',
            'relevance_id'       => '关联ID（询价或库存）',
            'tax_rate'           => '税率',
            'price'              => '单价',
            'tax_price'          => '含税单价',
            'number'             => '数量',
            'is_agreement'       => '是否报价 0否 1是',
            'agreement_sn'       => '单条合同号',
            'purchase_date'      => '采购时间',
            'agreement_date'     => '采购时间',
            'is_deleted'         => '是否删除：0未删除 1已删除',
            'updated_at'         => '更新时间',
            'created_at'         => '创建时间',
            'quote_tax_price'    => '报价含税单价',
            'quote_all_tax_price'=> '报价含税总价',
            'quote_delivery_time'=> '货期（周）',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::className(), ['id' => 'relevance_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderAgreement()
    {
        return $this->hasOne(OrderAgreement::className(), ['id' => 'order_agreement_id']);
    }
}
