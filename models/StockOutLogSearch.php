<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StockLog;

/**
 * StockInLogSearch represents the model behind the search form of `app\models\StockLog`.
 */
class StockOutLogSearch extends StockLog
{
    public $order_sn;
    public $order_type;
    public $goods_number;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_payment_id', 'goods_id', 'number', 'type', 'is_deleted', 'customer_id'], 'integer'],
            [['operate_time', 'updated_at', 'created_at', 'goods_number', 'remark', 'agreement_sn', 'admin_id',
                'is_manual', 'order_type', 'direction', 'region', 'plat_name', 'stock_out_cert'], 'safe'],
            [['id', 'order_sn', 'goods_number', 'number', 'agreement_sn'], 'trim'],
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
                'attributes' => ['id', 'number', 'operate_time']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->order_sn || $this->order_type != '') {
            $query->leftJoin('order as a', 'a.id = stock_log.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
            $query->andFilterWhere(['a.order_type' => $this->order_type]);
        }
        if ($this->goods_number) {
            $query->leftJoin('goods as b', 'b.id = stock_log.goods_id');
            $query->andFilterWhere(['like', 'b.goods_number', $this->goods_number]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'stock_log.id'                => $this->id,
            'stock_log.order_id'          => $this->order_id,
            'stock_log.goods_id'          => $this->goods_id,
            'stock_log.number'            => $this->number,
            'stock_log.type'              => StockLog::TYPE_OUT,
            'stock_log.agreement_sn'      => $this->agreement_sn,
            'stock_log.updated_at'        => $this->updated_at,
            'stock_log.is_manual'         => $this->is_manual,
            'stock_log.customer_id'       => $this->customer_id,
        ]);
        $query->andFilterWhere(['like', 'stock_log.direction', $this->direction])
            ->andFilterWhere(['like', 'stock_log.region', $this->region])
            ->andFilterWhere(['like', 'stock_log.stock_out_cert', $this->stock_out_cert])
            ->andFilterWhere(['like', 'stock_log.plat_name', $this->plat_name]);

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
