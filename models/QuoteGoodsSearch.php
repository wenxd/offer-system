<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\QuoteGoods;

/**
 * QuoteGoodsSearch represents the model behind the search form of `app\models\QuoteGoods`.
 */
class QuoteGoodsSearch extends QuoteGoods
{
    public $order_sn;
    public $customer_id;
    public $goods_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_final_id', 'goods_id', 'type', 'relevance_id', 'number', 'is_quote',
                'is_deleted', 'delivery_time', 'customer_id'], 'integer'],
            [['order_final_sn', 'order_quote_id', 'order_quote_sn', 'updated_at', 'created_at', 'serial',
                'order_sn', 'goods_number'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price',
                'quote_all_price', 'quote_all_tax_price'], 'number'],
            [['goods_number'], 'trim'],
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
        $query = QuoteGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'updated_at', 'created_at']
            ],
            'pagination' => ['pageSize' => 1000]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->order_sn || $this->customer_id) {
            $query->leftJoin('order as a', 'a.id = quote_goods.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
            $query->andFilterWhere(['a.customer_id' => $this->customer_id]);
        }

        if ($this->goods_number) {
            $query->leftJoin('goods as g', 'g.id = quote_goods.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'quote_goods.id' => $this->id,
            'quote_goods.order_id' => $this->order_id,
            'quote_goods.order_final_id' => $this->order_final_id,
            'quote_goods.goods_id' => $this->goods_id,
            'quote_goods.type' => $this->type,
            'quote_goods.relevance_id' => $this->relevance_id,
            'quote_goods.number' => $this->number,
            'quote_goods.is_quote' => $this->is_quote,
            'quote_goods.is_deleted' => $this->is_deleted,
            'quote_goods.updated_at' => $this->updated_at,
            'quote_goods.tax_rate' => $this->tax_rate,
            'quote_goods.price' => $this->price,
            'quote_goods.tax_price' => $this->tax_price,
            'quote_goods.all_price' => $this->all_price,
            'quote_goods.all_tax_price' => $this->all_tax_price,
            'quote_goods.quote_price' => $this->quote_price,
            'quote_goods.quote_tax_price' => $this->quote_tax_price,
            'quote_goods.quote_all_price' => $this->quote_all_price,
            'quote_goods.quote_all_tax_price' => $this->quote_all_tax_price,
            'quote_goods.delivery_time' => $this->delivery_time,
        ]);

        $query->andFilterWhere(['like', 'quote_goods.order_final_sn', $this->order_final_sn])
              ->andFilterWhere(['like', 'quote_goods.order_quote_id', $this->order_quote_id])
              ->andFilterWhere(['like', 'quote_goods.order_quote_sn', $this->order_quote_sn])
              ->andFilterWhere(['like', 'quote_goods.serial', $this->serial]);

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'quote_goods.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
