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
 * @property int $inquiry_id 询价ID或库存ID
 * @property int $goods_id 零件ID
 * @property string $quote_price 报价价格
 * @property int $number 购买数量
 * @property int $order_quote_id 报价订单ID
 * @property int $order_type 订单类型 1:报价单  2询价单
 * @property string $remark 询价备注
 * @property int $status 是否询价  0未询价  1已询价
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class QuoteRecord extends ActiveRecord
{
    const TYPE_QUOTE   = '1';
    const TYPE_INQUIRY = '2';

    const STATUS_NO    = '0';
    const STATUS_YES   = '1';

    public static $status = [
        self::STATUS_NO     => '未询价',
        self::STATUS_YES => '已询价',
    ];

    public $goods_number;
    public $supplier_id;
    public $supplier_name;
    public $tax_rate = 10;
    public $price;
    public $tax_price;

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
            [['type', 'inquiry_id', 'goods_id', 'number', 'order_quote_id', 'order_type', 'status'], 'integer'],
            [['quote_price', 'tax_rate', 'price', 'tax_price'], 'number'],
            [['offer_date', 'updated_at', 'created_at', 'supplier_id', 'supplier_name', 'goods_number'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['tax_rate', 'price', 'tax_price', 'offer_date', 'remark'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'type'             => '报价类型 1:最新询价  2:优选  3:本地库存',
            'inquiry_id'       => '询价ID或库存ID',
            'goods_id'         => '零件ID',
            'quote_price'      => '报价价格',
            'number'           => '购买数量',
            'order_quote_id'   => '报价订单ID',
            'order_type'       => '订单类型 1:报价单  2询价单',
            'remark'           => '询价备注',
            'status'           => '是否询价',
            'offer_date'       => '交货期',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',

            'tax_rate'         => '税率',
            'price'            => '未税价格',
            'tax_price'        => '含税价格',

        ];
    }

    public function beforeSave($insert)
    {
        $inquiry = new Inquiry();
        $inquiry->good_id     = $this->goods_id;
        $inquiry->supplier_id = $this->supplier_id;
        $inquiry->price       = $this->price;
        $inquiry->tax_price   = $this->tax_price;
        $inquiry->tax_rate    = $this->tax_rate;
        $inquiry->inquiry_datetime = date('Y-m-d H:i:s');
        $inquiry->offer_date  = $this->offer_date;
        $inquiry->remark      = $this->remark;
        $inquiry->save();
        $this->type = 1;
        $this->inquiry_id = $inquiry->primaryKey;
        $this->status = self::STATUS_YES;
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        //对询价订单做状态改变
        $record = QuoteRecord::find()->where(['order_quote_id' => $this->order_quote_id, 'status' => QuoteRecord::STATUS_NO])->one();
        if (!$record) {
            $order = Order::findOne($this->order_quote_id);
            $order->status = Order::STATUS_YES;
            $order->save();
        }
        parent::afterSave($insert, $changedAttributes);
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
