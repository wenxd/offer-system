<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_final".
 *
 * @property int $id
 * @property string $final_sn 最终订单号
 * @property int $order_id 订单ID
 * @property string $goods_info 零件ID json
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderFinal extends \yii\db\ActiveRecord
{
    public $provide_date;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_final';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at', 'provide_date'], 'safe'],
            [['final_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'final_sn'     => '最终订单号',
            'order_sn'     => '订单编号',
            'customer'     => '客户名称',
            'short_name'   => '客户缩写',
            'manage_name'  => '订单管理员',
            'provide_date' => '订单报价截止日期',
            'order_id'     => '订单ID',
            'goods_info'   => '零件ID json',
            'is_deleted'   => '是否删除：0未删除 1已删除',
            'updated_at'   => '更新时间',
            'created_at'   => '创建时间',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
