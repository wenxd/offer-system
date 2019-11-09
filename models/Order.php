<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order".
 *
 * @property int $id 自增id
 * @property int $customer_id 客户ID
 * @property string $order_sn 订单编号
 * @property string $description 描述
 * @property string $order_price 订单价格  报价金额
 * @property string $remark 备注
 * @property int $type 订单类型 0报价单 1询价单 10最终询价单
 * @property int $order_type 是否项目订单 0否 1是
 * @property int $status 是否询价：0未询价 1已询价
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $provide_date 供货日期
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $goods_ids 创建时间
 * @property string $is_final
 * @property string $is_dispatch
 */
class Order extends ActiveRecord
{
    const TYPE_QUOTE    = '1';
    const TYPE_INQUIRY  = '2';
    const TYPE_FINAL    = '10';
    const TYPE_PURCHASE = '11';

    const STATUS_NO   = '0';
    const STATUS_YES  = '1';

    const ORDER_TYPE_PROJECT_NO  = 0;
    const ORDER_TYPE_PROJECT_YES = 1;

    const IS_FINAL_NO  = '0';
    const IS_FINAL_YES = '1';

    const IS_DISPATCH_NO  = '0';
    const IS_DISPATCH_YES = '1';

    public static $status = [
        self::STATUS_NO  => '未询价',
        self::STATUS_YES => '已询价',
    ];

    public static $type = [
        self::TYPE_QUOTE      => '报价单',
        self::TYPE_INQUIRY    => '询价单',
        self::TYPE_FINAL      => '最终报价单',
        self::TYPE_PURCHASE   => '采购单',
    ];

    public static $orderType = [
        self::ORDER_TYPE_PROJECT_YES => '项目订单',
        self::ORDER_TYPE_PROJECT_NO  => '非项目订单',
    ];

    public static $final = [
        self::IS_FINAL_NO  => '否',
        self::IS_FINAL_YES => '是',
    ];

    public static $dispatch = [
        self::IS_DISPATCH_NO  => '否',
        self::IS_DISPATCH_YES => '是',
    ];

    public $is_inquiry;
    public $customer_short_name = '';

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
            [['customer_id', 'type', 'status', 'is_deleted', 'order_type', 'is_final', 'is_dispatch'], 'integer'],
            [['order_price'], 'number'],
            [['provide_date', 'updated_at', 'created_at'], 'safe'],
            [['goods_ids', 'order_sn', 'description', 'remark', 'manage_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'customer_id'     => '客户ID',
            'customer_name'   => '客户名称',
            'manage_name'     => '订单管理员名称',
            'order_sn'        => '订单编号',
            'description'     => '描述',
            'order_price'     => '订单总金额',
            'remark'          => '备注',
            'type'            => '订单类型',
            'status'          => '全部确认询价完成',
            'is_deleted'      => '是否删除：0未删除 1已删除',
            'provide_date'    => '报价截止日期',
            'updated_at'      => '更新时间',
            'created_at'      => '创建时间',
            'order_type'      => '订单来源',
            'is_final'        => '是否生成成本单',
            'is_dispatch'     => '是否全部派送询价员',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    //关联成本单
    public function getCost()
    {
        return $this->hasOne(OrderFinal::className(), ['order_id' => 'id']);
    }

    public static function getInquiry($order_id)
    {
        $orderGoodsList = OrderGoods::find()->where(['order_id' => $order_id])->indexBy('goods_id')->asArray()->all();
        $goods_ids = ArrayHelper::getColumn($orderGoodsList, 'goods_id');

        $inquiryList = Inquiry::find()->where(['good_id' => $goods_ids])->all();
        $inquiryListNew = ArrayHelper::index($inquiryList, null, 'good_id');

        return (count($orderGoodsList) == count($inquiryListNew)) ? '是' : '否';
    }
}
