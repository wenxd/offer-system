<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_config".
 *
 * @property int $id
 * @property string $title 配置名称
 * @property string $value 配置参数值
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class SystemConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'title'      => '配置名称',
            'value'      => '配置参数值',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    const TITLE_TAX = 'tax';

    public static $config = [
        self::TITLE_TAX => '税率',
    ];
}
