<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TempPaymentGoods;

/**
 * TempPaymentGoodsSearch represents the model behind the search form of `app\models\TempPaymentGoods`.
 */
class TempPaymentGoodsSearch extends TempPaymentGoods
{
    public $goods_number;
    public $goods_number_b;
    public $description;
    public $description_en;
    public $original_company;
    public $order_sn;
    public $order_final_id;
    public $is_purchase;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_payment_id', 'order_purchase_id', 'purchase_goods_id', 'goods_id', 'type',
                'relevance_id', 'number', 'fixed_number', 'inquiry_admin_id', 'is_quality', 'supplier_id', 'before_supplier_id',
                'is_payment', 'order_final_id', 'is_purchase'], 'integer'],
            [['order_payment_sn', 'order_purchase_sn', 'serial', 'updated_at', 'created_at', 'goods_number', 'goods_number_b',
                'description', 'description_en', 'original_company', 'order_sn'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'fixed_price', 'fixed_tax_price', 'fixed_all_price', 'fixed_all_tax_price', 'delivery_time', 'before_delivery_time'], 'number'],
            [['order_payment_sn', 'order_purchase_sn', 'serial', 'updated_at', 'created_at', 'goods_number', 'goods_number_b',
                'description', 'description_en', 'original_company', 'order_sn'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TempPaymentGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'fixed_number', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number) {
            $query->leftJoin('goods as a', 'a.id = purchase_goods.goods_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
        }

        if ($this->order_sn) {
            $query->leftJoin('order as b', 'b.id = purchase_goods.order_id');
            $query->andFilterWhere(['like', 'b.order_sn', $this->order_sn]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'purchase_goods.id'             => $this->id,
            'purchase_goods.order_id'       => $this->order_id,
            'purchase_goods.order_final_id' => $this->order_final_id,
            'purchase_goods.goods_id'       => $this->goods_id,
            'purchase_goods.type'           => $this->type,
            'purchase_goods.relevance_id'   => $this->relevance_id,
            'purchase_goods.is_purchase'    => $this->is_purchase,
            'purchase_goods.number'         => $this->number,
            'purchase_goods.delivery_time'  => $this->delivery_time,
        ]);

        $query->andFilterWhere(['like', 'order_purchase_id', $this->order_purchase_id])
            ->andFilterWhere(['like', 'order_purchase_sn', $this->order_purchase_sn]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'purchase_goods.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'purchase_goods.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
