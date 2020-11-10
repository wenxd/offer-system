<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%stock}}".
 *
 * @property int $id 自增id
 * @property string $good_id 厂家号
 * @property int $supplier_id 供应商ID
 * @property string $supplier_name 供应商名称
 * @property string $price 价格
 * @property string $position 库存位置
 * @property int $number 库存数量
 * @property int $sort 排序
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $tax_rate  税率
 * @property string $tax_price
 * @property string $suggest_number
 * @property string $high_number
 * @property string $low_number
 * @property string $temp_number
 */
class Stock extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_EMERG_NO    = '0';
    const IS_EMERG_YES   = '1';

    public static $emerg = [
        self::IS_EMERG_NO  => '否',
        self::IS_EMERG_YES => '是',
    ];

    public static $zero = [
        '1' => '是',
        '2' => '否'
    ];

    public $description;
    public $description_en;
    public $is_zero;

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
        return '{{%stock}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['good_id', 'supplier_id', 'number', 'sort', 'is_deleted', 'is_emerg', 'is_zero', 'temp_number'], 'integer'],
            [['price', 'tax_rate', 'tax_price'], 'number'],
            [['updated_at', 'created_at'], 'safe'],
            [['position', 'description', 'description_en'], 'string', 'max' => 255],
            [
                ['good_id', 'price', 'position', 'number'],
                'required',
                'on' => 'stock'
            ],
            [['price', 'tax_rate', 'tax_price'], 'double', 'min' => 0],
            [['number', 'suggest_number', 'high_number', 'low_number'], 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'good_id'          => '零件ID',
            'goods_number'     => '零件号',
            'description'      => '中文描述',
            'description_en'   => '英文描述',
            'material_code'    => '设备类别',
            'supplier_id'      => '供应商ID',
            'supplier_name'    => '供应商名称',
            'price'            => '未税单价',
            'tax_price'        => '含税单价',
            'tax_rate'         => '税率',
            'position'         => '库存位置',
            'number'           => '库存数量',
            'suggest_number'   => '建议库存',
            'high_number'      => '高储',
            'low_number'       => '低储',
            'sort'             => '排序',
            'is_emerg'         => '紧急',
            'is_zero'          => '是否有库存',
            'is_deleted'       => '是否删除：0未删除 1已删除',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',
            'temp_number'       => '临时库存数量',
        ];
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public static function getAllMoney()
    {
        $list = self::find()->select('tax_price, number')->where(['is_deleted' => self::IS_DELETED_NO])->asArray()->all();
        $allMoney = 0;
        foreach ($list as $item) {
            $allMoney += $item['tax_price'] * $item['number'];
        }
        return $allMoney;
    }

    /**
     * 根据goods_id计算临时库存
     */
    public static function countTempNumber($goods_ids)
    {
        $models = self::find()->where(['good_id' => $goods_ids, 'is_deleted' => self::IS_DELETED_NO])->all();
        foreach ($models as $model) {
            $occupy_number = $model->occupy['number'] ?? 0;
            $temp_number = $model->number - $occupy_number;
            if ($temp_number != $model->temp_number) {
                $model->temp_number = $temp_number >= 0 ? $temp_number : 0;
                $model->save();
            }
        }
    }

    /**
     * 关联订单使用库存表查询已占用库存
     */
    public function getOccupy()
    {
        return $this->hasOne(AgreementStock::className(), ['goods_id' => 'good_id'])
            ->select('SUM(use_number) AS number')
            ->where(['is_stock' => 0, 'is_confirm' => 1])
            ->andWhere(['>', 'stock_number', 0])->asArray()->one();
    }
}
