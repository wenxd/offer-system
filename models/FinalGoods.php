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
 */
class FinalGoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'final_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'type', 'relevance_id', 'is_purchase', 'purchase_id', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['order_final_id', 'final_sn'], 'string', 'max' => 255],
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
            'final_sn' => '最终订单号',
            'goods_id' => '零件ID',
            'type' => '关联类型  0询价  1库存',
            'relevance_id' => '关联ID',
            'is_purchase' => '是否有采购单 0否  1是',
            'purchase_id' => '采购单ID',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
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
        return $this->hasOne(Stock::className(), ['id' => 'relevance_id']);
    }
}
