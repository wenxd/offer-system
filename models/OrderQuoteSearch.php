<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderQuote;

/**
 * OrderQuoteSearch represents the model behind the search form of `app\models\OrderQuote`.
 */
class OrderQuoteSearch extends OrderQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'is_quote', 'admin_id', 'is_deleted'], 'integer'],
            [['quote_sn', 'goods_info', 'end_date', 'updated_at', 'created_at'], 'safe'],
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
        $query = OrderQuote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'end_date' => $this->end_date,
            'is_quote' => $this->is_quote,
            'admin_id' => $this->admin_id,
            'is_deleted' => $this->is_deleted,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'quote_sn', $this->quote_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        return $dataProvider;
    }
}
