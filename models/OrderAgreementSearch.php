<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderAgreement;
use yii\helpers\ArrayHelper;

/**
 * OrderAgreementSearch represents the model behind the search form of `app\models\OrderAgreement`.
 */
class OrderAgreementSearch extends OrderAgreement
{
    public $order_sn;
    public $customer_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_quote_id', 'is_agreement', 'admin_id', 'is_deleted',
                'is_payment', 'is_bill', 'is_stock', 'is_advancecharge', 'is_purchase', 'is_complete'], 'integer'],
            [['agreement_sn', 'goods_info', 'agreement_date', 'updated_at', 'created_at', 'order_sn',
                'customer_name', 'sign_date', 'stock_at', 'payment_price', 'order_quote_sn', 'expect_at'], 'safe'],
            [['id', 'order_quote_sn', 'order_sn', 'customer_name', 'payment_price', 'payment_ratio', 'remain_price'], 'trim'],
            [['payment_price', 'payment_ratio', 'remain_price'], 'number'],
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
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'agreement_date']
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
            'order_agreement.id'               => $this->id,
            'order_agreement.order_id'         => $this->order_id,
            'order_agreement.order_quote_id'   => $this->order_quote_id,
            'order_agreement.is_agreement'     => $this->is_agreement,
            'order_agreement.admin_id'         => $this->admin_id,
            'order_agreement.payment_price'    => $this->payment_price,
            'order_agreement.payment_ratio'    => $this->payment_ratio,
            'order_agreement.remain_price'     => $this->remain_price,
            'order_agreement.is_payment'       => $this->is_payment,
            'order_agreement.is_bill'          => $this->is_bill,
            'order_agreement.is_stock'         => $this->is_stock,
            'order_agreement.is_advancecharge' => $this->is_advancecharge,
            'order_agreement.is_purchase'      => $this->is_purchase,
            'order_agreement.is_complete'      => $this->is_complete,
        ]);

        if ($this->order_sn || $this->customer_name) {
            $customerList = Customer::find()->where(['like', 'name', $this->customer_name])->all();
            $customerIds = ArrayHelper::getColumn($customerList, 'id');
            $query->leftJoin('order as a', 'a.id = order_agreement.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
            $query->andWhere(['a.customer_id' => $customerIds]);
        }

        $query->andFilterWhere(['like', 'agreement_sn', $this->agreement_sn])
              ->andFilterWhere(['like', 'order_quote_sn', $this->order_quote_sn])
              ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.created_at', $created_at_start, $created_at_end]);
        }

        if ($this->agreement_date && strpos($this->agreement_date, ' - ')) {
            list($agreement_at_start, $agreement_at_end) = explode(' - ', $this->agreement_date);
            $agreement_at_start .= ' 00:00:00';
            $agreement_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.agreement_date', $agreement_at_start, $agreement_at_end]);
        }

        if ($this->sign_date && strpos($this->sign_date, ' - ')) {
            list($sign_date_start, $sign_date_end) = explode(' - ', $this->sign_date);
            $sign_date_start .= ' 00:00:00';
            $sign_date_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.agreement_date', $sign_date_start, $sign_date_end]);
        }

        if ($this->stock_at && strpos($this->stock_at, ' - ')) {
            list($stock_at_start, $stock_at_end) = explode(' - ', $this->stock_at);
            $stock_at_start .= ' 00:00:00';
            $stock_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.stock_at', $stock_at_start, $stock_at_end]);
        }

        if ($this->expect_at && strpos($this->expect_at, ' - ')) {
            list($expect_at_start, $expect_at_end) = explode(' - ', $this->expect_at);
            $expect_at_start .= ' 00:00:00';
            $expect_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_agreement.expect_at', $expect_at_start, $expect_at_end]);
        }
        return $dataProvider;
    }
}
