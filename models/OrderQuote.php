<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_quote".
 *
 * @property int $id
 * @property string $quote_sn 报价单号
 * @property int $order_id 订单ID
 * @property string $goods_info 零件信息 json，包括ID
 * @property string $end_date 报价截止时间
 * @property int $is_quote 是否报价：0未报价 1已报价
 * @property int $admin_id 报价员ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderQuote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'is_quote', 'admin_id', 'is_deleted'], 'integer'],
            [['end_date', 'updated_at', 'created_at'], 'safe'],
            [['quote_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quote_sn' => '报价单号',
            'order_id' => '订单ID',
            'goods_info' => '零件信息 json，包括ID',
            'end_date' => '报价截止时间',
            'is_quote' => '是否报价：0未报价 1已报价',
            'admin_id' => '报价员ID',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
