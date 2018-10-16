<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stock_log".
 *
 * @property int $id 自增id
 * @property int $order_id 订单ID
 * @property int $order_purchase_id 采购单ID
 * @property int $goods_id 零件编号
 * @property int $number 库存数量
 * @property int $type 入库出库 0入库  1出库
 * @property string $operate_time 操作时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class StockLog extends \yii\db\ActiveRecord
{
    const TYPE_IN    = '1';
    const TYPE_OUT   = '2';

    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

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
            [['order_id', 'order_purchase_id', 'goods_id', 'number', 'type', 'is_deleted'], 'integer'],
            [['operate_time', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'order_id' => '订单ID',
            'order_purchase_id' => '采购单ID',
            'goods_id' => '零件编号',
            'number' => '库存数量',
            'type' => '入库出库 0入库  1出库',
            'operate_time' => '操作时间',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
