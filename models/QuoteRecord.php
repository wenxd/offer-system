<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "quote_record".
 *
 * @property int $id 自增id
 * @property int $type 报价类型 1:最新询价  2:优选  3:本地库存
 * @property int $good_id 零件ID
 * @property string $quote_price 报价价格
 * @property int $number 购买数量
 * @property int $order_quote_id 报价订单ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class QuoteRecord extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    # 修改之前
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                #设置默认值
                'value' => date('Y-m-d H:i:s', time())
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'good_id', 'number', 'order_quote_id'], 'integer'],
            [['quote_price'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => '自增id',
            'type'           => '报价类型 1:最新询价  2:优选  3:本地库存',
            'good_id'        => '零件ID',
            'quote_price'    => '报价价格',
            'number'         => '购买数量',
            'order_quote_id' => '报价订单ID',
            'order_type'     => '订单类型 1:报价单  2询价单',
            'updated_at'     => '更新时间',
            'created_at'     => '创建时间',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::className(), ['id' => 'inquiry_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['id' => 'inquiry_id']);
    }
}
