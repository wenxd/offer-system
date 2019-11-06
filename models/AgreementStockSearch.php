<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AgreementStock;

/**
 * AgreementStockSearch represents the model behind the search form of `app\models\AgreementStock`.
 */
class AgreementStockSearch extends AgreementStock
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_agreement_id', 'order_purchase_id', 'order_payment_id', 'goods_id', 'use_number'], 'integer'],
            [['order_agreement_sn', 'order_purchase_sn', 'order_payment_sn'], 'safe'],
            [['price', 'tax_price', 'all_price', 'all_tax_price'], 'number'],
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
        $query = AgreementStock::find();

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
            'order_agreement_id' => $this->order_agreement_id,
            'order_purchase_id' => $this->order_purchase_id,
            'order_payment_id' => $this->order_payment_id,
            'goods_id' => $this->goods_id,
            'price' => $this->price,
            'tax_price' => $this->tax_price,
            'use_number' => $this->use_number,
            'all_price' => $this->all_price,
            'all_tax_price' => $this->all_tax_price,
        ]);

        $query->andFilterWhere(['like', 'order_agreement_sn', $this->order_agreement_sn])
            ->andFilterWhere(['like', 'order_purchase_sn', $this->order_purchase_sn])
            ->andFilterWhere(['like', 'order_payment_sn', $this->order_payment_sn]);

        return $dataProvider;
    }
}
