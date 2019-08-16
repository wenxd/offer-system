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
 * @property int $goods_id 零件编号
 * @property int $number 库存数量
 * @property int $type 入库出库 0入库  1出库
 * @property string $operate_time 操作时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $payment_sn
 * @property string $remark
 */
class StockLog extends ActiveRecord
{
    public $price;
    public $goods_number;

    const TYPE_IN    = '1';
    const TYPE_OUT   = '2';

    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

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
            [['order_id', 'order_payment_id', 'goods_id', 'number', 'type', 'is_deleted'], 'integer'],
            [['operate_time', 'updated_at', 'created_at', 'goods_number', 'remark'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => '自增id',
            'order_id'          => '订单ID',
            'order_sn'          => '订单编号',
            'order_payment_id'  => '支出合同单ID',
            'payment_sn'        => '支出合同单号',
            'goods_id'          => '零件ID',
            'goods_number'      => '零件编号',
            'number'            => '数量',
            'type'              => '入库出库 0入库  1出库',
            'operate_time'      => '操作时间',
            'is_deleted'        => '是否删除：0未删除 1已删除',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
            'remark'            => '备注说明',
            'price'             => '价格',
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
}
