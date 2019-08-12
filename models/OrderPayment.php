<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_payment".
 *
 * @property int $id
 * @property string $payment_sn 支出合同单号
 * @property int $order_id 订单ID
 * @property int $order_purchase_id 采购订单ID
 * @property string $order_purchase_sn 采购单号
 * @property string $goods_info 零件信息 json，包括ID
 * @property string $payment_at 支付时间
 * @property int $is_payment 是否支付：0未 1已
 * @property int $admin_id 采购员ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_purchase_id', 'is_payment', 'admin_id'], 'integer'],
            [['payment_at', 'updated_at', 'created_at'], 'safe'],
            [['payment_sn', 'order_purchase_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'payment_sn'        => '支出合同单号',
            'order_id'          => '订单ID',
            'order_purchase_id' => '采购订单ID',
            'order_purchase_sn' => '采购单号',
            'goods_info'        => '零件信息 json，包括ID',
            'payment_at'        => '支付时间',
            'is_payment'        => '是否支付：0未 1已',
            'admin_id'          => '采购员ID',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
        ];
    }
}
