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
     * 互斥子零件
     * @param $goods_id 当前goods_id
     * @param $goods_mutex 互斥数组
     */
    public static function goodsMutex($goods_id, $goods_mutex)
    {
        //查询子级
        $data = self::find()->select(['goods_id'])->where(['is_deleted' => GoodsRelation::IS_DELETED_YES, 'p_goods_id' => $goods_id])->asArray()->all();
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

    /**
     * 获取最低级零件数据
     */
    public static function getGoodsSon($goods, $info = [])
    {
        //查询子级
        $data = self::find()
            ->select(['goods.*', 'goods_relation.number', 'goods_relation.p_goods_id'])
            ->where(['goods_relation.is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $goods['id']])
            ->join('LEFT JOIN', Goods::tableName(), 'goods.id=goods_relation.goods_id')
            ->asArray()->all();
        foreach ($data as $item) {
            $item['sum'] = $item['number'] * $goods['sum'];
            $item['info'][$goods['goods_number']] = $item['sum'];
            if ($item['is_assembly'] == Goods::IS_ASSEMBLY_YES) {
                $info = GoodsRelation::getGoodsSon($item, $info);
            } else {
                if (isset($info[$item['id']])) {
                    $item['info'] = $info[$item['id']]['info'];
                    $item['info'][$goods['goods_number']] = $item['sum'];
                    $item['sum'] += $info[$item['id']]['sum'];
                }
                $info[$item['id']] = $item;
            }
        }
        return $info;

    }
}
