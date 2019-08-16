<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderPayment;

/**
 * OrderPaymentSearch represents the model behind the search form of `app\models\OrderPayment`.
 */
class OrderPaymentVerifySearch extends OrderPayment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_purchase_id', 'is_payment', 'admin_id'], 'integer'],
            [['payment_sn', 'order_purchase_sn', 'goods_info', 'payment_at', 'updated_at', 'created_at'], 'safe'],
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
        $query = OrderPayment::find()->where(['purchase_status' => self::PURCHASE_STATUS_CREATE]);

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
            'id'                => $this->id,
            'order_id'          => $this->order_id,
            'order_purchase_id' => $this->order_purchase_id,
            'payment_at'        => $this->payment_at,
            'is_payment'        => $this->is_payment,
            'admin_id'          => $this->admin_id,
            'updated_at'        => $this->updated_at,
            'created_at'        => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'payment_sn', $this->payment_sn])
            ->andFilterWhere(['like', 'order_purchase_sn', $this->order_purchase_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        return $dataProvider;
    }
}