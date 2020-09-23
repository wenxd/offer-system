<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agreement_goods_data".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_agreement_id 合同订单ID
 * @property string $order_agreement_sn 合同订单号
 * @property int $order_quote_id 报价ID
 * @property string $order_quote_sn 报价单号
 * @property string $serial 序号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $relevance_id 关联ID（询价或库存）
 * @property string $tax_rate 税率
 * @property string $price 单价
 * @property string $tax_price 含税单价
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 * @property string $quote_price 报价未税价格
 * @property string $quote_tax_price 报价含税价格
 * @property string $quote_all_price 报价未税总价
 * @property string $quote_all_tax_price 报价含税总价
 * @property int $number 订单需求数量（原始数据，报价单生成收入合同而来）
 * @property int $order_number 生成采购单的零件数量。用于合并后展示用
 * @property int $is_agreement 是否报价 0否 1是
 * @property string $agreement_sn 单条合同号
 * @property string $purchase_date 采购时间
 * @property string $agreement_date 采购时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property int $inquiry_admin_id 询价员ID
 * @property int $is_out 是否出库
 * @property string $quote_delivery_time 报价货期（周）
 * @property string $delivery_time 成本货期（周）
 * @property int $is_quality 是否质检 0否 1是
 * @property int $purchase_number 采购数量
 * @property int $purchase_is_show 生成采购单的时候合并数据后不显示 1默认显示 0为不显示
 */
class AgreementGoodsData extends AgreementGoods
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_goods_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'order_id', 'order_agreement_id', 'order_quote_id', 'goods_id', 'type', 'relevance_id', 'number', 'order_number', 'is_agreement', 'is_deleted', 'inquiry_admin_id', 'is_out', 'is_quality', 'purchase_number', 'purchase_is_show'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price', 'quote_all_price', 'quote_all_tax_price', 'quote_delivery_time', 'delivery_time'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['order_agreement_sn', 'order_quote_sn', 'serial', 'agreement_sn', 'purchase_date', 'agreement_date'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'order_agreement_id' => '合同订单ID',
            'order_agreement_sn' => '合同订单号',
            'order_quote_id' => '报价ID',
            'order_quote_sn' => '报价单号',
            'serial' => '序号',
            'goods_id' => '零件ID',
            'type' => '关联类型  0询价  1库存',
            'relevance_id' => '关联ID（询价或库存）',
            'tax_rate' => '税率',
            'price' => '单价',
            'tax_price' => '含税单价',
            'all_price' => '未税总价',
            'all_tax_price' => '含税总价',
            'quote_price' => '报价未税价格',
            'quote_tax_price' => '报价含税价格',
            'quote_all_price' => '报价未税总价',
            'quote_all_tax_price' => '报价含税总价',
            'number' => '订单需求数量（原始数据，报价单生成收入合同而来）',
            'order_number' => '生成采购单的零件数量。用于合并后展示用',
            'is_agreement' => '是否报价 0否 1是',
            'agreement_sn' => '单条合同号',
            'purchase_date' => '采购时间',
            'agreement_date' => '采购时间',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'inquiry_admin_id' => '询价员ID',
            'is_out' => '是否出库',
            'quote_delivery_time' => '报价货期（周）',
            'delivery_time' => '成本货期（周）',
            'is_quality' => '是否质检 0否 1是',
            'purchase_number' => '采购数量',
            'purchase_is_show' => '生成采购单的时候合并数据后不显示 1默认显示 0为不显示',
        ];
    }
}
