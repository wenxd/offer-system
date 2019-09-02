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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_final_id', 'goods_id', 'type', 'relevance_id', 'number', 'is_quote',
                'is_deleted', 'delivery_time'], 'integer'],
            [['order_final_sn', 'order_quote_id', 'order_quote_sn', 'updated_at', 'created_at', 'serial',
                'order_sn'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price',
                'quote_all_price', 'quote_all_tax_price'], 'number'],
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

        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = quote_goods.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_final_id' => $this->order_final_id,
            'goods_id' => $this->goods_id,
            'type' => $this->type,
            'relevance_id' => $this->relevance_id,
            'number' => $this->number,
            'is_quote' => $this->is_quote,
            'is_deleted' => $this->is_deleted,
            'updated_at' => $this->updated_at,
            'tax_rate' => $this->tax_rate,
            'price' => $this->price,
            'tax_price' => $this->tax_price,
            'all_price' => $this->all_price,
            'all_tax_price' => $this->all_tax_price,
            'quote_price' => $this->quote_price,
            'quote_tax_price' => $this->quote_tax_price,
            'quote_all_price' => $this->quote_all_price,
            'quote_all_tax_price' => $this->quote_all_tax_price,
            'delivery_time' => $this->delivery_time,
        ]);

        $query->andFilterWhere(['like', 'order_final_sn', $this->order_final_sn])
            ->andFilterWhere(['like', 'order_quote_id', $this->order_quote_id])
            ->andFilterWhere(['like', 'order_quote_sn', $this->order_quote_sn])
            ->andFilterWhere(['like', 'serial', $this->serial]);

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_final.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
