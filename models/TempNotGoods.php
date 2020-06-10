<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_not_goods".
 *
 * @property int $id
 * @property int $goods_number 零件号
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class TempNotGoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temp_not_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at', 'created_at', 'goods_number'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'goods_number'  => '零件号',
            'updated_at'    => '更新时间',
            'created_at'    => '创建时间',
        ];
    }
}
