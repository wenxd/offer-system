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
        $data = self::find()->select(['goods_id'])->where(['is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $goods_id])->asArray()->all();
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
            if ($item['is_assembly'] == Goods::IS_ASSEMBLY_YES) {
                $info = GoodsRelation::getGoodsSon($item, $info);
            } else {
                if (isset($info[$item['id']])) {
                    $item['sum'] += $info[$item['id']]['sum'];
                }
                $info[$item['id']] = $item;
            }
        }
        return $info;

    }

    /**
     * 获取最低级零件价格（项目）
     */
    public static function getGoodsSonPrice($goods, $info = [])
    {
        //查询子级
        $data = self::find()
            ->alias("relation")
//            ->select(['relation.number', 'relation.p_goods_id', 'goods.is_assembly', 'goods_number'])
            ->where(['relation.is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $goods['goods_id']])
            ->with('goods')
            ->with('finallow')
            ->with('inquirylow')
            ->groupBy('relation.goods_id')->all();
        foreach ($data as $item) {
            $number = $item['number'] * $goods['number'];
            $res = [
                'order_id' => $goods['order_id'], //'订单ID',
                'order_agreement_id' => $goods['order_agreement_id'], //'合同订单ID',
                'order_agreement_sn' => $goods['order_agreement_sn'], //'合同订单号',
                'order_quote_id' => $goods['order_quote_id'], //'报价ID',
                'order_quote_sn' => $goods['order_quote_sn'], //'报价单号',
                'serial' => $item->goods_id, //'序号',
                'goods_id' => $item->goods_id, //'零件ID',
                'tax_rate' => 0, //'税率',
                'price' => 0, //'单价',
                'type' => 0, //'关联类型  0询价  1库存',
                'relevance_id' => 0, //'关联ID（询价或库存）',
                'number' => $number, //'订单需求数量（原始数据，报价单生成收入合同而来）',
                'purchase_is_show' => 1, //'生成采购单的时候合并数据后不显示 1默认显示 0为不显示',
                'is_agreement' => 0, //'是否报价 0否 1是',
                'agreement_sn' => '', //'单条合同号',
                'purchase_date' => '', //'采购时间',
                'agreement_date' => '', //'采购时间',
                'inquiry_admin_id' => '', //'询价员ID',
                'is_out' => '', //'是否出库',
                'quote_delivery_time' => '', //'报价货期（周）',
                'delivery_time' => '', //'成本货期（周）',
                'is_quality' => 0, //'是否质检 0否 1是',
                'top_goods_number' => $goods['top_goods_number'],
            ];
            //询价单
            if (isset($item->inquirylow) && !empty($item->inquirylow)) {
                $inquirylow = $item->inquirylow;
                $res['tax_rate'] = $inquirylow->tax_rate;
                $res['price'] = $inquirylow->price;
                $res['inquiry_admin_id'] = $inquirylow->admin_id;
                $res['relevance_id'] = $inquirylow->id;
                $res['delivery_time'] = $inquirylow->delivery_time;
                $res['quote_delivery_time'] = $inquirylow->delivery_time;
            }
            //成本单
            if (isset($item->finallow->inquirylow) && !empty($item->finallow->inquirylow)) {
                $finallow = $item->finallow;
                $inquirylow = $finallow->inquirylow;
                //序号
                $res['serial'] = $finallow->serial;
                $res['tax_rate'] = $inquirylow->tax_rate;
                $res['price'] = $inquirylow->price;
                $res['inquiry_admin_id'] = $inquirylow->admin_id;
                $res['relevance_id'] = $inquirylow->id;
                $res['delivery_time'] = $finallow->delivery_time;
                $res['quote_delivery_time'] = $finallow->delivery_time;
            }
            if ($item->goods->is_assembly == Goods::IS_ASSEMBLY_YES) {
                $info = GoodsRelation::getGoodsSonPrice($res, $info);
            } else {
                $id = $item->goods_id;
                if (isset($info[$id])) {
                    $res['number'] += $info[$id]['number'];
                    if ($info[$id]['quote_delivery_time'] > $res['quote_delivery_time']) {
                        $res['quote_delivery_time'] = $info[$id]['quote_delivery_time'];
                    }
                }
                $res['order_number'] = $res['number'];
                $res['purchase_number'] = $res['number'];
                $info[$id] = $res;
            }
        }
        return $info;
    }

    /**
     * 获取最低级零件价格(非项目)
     */
    public static function getGoodsSonPriceFinal($goods, $info = [])
    {
        //查询子级
        $data = self::find()
            ->alias("relation")
//            ->select(['relation.number', 'relation.p_goods_id', 'goods.is_assembly', 'goods_number'])
            ->where(['relation.is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $goods['goods_id']])
            ->with('goods')
            ->with('finallow')
            ->with('inquirylow')
            ->groupBy('relation.goods_id')->all();
        foreach ($data as $item) {
            $number = $item['number'] * $goods['number'];
            $res = [
                'order_id' => $goods['order_id'], //'订单ID',
                'order_final_id' => $goods['order_final_id'], //'合同订单ID',
                'final_sn' => $goods['final_sn'], //'合同订单号',
                'key' => $goods['key'], //'合同订单号',
                'serial' => $item->goods_id, //'序号',
                'goods_id' => $item->goods_id, //'零件ID',
                'tax_rate' => 0, //'税率',
                'price' => 0, //'单价',
                'type' => 0, //'关联类型  0询价  1库存',
                'relevance_id' => 0, //'关联ID（询价或库存）',
                'number' => $number, //'订单需求数量（原始数据，报价单生成收入合同而来）',
                'purchase_is_show' => 1, //'生成采购单的时候合并数据后不显示 1默认显示 0为不显示',
                'is_agreement' => 0, //'是否报价 0否 1是',
                'agreement_sn' => '', //'单条合同号',
                'purchase_date' => '', //'采购时间',
                'agreement_date' => '', //'采购时间',
                'inquiry_admin_id' => '', //'询价员ID',
                'delivery_time' => '', //'报价货期（周）',
                'top_goods_number' => $goods['top_goods_number'],
            ];
            //询价单
            if (isset($item->inquirylow) && !empty($item->inquirylow)) {
                $inquirylow = $item->inquirylow;
                $res['tax_rate'] = $inquirylow->tax_rate;
                $res['price'] = $inquirylow->price;
                $res['inquiry_admin_id'] = $inquirylow->admin_id;
                $res['relevance_id'] = $inquirylow->id;
                $res['delivery_time'] = $inquirylow->delivery_time;
            }
            //成本单
            if (isset($item->finallow->inquirylow) && !empty($item->finallow->inquirylow)) {
                $finallow = $item->finallow;
                $inquirylow = $finallow->inquirylow;
                //序号
                $res['serial'] = $finallow->serial;
                $res['tax_rate'] = $inquirylow->tax_rate;
                $res['price'] = $inquirylow->price;
                $res['inquiry_admin_id'] = $inquirylow->admin_id;
                $res['relevance_id'] = $inquirylow->id;
                $res['delivery_time'] = $inquirylow->delivery_time;
            }
            if ($item->goods->is_assembly == Goods::IS_ASSEMBLY_YES) {
                $info = GoodsRelation::getGoodsSonPriceFinal($res, $info);
            } else {
                $id = $item->goods_id;
                if (isset($info[$id])) {
                    $res['number'] += $info[$id]['number'];
                    if ($info[$id]['delivery_time'] > $res['delivery_time'] && $info[$id]['delivery_time'] > 0) {
                        $res['delivery_time'] = $info[$id]['delivery_time'];
                    }
                }
                $res['order_number'] = $res['number'];
                $res['purchase_number'] = $res['number'];
                $info[$id] = $res;
            }

        }
        return $info;
    }

    /**
     * 关联成本单
     */
    public function getFinallow()
    {
        return $this->hasOne(FinalGoods::className(), ['goods_id' => 'goods_id'])->with('inquirylow')->orderBy('price ASC');
    }

    /**
     * 关联询价单
     */
    public function getInquirylow()
    {
        return $this->hasOne(Inquiry::className(), ['good_id' => 'goods_id'])->orderBy('price ASC');
    }

    /**
     * 关联库存
     */
    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'goods_id']);
    }

    /**
     * 获取最低级零件对应数量
     */
    public static function getGoodsSonNumber($goods, $info = [])
    {
        //查询子级
        $data = self::find()
            ->where(['goods_relation.is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $goods['goods_id']])
            ->all();
        foreach ($data as $item) {
            $goods_son = [
                'goods_id' => $item->goods->id,
                'goods_number' => $item->goods->goods_number,
                'goods_number_b' => $item->goods->goods_number_b,
                'description' => $item->goods->description,
                'description_en' => $item->goods->description_en,
                'original_company' => $item->goods->original_company,
                'unit' => $item->goods->unit,
                'stock_number' => $item->stock->number ?? 0,
                'temp_number' => $item->stock->temp_number ?? 0,
                'stock_position' => $item->stock->position ?? '',
                'number' => $item->number * $goods['number'],
            ];
            if ($item->goods->is_assembly == Goods::IS_ASSEMBLY_YES) {
                $info = self::getGoodsSonNumber($item, $info);
            } else {
                if (isset($info[$goods_son['goods_id']])) {
                    $goods_son['number'] += $info[$goods_son['goods_id']]['number'];
                }
                $info[$goods_son['goods_id']] = $goods_son;
            }
        }
        return $info;

    }

    /**
     * 获取子零件的所有等级的信息数据
     */
    public static function getGoodsSonInfo($id, $info = [], $p_goods_id = '')
    {
        //查询子级
        $select = [
            'goods_number',
            'description',
            'material_code',
            'goods_number_b',
            'description_en',
            'original_company',
            'is_assembly',
            'goods.id',
            'goods_relation.number',
            'goods_relation.p_goods_id',
            'goods_relation.id AS relation_id'
        ];
        $data = self::find()
            ->select($select)
            ->where(['goods_relation.is_deleted' => GoodsRelation::IS_DELETED_NO, 'p_goods_id' => $id])
            ->join('LEFT JOIN', Goods::tableName(), 'goods.id=goods_relation.goods_id')
            ->asArray()->all();
        foreach ($data as $item) {
            $item['p_goods_id'] = $p_goods_id;
            $info[] = $item;
            if ($item['is_assembly'] == Goods::IS_ASSEMBLY_YES) {
                $info = self::getGoodsSonInfo($item['id'], $info, $item['relation_id']);
            }
        }
        return $info;
    }
}
