<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quote_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_final_id 最终订单ID
 * @property string $order_final_sn 最终订单号
 * @property string $order_quote_id 报价ID
 * @property string $order_quote_sn 报价单号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $relevance_id 关联ID（询价或库存）
 * @property int $number 数量
 * @property int $is_quote 是否报价 0否 1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class QuoteGoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_final_id', 'goods_id', 'type', 'relevance_id', 'number', 'is_quote', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['order_final_sn', 'order_quote_id', 'order_quote_sn'], 'string', 'max' => 255],
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
            'order_final_id' => '最终订单ID',
            'order_final_sn' => '最终订单号',
            'order_quote_id' => '报价ID',
            'order_quote_sn' => '报价单号',
            'goods_id' => '零件ID',
            'type' => '关联类型  0询价  1库存',
            'relevance_id' => '关联ID（询价或库存）',
            'number' => '数量',
            'is_quote' => '是否报价 0否 1是',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['id' => 'relevance_id']);
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::className(), ['id' => 'relevance_id']);
    }
}
