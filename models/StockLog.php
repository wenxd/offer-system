<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "stock_log".
 *
 * @property int $id 自增id
 * @property int $order_id 订单ID
 * @property int $order_payment_id 支出合同单ID
 * @property string $payment_sn 支出合同单号
 * @property int $order_agreement_id 收入合同单ID
 * @property string $agreement_sn 收入合同单号
 * @property int $order_purchase_id 采购单ID
 * @property int $purchase_sn 采购单号
 * @property int $goods_id 零件编号
 * @property int $number 库存数量
 * @property int $type 入库出库 1入库  2出库
 * @property string $operate_time 操作时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $remark 出入库备注
 * @property int $admin_id 操作人ID
 * @property int $is_manual 是否手动出入库 0否 1是
 * @property int $direction
 * @property int $customer_id
 * @property int $region
 * @property int $plat_name
 */
class StockLog extends ActiveRecord
{
    public $price;
    public $goods_number;
    public $suggest_number;
    public $position;

    const TYPE_IN    = '1';
    const TYPE_OUT   = '2';

    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_MANUAL_NO   = '0';
    const IS_MANUAL_YES  = '1';

    public static $manual = [
        self::IS_MANUAL_NO  => '否',
        self::IS_MANUAL_YES => '是'
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
        return 'stock_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_payment_id', 'order_agreement_id', 'order_purchase_id', 'purchase_sn', 'goods_id',
                'number', 'type', 'is_deleted', 'admin_id', 'is_manual', 'customer_id'], 'integer'],
            [['operate_time', 'updated_at', 'created_at', 'goods_number', 'direction'], 'safe'],
            [['payment_sn', 'agreement_sn', 'remark', 'region', 'plat_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => '自增id',
            'order_id'              => '订单ID',
            'order_payment_id'      => '支出合同单ID',
            'payment_sn'            => '支出合同单号',
            'order_agreement_id'    => '收入合同单ID',
            'agreement_sn'          => '收入合同单号',
            'order_purchase_id'     => '采购单ID',
            'purchase_sn'           => '采购单号',
            'goods_id'              => '零件编号',
            'number'                => '库存数量',
            'type'                  => '入库出库 1入库  2出库',
            'operate_time'          => '操作时间',
            'is_deleted'            => '是否删除：0未删除 1已删除',
            'updated_at'            => '更新时间',
            'created_at'            => '创建时间',
            'remark'                => '备注',
            'admin_id'              => '操作人ID',
            'is_manual'             => '手动',
            'direction'             => '去向',
            'price'                 => '价格',
            'customer_id'           => '客户',
            'region'                => '区块',
            'plat_name'             => '平台名称',
            'suggest_number'        => '建议库存',
            'position'              => '库存位置',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderPayment()
    {
        return $this->hasOne(OrderPayment::className(), ['id' => 'order_payment_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }

    public function getSystem()
    {
        return $this->hasOne(Stock::className(), ['id' => 'direction']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
