<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "competitor_goods".
 *
 * @property int $id 自增id
 * @property int $goods_id 商品ID
 * @property int $competitor_id 竞争对手ID
 * @property string $offer_date 报价时间
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class CompetitorGoods extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    public $goods_number;
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
        return 'competitor_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'competitor_id', 'is_deleted', 'customer', 'number'], 'integer'],
            [['tax_rate', 'price', 'tax_price'], 'number'],
            [['offer_date', 'updated_at', 'created_at'], 'safe'],
            [['price', 'tax_price'], 'double', 'min' => 0],
            [['competitor_id', 'price', 'offer_date'], 'required', 'on' => 'competitor_goods'],
            [['remark', 'unit', 'goods_number'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => '自增id',
            'goods_id'         => '零件ID',
            'goods_number'     => '零件号',
            'competitor_id'    => '竞争对手ID',
            'competitor_name'  => '竞争对手名称',
            'price'            => '未税价格',
            'tax_price'        => '含税价格',
            'tax_rate'         => '税率',
            'number'           => '数量',
            'unit'             => '单位',
            'remark'           => '备注',
            'offer_date'       => '报价时间',
            'is_deleted'       => '是否删除：0未删除 1已删除',
            'updated_at'       => '更新时间',
            'created_at'       => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->goods_number) {
            $this->addError('id', '请输入零件号');
            return false;
        }
        $goods = Goods::findOne(['goods_number' => $this->goods_number, 'is_deleted' => Goods::IS_DELETED_NO]);
        if (!$goods) {
            $this->addError('id', '没有此厂家号');
            return false;
        }
        $this->goods_id = $goods->id;
        return parent::beforeSave($insert);
    }
    public function afterFind()
    {
        if ($this->goods_id) {
            $this->goods_number = $this->goods->goods_number;
        }
    }
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }

    public function getCustomers()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer']);
    }
}
