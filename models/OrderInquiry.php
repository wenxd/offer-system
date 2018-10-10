<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_inquiry".
 *
 * @property int $id
 * @property string $inquiry_sn 询价单号
 * @property int $order_id 订单ID
 * @property string $goods_info 零件信息 json，包括ID，是否询价完成
 * @property string $end_date 询价截止时间
 * @property int $is_inquiry 是否询价：0未询价 1已询价
 * @property int $admin_id 询价员ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderInquiry extends \yii\db\ActiveRecord
{
    const IS_INQUIRY_NO  = 0;
    const IS_INQUIRY_YES = 1;

    public static $Inquiry = [
        self::IS_INQUIRY_NO  => '否',
        self::IS_INQUIRY_YES => '是',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_inquiry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'is_inquiry', 'admin_id', 'is_deleted'], 'integer'],
            [['end_date', 'updated_at', 'created_at', 'order_sn'], 'safe'],
            [['inquiry_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'inquiry_sn' => '询价单号',
            'order_id'   => '订单ID',
            'order_sn'   => '订单号',
            'goods_info' => '零件信息 json，包括ID，是否询价完成',
            'end_date'   => '询价截止时间',
            'is_inquiry' => '是否询价',
            'admin_id'   => '询价员ID',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}
