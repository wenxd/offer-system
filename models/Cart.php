<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "cart".
 *
 * @property int $id 自增id
 * @property int $inquiry_id 询价id
 * @property int $type 类型：0最新 1优选 2库存
 * @property int $number 购买数量
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Cart extends ActiveRecord
{
    const TYPE_NEW    = '0';
    const TYPE_BETTER = '1';
    const TYPE_STOCK  = '2';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }
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
    public function rules()
    {
        return [
            [['inquiry_id', 'type', 'number'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'inquiry_id' => '询价id',
            'type' => '类型：0最新 1优选 2库存',
            'number' => '购买数量',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
