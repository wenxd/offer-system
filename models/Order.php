<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "order".
 *
 * @property int $id 自增id
 * @property int $customer_id 客户ID
 * @property string $order_id 订单编号
 * @property string $description 描述
 * @property string $order_price 订单价格  报价金额
 * @property string $remark 备注
 * @property int $type 订单类型 0报价单 1询价单 10最终询价单
 * @property int $status 是否询价：0未询价 1已询价
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $provide_date 供货日期
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Order extends ActiveRecord
{
    const STATUS_NO   = '0';
    const STATUS_YES  = '1';

    public static $status = [
        self::STATUS_NO     => '未询价',
        self::STATUS_YES => '已询价',
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
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'type', 'status', 'is_deleted'], 'integer'],
            [['order_price'], 'number'],
            [['status'], 'required'],
            [['provide_date', 'updated_at', 'created_at'], 'safe'],
            [['order_id', 'description', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => '自增id',
            'customer_id'     => '客户ID',
            'customer_name'   => '客户名称',
            'order_id'        => '订单编号',
            'description'     => '描述',
            'order_price'     => '订单价格  报价金额',
            'remark'          => '备注',
            'type'            => '订单类型 0报价单 1询价单 10最终询价单',
            'status'          => '是否询价：0未询价 1已询价',
            'is_deleted'      => '是否删除：0未删除 1已删除',
            'provide_date'    => '供货日期',
            'updated_at'      => '更新时间',
            'created_at'      => '创建时间',
        ];
    }
}
