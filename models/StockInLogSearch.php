<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StockLog;

/**
 * StockInLogSearch represents the model behind the search form of `app\models\StockLog`.
 */
class StockInLogSearch extends StockLog
{
    public $order_sn;
    public $goods_number;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_purchase_id', 'goods_id', 'number', 'type', 'is_deleted'], 'integer'],
            [['operate_time', 'updated_at', 'created_at', 'order_sn'], 'safe'],
            [['order_sn', 'goods_number', 'purchase_sn'], 'string', 'max' => 255],
            [['id', 'order_sn', 'goods_number', 'purchase_sn', 'number'], 'trim'],
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
        $query = StockLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'number', 'operate_time', 'created_at']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = stock_log.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }
        if ($this->goods_number) {
            $query->leftJoin('goods as b', 'b.id = stock_log.goods_id');
            $query->andFilterWhere(['like', 'b.goods_number', $this->goods_number]);
        }

        $query->andFilterWhere(['like', 'stock_log.purchase_sn', $this->purchase_sn]);
        // grid filtering conditions
        $query->andFilterWhere([
            'stock_log.id'                => $this->id,
            'stock_log.order_id'          => $this->order_id,
            'stock_log.order_purchase_id' => $this->order_purchase_id,
            'stock_log.goods_id'          => $this->goods_id,
            'stock_log.number'            => $this->number,
            'stock_log.type'              => StockLog::TYPE_IN,
            'stock_log.is_deleted'        => $this->is_deleted,
            'stock_log.updated_at'        => $this->updated_at,
        ]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'stock_log.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'stock_log.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
