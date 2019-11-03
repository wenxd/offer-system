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

    const TITLE_TAX                     = 'tax';
    const TITLE_DELIVERY_TIME           = 'delivery';
    const TITLE_STOCK_SOURCE            = 'source';
    const TITLE_STOCK_DIRECTION         = 'direction';
    const TITLE_REGION                  = 'region';
    const TITLE_HIGH_STOCK_RATIO        = 'high_stock_ratio';
    const TITLE_LOW_STOCK_RATIO         = 'low_stock_ratio';
    const TITLE_PAYMENT_RATIO           = 'payment_ratio';
    const TITLE_QUOTE_PRICE_RATIO       = 'quote_price_ratio';
    const TITLE_QUOTE_DELIVERY_RATIO    = 'quote_delivery_ratio';
    const TITLE_COMPETITOR_RATIO        = 'competitor_ratio';

    public static $config = [
        self::TITLE_TAX                     => '税率',
        self::TITLE_DELIVERY_TIME           => '货期',
        self::TITLE_STOCK_SOURCE            => '入库来源',
        self::TITLE_STOCK_DIRECTION         => '出库去向',
        self::TITLE_REGION                  => '区块',
        self::TITLE_HIGH_STOCK_RATIO        => '高储系数',
        self::TITLE_LOW_STOCK_RATIO         => '低储系数',
        self::TITLE_PAYMENT_RATIO           => '预付款比例',
        self::TITLE_QUOTE_PRICE_RATIO       => '报价系数',
        self::TITLE_QUOTE_DELIVERY_RATIO    => '货期系数',
        self::TITLE_COMPETITOR_RATIO        => '竞争者系数',
    ];

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

    public function beforeSave($insert)
    {
        if ($insert && $this->title == self::TITLE_TAX) {
            $res = self::find()->where(['title' => self::TITLE_TAX])->one();
            if ($res) {
                $this->addError('id', '税率只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_QUOTE_PRICE_RATIO) {
            $res = self::find()->where(['title' => self::TITLE_QUOTE_PRICE_RATIO])->one();
            if ($res) {
                $this->addError('id', '报价系数只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_DELIVERY_TIME) {
            $res = self::find()->where(['title' => self::TITLE_DELIVERY_TIME])->one();
            if ($res) {
                $this->addError('id', '货期只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_QUOTE_DELIVERY_RATIO) {
            $res = self::find()->where(['title' => self::TITLE_QUOTE_DELIVERY_RATIO])->one();
            if ($res) {
                $this->addError('id', '货期系数只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_PAYMENT_RATIO) {
            $res = self::find()->where(['title' => self::TITLE_PAYMENT_RATIO])->one();
            if ($res) {
                $this->addError('id', '预付款比例只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_LOW_STOCK_RATIO) {
            $res = self::find()->where(['title' => self::TITLE_LOW_STOCK_RATIO])->one();
            if ($res) {
                $this->addError('id', '低储系数只能添加一个');
                return false;
            }
        }
        if ($insert && $this->title == self::TITLE_HIGH_STOCK_RATIO) {
            $res = self::find()->where(['title' => self::TITLE_HIGH_STOCK_RATIO])->one();
            if ($res) {
                $this->addError('id', '高储系数只能添加一个');
                return false;
            }
        }
        return parent::beforeSave($insert);
    }

    public static function getList($title = SystemConfig::TITLE_STOCK_DIRECTION)
    {
        $directionList = SystemConfig::find()->where(['title' => $title])->all();
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
