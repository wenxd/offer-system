<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StockLog;

/**
 * StockInLogSearch represents the model behind the search form of `app\models\StockLog`.
 */
class StockInLogSearch extends StockLog
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
            [['id', 'order_id', 'order_payment_id', 'order_agreement_id', 'order_purchase_id', 'purchase_sn',
                'goods_id', 'number', 'type', 'is_deleted', 'admin_id', 'is_manual'], 'integer'],
            [['payment_sn', 'agreement_sn', 'operate_time', 'updated_at', 'created_at', 'remark', 'order_sn',
                'order_type', 'goods_number', 'source', 'position'], 'safe'],
            [['payment_sn', 'agreement_sn', 'remark', 'source', 'position'], 'trim'],
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
            'stock_log.id'                    => $this->id,
            'stock_log.order_id'              => $this->order_id,
            'stock_log.order_payment_id'      => $this->order_payment_id,
            'stock_log.order_agreement_id'    => $this->order_agreement_id,
            'stock_log.order_purchase_id'     => $this->order_purchase_id,
            'stock_log.purchase_sn'           => $this->purchase_sn,
            'stock_log.goods_id'              => $this->goods_id,
            'stock_log.number'                => $this->number,
            'stock_log.type'                  => StockLog::TYPE_IN,
            'stock_log.is_deleted'            => $this->is_deleted,
            'stock_log.admin_id'              => $this->admin_id,
            'stock_log.is_manual'             => $this->is_manual,
        ]);

        if ($this->operate_time && strpos($this->operate_time, ' - ')) {
            list($operate_time_start, $operate_time_end) = explode(' - ', $this->operate_time);
            $operate_time_start .= ' 00:00:00';
            $operate_time_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'stock_log.operate_time', $operate_time_start, $operate_time_end]);
        }

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

        $query->andFilterWhere(['like', 'stock_log.payment_sn', $this->payment_sn])
            ->andFilterWhere(['like', 'stock_log.agreement_sn', $this->agreement_sn])
            ->andFilterWhere(['like', 'stock_log.source', $this->source])
            ->andFilterWhere(['like', 'stock_log.remark', $this->remark])
            ->andFilterWhere(['like', 'stock_log.position', $this->position]);

        return $dataProvider;
    }
}
