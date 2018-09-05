<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "order_inquiry".
 *
 * @property int $id 自增id
 * @property string $order_id 订单编号
 * @property string $description 描述
 * @property string $quote_price 咨询价格
 * @property string $remark 备注
 * @property string $record_ids 询价id列表 json
 * @property string $stocks 库存id列表 json
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $provide_date 供货日期
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class OrderInquiry extends ActiveRecord
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
        return 'order_inquiry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quote_price'], 'number'],
            [['record_ids', 'customer_id'], 'required'],
            [['record_ids'], 'string'],
            [['is_deleted'], 'integer'],
            [['provide_date', 'updated_at', 'created_at'], 'safe'],
            [['order_id', 'description', 'remark', 'stocks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => '自增id',
            'customer_id'     => '客户名称',
            'customer_name'   => '客户名称',
            'order_id'        => '订单编号',
            'description'     => '描述',
            'quote_price'     => '咨询价格',
            'remark'          => '备注',
            'record_ids'      => '询价id列表 json',
            'stocks'          => '库存id列表 json',
            'is_deleted'      => '是否删除：0未删除 1已删除',
            'status'          => '是否询价',
            'provide_date'    => '供货日期',
            'updated_at'      => '更新时间',
            'created_at'      => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        $record = QuoteRecord::find()->where(['order_quote_id' => $this->id, 'status' => QuoteRecord::STATUS_NO])->one();
        if (!$record) {
            $this->status = self::STATUS_YES;
        }
        return parent::beforeSave($insert);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
