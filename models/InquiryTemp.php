<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inquiry_temp".
 *
 * @property int $id 自增id
 * @property int $good_id 零件ID
 * @property int $supplier_id 供应商ID
 * @property string $price 未税价格
 * @property string $tax_price 含税价格
 * @property string $tax_rate 税率
 * @property string $all_tax_price 含税总价
 * @property string $all_price 未税总价
 * @property int $number 询价数量
 * @property string $inquiry_datetime 咨询时间
 * @property int $sort 排序
 * @property int $is_better 是否优选：0否 1是
 * @property int $is_newest 是否最新询价：0否 1是
 * @property int $is_priority 是否优先询价： 0否 1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $offer_date 交货日期
 * @property string $remark 备注
 * @property string $better_reason 优选理由
 * @property int $delivery_time 货期  （天）
 * @property int $admin_id 询价员ID
 * @property int $order_id 订单ID
 * @property int $order_inquiry_id 询价单ID
 * @property int $inquiry_goods_id 询价零件表ID
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property int $is_upload 是否导入 0否 1是
 */
class InquiryTemp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inquiry_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['good_id', 'supplier_id', 'number', 'sort', 'is_better', 'is_newest', 'is_priority', 'is_deleted', 'delivery_time', 'admin_id', 'order_id', 'order_inquiry_id', 'inquiry_goods_id', 'is_upload'], 'integer'],
            [['price', 'tax_price', 'tax_rate', 'all_tax_price', 'all_price'], 'number'],
            [['offer_date', 'updated_at', 'created_at'], 'safe'],
            [['inquiry_datetime', 'remark', 'better_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => '自增id',
            'good_id'           => '零件ID',
            'supplier_id'       => '供应商ID',
            'price'             => '未税价格',
            'tax_price'         => '含税单价',
            'tax_rate'          => '税率',
            'all_tax_price'     => '含税总价',
            'all_price'         => '未税总价',
            'number'            => '询价数量',
            'inquiry_datetime'  => '咨询时间',
            'sort'              => '排序',
            'is_better'         => '是否优选',
            'is_newest'         => '是否最新',
            'is_priority'       => '是否优先',
            'is_deleted'        => '是否删除：0未删除 1已删除',
            'offer_date'        => '交货日期',
            'remark'            => '备注',
            'better_reason'     => '优选理由',
            'delivery_time'     => '货期（周）',
            'admin_id'          => '询价员ID',
            'order_id'          => '订单ID',
            'order_inquiry_id'  => '询价单ID',
            'inquiry_goods_id'  => '询价零件表ID',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
            'is_upload'         => '是否导入 0否 1是',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderInquiry()
    {
        return $this->hasOne(OrderInquiry::className(), ['id' => 'order_inquiry_id']);
    }
}
