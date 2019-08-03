<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderFinal;

/**
 * OrderFinalSearch represents the model behind the search form of `app\models\OrderFinal`.
 */
class OrderFinalSearch extends OrderFinal
{
    public $order_sn;
    public $customer;
    public $short_name;
    public $manage_name;
    public $provide_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'is_deleted', 'is_quote'], 'integer'],
            [['final_sn', 'goods_info', 'updated_at', 'created_at', 'order_sn', 'customer', 'short_name', 'manage_name', 'provide_date'], 'safe'],
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
        $query = OrderFinal::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->order_sn || $this->customer || $this->short_name || $this->manage_name || $this->provide_date) {
            $query->leftJoin('order as a', 'a.id = order_final.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
            if ($this->customer) {
                $customer_ids = Customer::find()->where(['like', 'name', $this->customer])->column();
                $query->andWhere(['a.customer_id' => $customer_ids]);
            }
            if ($this->short_name) {
                $customer_ids = Customer::find()->where(['like', 'short_name', $this->short_name])->column();
                $query->andWhere(['a.customer_id' => $customer_ids]);
            }
            $query->andFilterWhere(['like', 'a.manage_name', $this->manage_name]);

            if ($this->provide_date && strpos($this->provide_date, ' - ')) {
                list($provide_date_start, $provide_date_end) = explode(' - ', $this->provide_date);
                $query->andFilterWhere(['between', 'a.provide_date', $provide_date_start, $provide_date_end]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_final.id'         => $this->id,
            'order_final.order_id'   => $this->order_id,
            'order_final.is_deleted' => $this->is_deleted,
            'order_final.is_quote'   => $this->is_quote,
        ]);

        $query->andFilterWhere(['like', 'final_sn', $this->final_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_final.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_final.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
