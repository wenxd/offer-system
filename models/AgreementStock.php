<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "agreement_stock".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $order_agreement_id 收入合同订单ID
 * @property string $order_agreement_sn 收入合同订单号
 * @property int $order_purchase_id 采购单ID
 * @property string $order_purchase_sn 采购单号
 * @property int $order_payment_id 支出合同订单ID
 * @property string $order_payment_sn 支出合同订单号
 * @property int $goods_id 零件ID
 * @property string $price 单价
 * @property string $tax_price 含税单价
 * @property int $use_number 使用库存数量
 * @property string $all_price 未税总价
 * @property string $all_tax_price 含税总价
 * @property string $updated_at
 * @property string $created_at
 * @property string $is_confirm
 * @property string $confirm_at
 * @property string $admin_id
 * @property string $is_stock
 * @property string $temp_number
 * @property string $stock_number
 */
class AgreementStock extends \yii\db\ActiveRecord
{
    const IS_CONFIRM_NO  = '0';
    const IS_CONFIRM_YES = '1';
    const IS_CONFIRM_REJECT = '4';

    public static $confirm = [
        self::IS_CONFIRM_NO  => '否',
        self::IS_CONFIRM_YES => '是',
        self::IS_CONFIRM_REJECT => '驳回'
    ];
    public static $stock = [
        self::IS_CONFIRM_NO  => '否',
        self::IS_CONFIRM_YES => '是',
    ];

    // 来源source
    const STRATEGY = 'strategy';
    const PURCHASE = 'purchase';
    const PAYMENT = 'payment';

    public static $source = [
        self::STRATEGY  => '采购策略',
        self::PURCHASE  => '采购订单',
        self::PAYMENT => '支出合同'
    ];

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
        return 'agreement_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_agreement_id', 'order_purchase_id', 'order_payment_id', 'goods_id', 'use_number',
                'is_confirm', 'is_stock', 'stock_number'], 'integer'],
            [['price', 'tax_price', 'all_price', 'all_tax_price'], 'number'],
            [['confirm_at', 'admin_id'], 'safe'],
            [['order_agreement_sn', 'order_purchase_sn', 'order_payment_sn', 'source'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'order_id'              => '订单ID',
            'order_agreement_id'    => '收入合同订单ID',
            'order_agreement_sn'    => '收入合同订单号',
            'order_purchase_id'     => '采购单ID',
            'order_purchase_sn'     => '采购单号',
            'order_payment_id'      => '支出合同订单ID',
            'order_payment_sn'      => '支出合同订单号',
            'goods_id'              => '零件ID',
            'price'                 => '单价',
            'tax_price'             => '含税单价',
            'use_number'            => '使用库存数量',
            'all_price'             => '未税总价',
            'all_tax_price'         => '含税总价',
            'updated_at'            => '更新时间',
            'created_at'            => '更新时间',
            'is_confirm'            => '是否确认',
            'admin_id'              => '操作人',
            'source'              => '来源',
            'is_stock'              => '是否出库',
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

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }
}
