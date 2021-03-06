<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "brand".
 *
 * @property int $id
 * @property string $name 品牌商名称
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $name_all
 * @property string $intro
 * @property string $remark
 */
class Brand extends \yii\db\ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'name_all', 'intro', 'remark'], 'string', 'max' => 255],
            [['name'], 'unique'],
            ['name', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => '品牌名称缩写',
            'name_all'   => '品牌全称',
            'intro'      => '品牌简介',
            'remark'     => '品牌备注',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * {@inheritdoc}
     * @return BrandQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BrandQuery(get_called_class());
    }

    //获取列表
    public static function getList()
    {
        $list = static::find()->where(['is_deleted' => self::IS_DELETED_NO])->asArray()->all();

        return ArrayHelper::map($list, 'id', 'name');
    }
}
