<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderAgreement;

/**
 * OrderAgreementSearch represents the model behind the search form of `app\models\OrderAgreement`.
 */
class OrderAgreementSearch extends OrderAgreement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_quote_id', 'order_quote_sn', 'is_agreement', 'admin_id', 'is_deleted'], 'integer'],
            [['agreement_sn', 'goods_info', 'agreement_date', 'updated_at', 'created_at'], 'safe'],
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
        $query = OrderAgreement::find();

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
            'id'             => $this->id,
            'order_id'       => $this->order_id,
            'order_quote_id' => $this->order_quote_id,
            'order_quote_sn' => $this->order_quote_sn,
            'agreement_date' => $this->agreement_date,
            'is_agreement'   => $this->is_agreement,
            'admin_id'       => $this->admin_id,
            'is_deleted'     => $this->is_deleted,
            'updated_at'     => $this->updated_at,
            'created_at'     => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'agreement_sn', $this->agreement_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        return $dataProvider;
    }
}
