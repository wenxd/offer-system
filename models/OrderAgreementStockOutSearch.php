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
class OrderAgreementStockOutSearch extends OrderAgreement
{
    public $order_sn;
    public $customer_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_quote_id', 'order_quote_sn', 'is_agreement', 'admin_id', 'is_deleted',
                'is_stock'], 'integer'],
            [['agreement_sn', 'goods_info', 'agreement_date', 'updated_at', 'created_at', 'order_sn',
                'customer_name', 'sign_date'], 'safe'],
            [['id', 'order_quote_sn', 'order_sn', 'customer_name'], 'trim'],
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
            'order_agreement.id'             => $this->id,
            'order_agreement.order_id'       => $this->order_id,
            'order_agreement.order_quote_id' => $this->order_quote_id,
            'order_agreement.order_quote_sn' => $this->order_quote_sn,
            'order_agreement.is_agreement'   => $this->is_agreement,
            'order_agreement.admin_id'       => $this->admin_id,
            'order_agreement.is_stock'       => $this->is_stock,
        ]);

        if ($this->order_sn || $this->customer_name) {
            $customerList = Customer::find()->where(['like', 'name', $this->customer_name])->all();
            $customerIds = ArrayHelper::getColumn($customerList, 'id');
            $query->leftJoin('order as a', 'a.id = order_agreement.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
            $query->andWhere(['a.customer_id' => $customerIds]);
        }

        $query->andFilterWhere(['like', 'agreement_sn', $this->agreement_sn])
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

        return $dataProvider;
    }
}
