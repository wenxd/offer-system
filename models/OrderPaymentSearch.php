<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderPayment;

/**
 * OrderPaymentSearch represents the model behind the search form of `app\models\OrderPayment`.
 */
class OrderPaymentSearch extends OrderPayment
{
    public $order_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_purchase_id', 'is_payment', 'admin_id'], 'integer'],
            [['payment_sn', 'order_purchase_sn', 'goods_info', 'payment_at', 'updated_at', 'created_at', 'order_sn'], 'safe'],
            [['payment_sn', 'order_purchase_sn', 'goods_info', 'payment_at', 'updated_at', 'created_at', 'order_sn'], 'trim'],
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
        $query = OrderPayment::find()->where(['purchase_status' => self::PURCHASE_STATUS_PASS]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id']
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
            'order_payment.id'                => $this->id,
            'order_payment.order_id'          => $this->order_id,
            'order_payment.order_purchase_id' => $this->order_purchase_id,
            'order_payment.payment_at'        => $this->payment_at,
            'order_payment.is_payment'        => $this->is_payment,
            'order_payment.admin_id'          => $this->admin_id,
            'order_payment.updated_at'        => $this->updated_at,
            'order_payment.created_at'        => $this->created_at,
        ]);

        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = order_payment.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }

        $query->andFilterWhere(['like', 'order_payment.payment_sn', $this->payment_sn])
              ->andFilterWhere(['like', 'order_payment.order_purchase_sn', $this->order_purchase_sn])
              ->andFilterWhere(['like', 'order_payment.goods_info', $this->goods_info]);

        return $dataProvider;
    }
}
