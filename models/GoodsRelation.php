<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "goods_relation".
 *
 * @property int $id
 * @property int $p_goods_id 父零件ID
 * @property int $goods_id 零件ID
 * @property int $number 数量
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class GoodsRelation extends \yii\db\ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['p_goods_id', 'goods_id', 'number', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'p_goods_id' => '父零件ID',
            'goods_id' => '零件ID',
            'number' => '数量',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * {@inheritdoc}
     * @return GoodsRelationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoodsRelationQuery(get_called_class());
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    /**
     * @param $goods_id 当前goods_id
     * @param $goods_mutex 互斥数组
     */
    public static function goodsMutex($goods_id, $goods_mutex)
    {
        //查询子级
        $data = self::find()->select(['goods_id'])->where(['is_deleted' => 0, 'p_goods_id' => $goods_id])->asArray()->all();
        if (empty($data)) {
            return false;
        }
        foreach ($data as $item) {
            if (in_array($item['goods_id'], $goods_mutex)) {
                return true;
            }
            $goods_mutex[] = $item['goods_id'];
        }
        foreach ($data as $item) {
            return self::goodsMutex($item['goods_id'], $goods_mutex);
        }
        return false;
    }
}
