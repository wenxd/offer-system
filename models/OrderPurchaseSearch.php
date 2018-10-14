<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderPurchase;

/**
 * OrderPurchaseSearch represents the model behind the search form of `app\models\OrderPurchase`.
 */
class OrderPurchaseSearch extends OrderPurchase
{
    public $order_sn;
    public $order_final_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_final_id', 'admin_id', 'is_purchase', 'is_deleted'], 'integer'],
            [['purchase_sn', 'goods_info', 'end_date', 'updated_at', 'created_at', 'order_sn'], 'safe'],
            [['id', 'purchase_sn', 'order_sn', 'order_final_sn'], 'trim'],
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
        $query = OrderPurchase::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'end_date', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = order_purchase.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }

        if ($this->order_final_sn) {
            $query->leftJoin('order_final as b', 'b.id = order_purchase.order_final_id');
            $query->andFilterWhere(['like', 'b.final_sn', $this->order_final_sn]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_purchase.id' => $this->id,
            'order_purchase.order_id' => $this->order_id,
            'order_purchase.order_final_id' => $this->order_final_id,
            'order_purchase.admin_id' => $this->admin_id,
            'order_purchase.is_purchase' => $this->is_purchase,
            'order_purchase.is_deleted' => $this->is_deleted,
        ]);

        if ($this->end_date && strpos($this->end_date, ' - ')) {
            list($end_date_start, $end_date_end) = explode(' - ', $this->end_date);
            $query->andFilterWhere(['between', 'order_purchase.end_date', $end_date_start, $end_date_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_purchase.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_purchase.created_at', $created_at_start, $created_at_end]);
        }

        $query->andFilterWhere(['like', 'purchase_sn', $this->purchase_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        return $dataProvider;
    }
}
