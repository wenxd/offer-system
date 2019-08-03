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
 * @property string $is_quote 是否生成报价单 0否 1是
 */
class OrderFinal extends \yii\db\ActiveRecord
{
    const IS_QUOTE_NO  = '0';
    const IS_QUOTE_YES = '1';

    public static $quote = [
        self::IS_QUOTE_NO  => '否',
        self::IS_QUOTE_YES => '是'
    ];

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
            [['order_id', 'is_deleted', 'is_quote'], 'integer'],
            [['updated_at', 'created_at', 'provide_date', 'agreement_date'], 'safe'],
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
            'id'             => 'ID',
            'final_sn'       => '成本单号',
            'order_sn'       => '订单编号',
            'customer'       => '客户名称',
            'short_name'     => '客户缩写',
            'manage_name'    => '订单管理员',
            'provide_date'   => '订单报价截止日期',
            'agreement_date' => '合同交货日期',
            'order_id'       => '订单ID',
            'goods_info'     => '零件ID json',
            'is_deleted'     => '是否删除：0未删除 1已删除',
            'is_quote'       => '是否生成报价单',
            'updated_at'     => '更新时间',
            'created_at'     => '创建时间',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
