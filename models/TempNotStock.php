<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_not_stock".
 *
 * @property int $id
 * @property string $goods_number 零件号
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class TempNotStock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temp_not_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at', 'created_at'], 'safe'],
            [['goods_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'goods_number'   => '零件号',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
