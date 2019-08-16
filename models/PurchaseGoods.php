<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchase_goods".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_agreement_id 收入合同订单ID
 * @property string $order_purchase_id 采购订单ID
 * @property string $order_purchase_sn 采购订单号
 * @property int $goods_id 零件ID
 * @property int $type 关联类型  0询价  1库存
 * @property int $number 采购数量
 * @property int $relevance_id 关联ID（询价或库存）
 * @property int $is_purchase 是否采购了 0否  1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $serial 序号
 * @property string $fixed_number 修改后价格
 * @property string $tax_rate
 * @property string $price
 * @property string $tax_price
 * @property string $all_price
 * @property string $all_tax_price
 * @property string $fixed_price
 * @property string $fixed_tax_price
 * @property string $inquiry_admin_id
 * @property string $agreement_sn
 * @property string $purchase_date
 * @property string $is_stock
 */
class PurchaseGoods extends \yii\db\ActiveRecord
{
    public $goods_number;
    public $order_sn;

    const IS_STOCK_NO     = '0';
    const IS_STOCK_YES    = '1';

    const IS_PURCHASE_NO  = '0';
    const IS_PURCHASE_YES = '1';

    const TYPE_INQUIRY  = '0';
    const TYPE_STOCK    = '1';

    const APPLY_STATUS_CREATE = '1';
    const APPLY_STATUS_PASS   = '2';
    const APPLY_STATUS_REJECT = '3';

    public static $purchase = [
        self::IS_PURCHASE_NO  => '否',
        self::IS_PURCHASE_YES => '是',
    ];

    public static $stock = [
        self::IS_STOCK_NO  => '否',
        self::IS_STOCK_YES => '是',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_final_id', 'goods_id', 'type', 'number', 'relevance_id', 'is_purchase', 'is_deleted'
            , 'order_purchase_id', 'is_stock'], 'integer'],
            [['updated_at', 'created_at', 'fixed_number'], 'safe'],
            [['order_purchase_sn', 'goods_number', 'order_sn', 'serial'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'order_id'          => '订单ID',
            'order_sn'          => '订单号',
            'order_final_id'    => '最终订单ID',
            'order_purchase_id' => '采购订单ID',
            'order_purchase_sn' => '采购订单号',
            'goods_id'          => '零件ID',
            'goods_number'      => '零件号',
            'type'              => '关联类型  0询价  1库存',
            'number'            => '采购数量',
            'relevance_id'      => '关联ID（询价或库存）',
            'is_purchase'       => '采购',
            'is_deleted'        => '是否删除：0未删除 1已删除',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        unset($this->goods_number);

        return parent::beforeSave($insert);
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

    public function getOrderPurchase()
    {
        return $this->hasOne(OrderPurchase::className(), ['id' => 'order_purchase_id']);
    }

    //库存日志
    public function getStockLog()
    {
        return $this->hasOne(StockLog::className(), ['goods_id' => 'goods_id', 'order_id' => 'order_id', 'order_purchase_id' => 'order_purchase_id']);
    }
}
