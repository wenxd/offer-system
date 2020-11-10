<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inquiry_goods_clarify".
 *
 * @property int $clarify_id 澄清记录列表
 * @property int $inquiry_goods_id 询价单号与零件ID对应表ID
 * @property int $order_id 订单ID
 * @property int $order_inquiry_id 询价单ID
 * @property string $inquiry_sn 询价单号
 * @property int $goods_id 零件ID
 * @property int $is_inquiry 是否询价 0否 1是
 * @property int $is_result 是否寻不出  0否  1是 
 * @property string $reason 澄清问题
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $not_result_at 寻不出时间
 * @property int $admin_id 询价员ID
 * @property int $is_result_tag 是否寻不出  0否  1是, 用于标记列表
 * @property string $remark 特殊说明
 * @property string $clarify 澄清回复
 */
class InquiryGoodsClarify extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inquiry_goods_clarify';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inquiry_goods_id'], 'required'],
            [['inquiry_goods_id', 'order_id', 'order_inquiry_id', 'goods_id', 'is_inquiry', 'is_result', 'is_deleted', 'admin_id', 'is_result_tag'], 'integer'],
            [['updated_at', 'created_at', 'not_result_at'], 'safe'],
            [['inquiry_sn', 'reason', 'remark', 'clarify'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clarify_id' => 'ID',
            'inquiry_goods_id' => '询价单号与零件ID对应表ID',
            'order_id' => '订单ID',
            'order_inquiry_id' => '询价单ID',
            'inquiry_sn' => '询价单号',
            'goods_id' => '零件ID',
            'is_inquiry' => '是否询价',
            'is_result' => '是否寻不出',
            'reason' => '澄清问题',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'not_result_at' => '寻不出时间',
            'admin_id' => '询价员ID',
            'is_result_tag' => '是否寻不出',
            'remark' => '特殊说明',
            'clarify' => '澄清回复',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getOrderInquiry()
    {
        return $this->hasOne(OrderInquiry::className(), ['inquiry_sn' => 'inquiry_sn']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
