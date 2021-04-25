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
 * @property string $serial 序号
 * @property string $tax_rate 税率
 * @property string $quote_delivery_time
 * @property string $competitor_goods_id
 * @property string $competitor_goods_tax_price
 * @property string $competitor_goods_tax_price_all
 * @property string $competitor_goods_quote_tax_price
 * @property string $competitor_goods_quote_tax_price_all
 * @property string $bid_remarks
 */
class QuoteGoods extends \yii\db\ActiveRecord
{
    const IS_QUOTE_NO  = 0;
    const IS_QUOTE_YES = 1;

    public static $quote = [
        self::IS_QUOTE_NO  => '否',
        self::IS_QUOTE_YES => '是',
    ];

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
            [['order_id', 'order_final_id', 'goods_id', 'type', 'relevance_id', 'number', 'is_quote', 'is_deleted', 'competitor_goods_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price', 'quote_all_price', 'quote_all_tax_price', 'delivery_time', 'quote_delivery_time', 'competitor_goods_tax_price', 'competitor_goods_tax_price_all', 'competitor_goods_quote_tax_price', 'competitor_goods_quote_tax_price_all', 'publish_tax_price', 'publish_tax_price_all'], 'number'],
            [['order_final_sn', 'order_quote_id', 'order_quote_sn', 'serial', 'bid_remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                  => 'ID',
            'order_id'            => '订单ID',
            'order_final_id'      => '成本订单ID',
            'order_final_sn'      => '成本订单号',
            'order_quote_id'      => '报价ID',
            'order_quote_sn'      => '报价单号',
            'goods_id'            => '零件ID',
            'type'                => '关联类型  0询价  1库存',
            'relevance_id'        => '询价ID',
            'number'              => '数量',
            'is_quote'            => '报价',
            'is_deleted'          => '是否删除：0未删除 1已删除',
            'updated_at'          => '更新时间',
            'created_at'          => '创建时间',
            'serial'              => '序号',
            'tax_rate'            => '税率',
            'price'               => '未税单价',
            'tax_price'           => '含税单价',
            'all_price'           => '未税总价',
            'all_tax_price'       => '含税总价',
            'quote_price'         => '报价未税单价',
            'quote_tax_price'     => '报价含税单价',
            'quote_all_price'     => '报价未税总价',
            'quote_all_tax_price' => '报价含税总价',
            'delivery_time'       => '货期(周)',
            'quote_delivery_time' => '货期(周)',
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

    public function getStockNumber()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }

    public function getAgreementGoods()
    {
        return $this->hasOne(AgreementGoods::className(), ['order_quote_id' => 'order_quote_id', 'goods_id' => 'goods_id', 'serial' => 'serial']);
    }
}
