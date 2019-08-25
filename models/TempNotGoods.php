<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_not_goods".
 *
 * @property int $id
 * @property int $goods_id 零件ID
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
            [['goods_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
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
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
