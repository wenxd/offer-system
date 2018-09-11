<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderQuoteSearch represents the model behind the search form of `backend\models\OrderQuote`.
 */
class OrderQuoteSearch extends Order
{
    public $customer_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_deleted'], 'integer'],
            [['order_id', 'description', 'remark', 'provide_date', 'updated_at', 'created_at', 'customer_name'], 'safe'],
            [['order_price'], 'number'],
            [['id', 'order_id', 'description', 'order_price', 'remark', 'customer_name'], 'trim'],
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
        $query = Order::find()->where(['type' => Order::TYPE_QUOTE]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'order_price', 'provide_date', 'updated_at', 'created_at']
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
            'order_quote.id' => $this->id,
            'order_quote.order_price' => $this->order_price,
            'order_quote.is_deleted' => $this->is_deleted,
        ]);

        if ($this->customer_name) {
            $query->leftJoin('customer as a', 'a.id = order_quote.customer_id');
            $query->andFilterWhere(['like', 'a.name', $this->customer_name]);
        }
        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'remark', $this->remark]);

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
