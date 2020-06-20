<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_not_goods_b".
 *
 * @property int $id
 * @property int $goods_id 零件ID
 * @property string $goods_number 零件号
 * @property string $goods_number_b 厂家号
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class TempNotGoodsB extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temp_not_goods_b';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['goods_number', 'goods_number_b'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '零件ID',
            'goods_number' => '零件号',
            'goods_number_b' => '厂家号',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }
}
