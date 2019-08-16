<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_agreement".
 *
 * @property int $id
 * @property string $agreement_sn 合同单号
 * @property int $order_id 订单ID
 * @property int $order_quote_id 报价订单ID
 * @property int $order_quote_sn 报价订单号
 * @property string $goods_info 零件信息 json，包括ID
 * @property string $agreement_date 合同截止时间
 * @property int $is_agreement 是否报价：0未报价 1已报价
 * @property int $admin_id 报价员ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderAgreement extends \yii\db\ActiveRecord
{
    public $order_sn;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_agreement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_quote_id', 'is_agreement', 'admin_id', 'is_deleted'], 'integer'],
            [['agreement_date', 'updated_at', 'created_at'], 'safe'],
            [['order_quote_sn', 'agreement_sn', 'order_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'agreement_sn'    => '收入合同单号',
            'order_id'        => '订单ID',
            'order_sn'        => '订单号',
            'order_quote_id'  => '报价订单ID',
            'order_quote_sn'  => '报价订单号',
            'goods_info'      => '零件信息 json，包括ID',
            'agreement_date'  => '合同交货时间',
            'is_agreement'    => '是否报价：0未报价 1已报价',
            'admin_id'        => '报价员ID',
            'is_deleted'      => '是否删除：0未删除 1已删除',
            'updated_at'      => '更新时间',
            'created_at'      => '创建时间',
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
