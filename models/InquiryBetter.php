<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%inquiry}}".
 *
 * @property int $id 自增id
 * @property string $good_id 厂家号
 * @property int $supplier_id 供应商ID
 * @property string $supplier_name 供应商名称
 * @property string $inquiry_price 咨询价格
 * @property string $inquiry_datetime 咨询时间
 * @property int $sort 排序
 * @property int $is_better 是否优选：0否 1是
 * @property int $is_newest 是否最新询价：0否 1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class InquiryBetter extends Inquiry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'is_priority'], 'integer'],
            [['price', 'tax_rate', 'tax_price'], 'number'],
            [['updated_at', 'created_at', 'offer_date'], 'safe'],
            [['good_id', 'inquiry_datetime', 'remark'], 'string', 'max' => 255],
            [
                ['good_id', 'supplier_id', 'inquiry_datetime'],
                'required',
                'on' => 'inquiry'
            ],
            [['price', 'tax_rate', 'tax_price'], 'double', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'good_id'          => '零件ID',
            'goods_number'     => '厂家号',
            'supplier_id'      => '供应商ID',
            'supplier_name'    => '供应商名称',
            'price'            => '未税单价',
            'tax_price'        => '含税单价',
            'tax_rate'         => '税率',
            'inquiry_datetime' => '咨询时间',
            'offer_date'       => '交货日期',
            'remark'           => '询价备注',
            'sort'             => '排序',
            'is_better'        => '优选',
            'is_newest'        => '最新询价',
            'is_priority'      => '优先询价',
            'is_deleted'       => '删除：0未删除 1已删除',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',
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
}
