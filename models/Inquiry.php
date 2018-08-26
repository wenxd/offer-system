<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "{{%inquiry}}".
 *
 * @property int $id 自增id
 * @property string $good_id 零件编号
 * @property int $supplier_id 供应商ID
 * @property string $supplier_name 供应商名称
 * @property string $inquiry_price 咨询价格
 * @property string $inquiry_datetime 咨询时间
 * @property int $sort 排序
 * @property int $is_better 是否优选：0否 1是
 * @property int $is_newest 是否最新询价：0否 1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Inquiry extends ActiveRecord
{
    const IS_DELETED_NO   = '0';
    const IS_DELETED_YES  = '1';

    const IS_BETTER_NO    = '0';
    const IS_BETTER_YES   = '1';

    const IS_NEWEST_NO    = '0';
    const IS_NEWEST_YES   = '1';

    const IS_PRIORITY_NO  = '0';
    const IS_PRIORITY_YES = '1';

    public static $newest = [
        self::IS_NEWEST_NO   => '否',
        self::IS_NEWEST_YES  => '是',
    ];

    public static $better = [
        self::IS_BETTER_NO   => '否',
        self::IS_BETTER_YES  => '是',
    ];

    public static $priority = [
        self::IS_PRIORITY_NO   => '否',
        self::IS_PRIORITY_YES  => '是',
    ];
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
    public static function tableName()
    {
        return 'inquiry';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'is_priority'], 'integer'],
            [['inquiry_price'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['good_id', 'supplier_name', 'inquiry_datetime'], 'string', 'max' => 255],
            [
                ['good_id', 'supplier_id', 'inquiry_datetime'],
                'required',
                'on' => 'inquiry'
            ],
            [['inquiry_price'], 'double', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'good_id'          => '零件编号',
            'supplier_id'      => '供应商ID',
            'supplier_name'    => '供应商名称',
            'inquiry_price'    => '咨询价格',
            'inquiry_datetime' => '咨询时间',
            'sort'             => '排序',
            'is_better'        => '是否优选',
            'is_newest'        => '是否最新询价',
            'is_priority'      => '是否优先询价',
            'is_deleted'       => '是否删除：0未删除 1已删除',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->supplier_id) {
            $this->supplier_name = Supplier::getCreateDropDown()[$this->supplier_id];
        }

        $date = $this->inquiry_datetime;
        $isHasNew = self::find()->where(['good_id' => $this->good_id])->andWhere(" inquiry_datetime >= '$date' ")->one();

        if (!$isHasNew) {
            self::updateAll(['is_newest' => self::IS_NEWEST_NO], ['good_id' => $this->good_id]);
            $this->is_newest = self::IS_NEWEST_YES;
        }

        return parent::beforeSave($insert);
    }
}
