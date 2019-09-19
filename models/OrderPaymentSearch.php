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
            [['order_id', 'order_purchase_id', 'admin_id', 'purchase_status', 'is_payment', 'is_stock',
                'is_advancecharge', 'is_bill'], 'integer'],
            [['updated_at', 'created_at', 'payment_at', 'advancecharge_at', 'stock_at', 'bill_at', 'agreement_at'], 'safe'],
            [['payment_sn', 'order_purchase_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
            [['order_sn'], 'trim'],
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
        $query = OrderPayment::find()->where([
            'purchase_status' => self::PURCHASE_STATUS_PASS,
            'is_agreement'    => self::IS_ADVANCECHARGE_YES,
        ]);

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

        if ($this->agreement_at && strpos($this->agreement_at, ' - ')) {
            list($agreement_at_start, $agreement_at_end) = explode(' - ', $this->agreement_at);
            $agreement_at_start .= ' 00:00:00';
            $agreement_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.agreement_at', $agreement_at_start, $agreement_at_end]);
        }

        return $dataProvider;
    }
}
