<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderPurchase;
use yii\helpers\ArrayHelper;

/**
 * OrderPurchaseSearch represents the model behind the search form of `app\models\OrderPurchase`.
 */
class OrderFinancialSearch extends OrderPayment
{
    public $order_sn;
    public $order_agreement_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_purchase_id', 'admin_id', 'purchase_status', 'is_payment', 'is_stock',
                'is_advancecharge', 'is_bill', 'is_complete'], 'integer'],
            [['updated_at', 'created_at', 'payment_at', 'advancecharge_at', 'stock_at', 'bill_at'], 'safe'],
            [['payment_sn', 'order_purchase_sn'], 'string', 'max' => 255],
            [['id'], 'trim'],
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
        $query = static::find()->where([
            //'is_complete'  => self::IS_COMPLETE_NO,
            'is_agreement' => self::IS_ADVANCECHARGE_YES,
            'is_contract' => self::IS_ADVANCECHARGE_YES,
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
        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = order_payment.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }



        // grid filtering conditions
        $query->andFilterWhere([
            'order_payment.id'               => $this->id,
            'order_payment.order_id'         => $this->order_id,
            'order_payment.admin_id'         => $this->admin_id,
            'order_payment.is_stock'         => $this->is_stock,
            'order_payment.is_advancecharge' => $this->is_advancecharge,
            'order_payment.is_payment'       => $this->is_payment,
            'order_payment.is_bill'          => $this->is_bill,
            'order_payment.is_complete'      => $this->is_complete,
        ]);


        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.created_at', $created_at_start, $created_at_end]);
        }

        $query->andFilterWhere(['like', 'order_purchase_sn', $this->order_purchase_sn])
            ->andFilterWhere(['like', 'payment_sn', $this->payment_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        return $dataProvider;
    }
}
