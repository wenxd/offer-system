<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temp_order_goods".
 *
 * @property int $id
 * @property string $serial 序号
 * @property int $goods_id 零件ID
 * @property int $number 数量
 * @property string $token 每次上传的一批零件统一为一个token
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class TempOrderGoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temp_order_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'number'], 'integer'],
            [['serial', 'updated_at', 'created_at'], 'safe'],
            [['token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serial' => '序号',
            'goods_id' => '零件ID',
            'number' => '数量',
            'token' => '每次上传的一批零件统一为一个token',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }
}
