<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $goods_id 零件ID
 * @property int $number 数量
 * @property int $serial 序号
 * @property int $is_out 是否出库 0否 1是
 * @property string $out_time 出库时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    const IS_OUT_NO  = '0';
    const IS_OUT_YES = '1';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'number', 'is_out', 'is_deleted'], 'integer'],
            [['out_time', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'order_id'   => '订单ID',
            'goods_id'   => '零件ID',
            'number'     => '数量',
            'is_out'     => '是否出库 0否 1是',
            'out_time'   => '出库时间',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getInquiryGoods()
    {
        return $this->hasOne(InquiryGoods::className(), ['order_id' => 'order_id', 'goods_id' => 'goods_id', 'serial' => 'serial'])->orderBy('inquiry_goods.id Desc');
    }

    public function getFinalGoods()
    {
        return $this->hasOne(FinalGoods::className(), ['order_id' => 'order_id', 'goods_id' => 'goods_id', 'serial' => 'serial']);
    }
}
