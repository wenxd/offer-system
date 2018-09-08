<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "competitor".
 *
 * @property int $id 自增id
 * @property string $name 竞争对手名称
 * @property string $mobile 竞争对手电话
 * @property string $telephone 竞争对手座机
 * @property string $email 竞争对手邮箱
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Competitor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'competitor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'mobile', 'telephone', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'name' => '竞争对手名称',
            'mobile' => '竞争对手电话',
            'telephone' => '竞争对手座机',
            'email' => '竞争对手邮箱',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
