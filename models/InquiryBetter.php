<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%inquiry}}".
 *
 * @property int $id 自增id
 * @property string $good_id 零件号
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
            [['inquiry_price'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['good_id', 'inquiry_datetime'], 'string', 'max' => 255],
            [
                ['good_id', 'supplier_id', 'inquiry_datetime'],
                'required',
                'on' => 'inquiry'
            ],
            [['inquiry_price'], 'double', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'good_id'          => '零件号',
            'goods_number'     => '零件号',
            'supplier_id'      => '供应商ID',
            'supplier_name'    => '供应商名称',
            'inquiry_price'    => '咨询价格',
            'inquiry_datetime' => '咨询时间',
            'sort'             => '排序',
            'is_better'        => '是否优选',
            'is_newest'        => '是否最新询价',
            'is_priority'      => '是否优先询价',
            'is_deleted'       => '是否删除：0未删除 1已删除',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        $date = $this->inquiry_datetime;
        $isHasNew = self::find()->where(['good_id' => $this->good_id])->andWhere(" inquiry_datetime >= '$date' ")->one();

        if (!$isHasNew) {
            self::updateAll(['is_newest' => self::IS_NEWEST_NO], ['good_id' => $this->good_id]);
            $this->is_newest = self::IS_NEWEST_YES;
        }

        return parent::beforeSave($insert);
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
