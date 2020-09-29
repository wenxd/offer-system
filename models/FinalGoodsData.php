<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "final_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property string $order_final_id 最终订单ID
 * @property string $final_sn 最终订单号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $relevance_id 关联ID
 * @property int $is_purchase 是否有采购单 0否  1是
 * @property int $purchase_id 采购单ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $serial
 * @property string $number
 * @property string $key
 * @property string $tax
 * @property string $price
 * @property string $tax_price
 * @property string $all_price
 * @property string $all_tax_price
 * @property string $delivery_time
 * @property string $purchase_is_show
 * @property string $belong_to
 */
class FinalGoodsData extends FinalGoods
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'final_goods_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'type', 'relevance_id', 'is_purchase', 'purchase_id', 'is_deleted', 'number'
                , 'order_final_id', 'purchase_is_show', 'serial'], 'integer'],
            [['updated_at', 'created_at', 'tax', 'price', 'tax_price', 'all_price', 'all_tax_price', 'delivery_time'], 'safe'],
            [['final_sn', 'key', 'belong_to'], 'string', 'max' => 255],
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
            'order_final_id' => '成本单ID',
            'final_sn' => '成本单号',
            'goods_id' => '零件ID',
            'serial' => '序号',
            'type' => '关联类型  0询价  1库存',
            'relevance_id' => '关联ID',
            'is_purchase' => '是否有采购单 0否  1是',
            'purchase_id' => '采购单ID',
            'key' => '每次生成成本单的唯一key',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'number' => '订单数量',
            'tax' => '税率',
            'price' => '未税单价',
            'tax_price' => '含税单价',
            'all_price' => '未税总价',
            'all_tax_price' => '含税总价',
            'delivery_time' => '货期',
            'purchase_is_show' => '生成采购单的时候合并数据后不显示  1默认显示 0为不显示',
        ];
    }
}
