<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderQuote;

/**
 * OrderQuoteSearch represents the model behind the search form of `backend\models\OrderQuote`.
 */
class OrderQuoteSearch extends OrderQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_deleted'], 'integer'],
            [['order_id', 'description', 'remark', 'inquirys', 'stocks', 'provide_date', 'updated_at', 'created_at'], 'safe'],
            [['quote_price'], 'number'],
            [['id', 'order_id', 'description', 'quote_price', 'remark'], 'trim'],
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
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'quote_price', 'provide_date', 'updated_at', 'created_at']
            ],
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
            'quote_price' => $this->quote_price,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'inquirys', $this->inquirys])
            ->andFilterWhere(['like', 'stocks', $this->stocks]);

        if ($this->provide_date && strpos($this->provide_date, ' - ')) {
            list($provide_at_start, $provide_at_end) = explode(' - ', $this->provide_date);
            $provide_at_start .= ' 00:00:00';
            $provide_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'provide_date', $provide_at_start, $provide_at_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
