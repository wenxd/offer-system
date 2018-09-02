<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%stock}}".
 *
 * @property int $id 自增id
 * @property string $good_id 零件编号
 * @property int $supplier_id 供应商ID
 * @property string $supplier_name 供应商名称
 * @property string $price 价格
 * @property string $position 库存位置
 * @property int $number 库存数量
 * @property int $sort 排序
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Stock extends ActiveRecord
{
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
        return '{{%stock}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'number', 'sort', 'is_deleted'], 'integer'],
            [['price'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['good_id', 'position'], 'string', 'max' => 255],
            [
                ['good_id', 'price', 'position', 'number'],
                'required',
                'on' => 'stock'
            ],
            [['price'], 'double', 'min' => 0],
            [['number'], 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => '自增id',
            'good_id'       => '零件ID',
            'goods_number'  => '零件编号',
            'supplier_id'   => '供应商ID',
            'supplier_name' => '供应商名称',
            'price'         => '价格',
            'position'      => '库存位置',
            'number'        => '库存数量',
            'sort'          => '排序',
            'is_deleted'    => '是否删除：0未删除 1已删除',
            'updated_at'    => '更新时间',
            'created_at'    => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
//        if ($this->supplier_id) {
//            $this->supplier_name = Supplier::getCreateDropDown()[$this->supplier_id];
//        }
        return parent::beforeSave($insert);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }
}
