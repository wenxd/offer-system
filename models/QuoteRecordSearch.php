<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\QuoteRecord;

/**
 * QuoteRecordSearch represents the model behind the search form of `app\models\QuoteRecord`.
 */
class QuoteRecordSearch extends QuoteRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'inquiry_id', 'goods_id', 'number', 'order_quote_id', 'order_type', 'status'], 'integer'],
            [['quote_price'], 'number'],
            [['remark', 'updated_at', 'created_at'], 'safe'],
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
        $query = QuoteRecord::find();

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
            'type' => $this->type,
            'inquiry_id' => $this->inquiry_id,
            'goods_id' => $this->goods_id,
            'quote_price' => $this->quote_price,
            'number' => $this->number,
            'order_quote_id' => $this->order_quote_id,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
