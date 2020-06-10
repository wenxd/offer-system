<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_order_inquiry".
 *
 * @property int $id
 * @property string $goods_ids 零件id 多个用,分开
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class TempOrderInquiry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temp_order_inquiry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_ids'], 'string'],
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
            'goods_ids' => '零件id 多个用,分开',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
