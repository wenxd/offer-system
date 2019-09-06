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
    const IS_DELETED_NO          = '0';
    const IS_DELETED_YES         = '1';

    const TITLE_TAX              = 'tax';
    const TITLE_DELIVERY_TIME    = 'delivery';
    const TITLE_STOCK_DIRECTION  = 'direction';
    const TITLE_REGION           = 'region';
    const TITLE_HIGH_STOCK_RATIO = 'high_stock_ratio';
    const TITLE_LOW_STOCK_RATIO  = 'low_stock_ratio';

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
            'title'      => '配置参数',
            'value'      => '配置参数值',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public static $config = [
        self::TITLE_TAX              => '税率',
        self::TITLE_DELIVERY_TIME    => '货期',
        self::TITLE_STOCK_DIRECTION  => '出库去向',
        self::TITLE_REGION           => '区块',
        self::TITLE_HIGH_STOCK_RATIO => '高储系数',
        self::TITLE_LOW_STOCK_RATIO  => '低储系数',
    ];

    public static function getList()
    {
        $directionList = SystemConfig::find()->where(['title' => SystemConfig::TITLE_STOCK_DIRECTION])->all();
        $list = [];
        foreach ($directionList as $item) {
            $list[$item->id] = $item['value'];
        }
        return $list;
    }

    //获取区块
    public static function getRegionList()
    {
        $directionList = SystemConfig::find()->where(['title' => SystemConfig::TITLE_REGION])->all();
        $list = [];
        foreach ($directionList as $item) {
            $list[$item->id] = $item['value'];
        }
        return $list;
    }
}
