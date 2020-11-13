<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderPayment;
use yii\helpers\ArrayHelper;

/**
 * OrderPaymentSearch represents the model behind the search form of `app\models\OrderPayment`.
 */
class OrderPaymentSearch extends OrderPayment
{
    public $order_sn;
    public $reim_price;
    public $reim_ratio;
    public $reim_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_purchase_id', 'admin_id', 'purchase_status', 'is_payment', 'is_stock',
                'is_advancecharge', 'is_bill', 'supplier_id', 'stock_admin_id', 'financial_admin_id', 'is_complete', 'is_contract', 'is_reim'], 'integer'],
            [['payment_price'], 'number'],
            [['updated_at', 'created_at', 'payment_at', 'advancecharge_at', 'stock_at', 'bill_at', 'take_time',
                'agreement_at', 'delivery_date', 'reim_date'], 'safe'],
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
    public function search($params, $is_contract = 1)
    {
        $use_admin = AuthAssignment::find()->where(['item_name' => ['采购员']])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        $userId   = Yii::$app->user->identity->id;
        if (in_array($userId, $adminIds)) {
            $query = OrderPayment::find()->where([
                'purchase_status' => self::PURCHASE_STATUS_PASS,
                'is_agreement'    => self::IS_ADVANCECHARGE_YES,
                'admin_id'        => $userId
            ]);
        } else {
            $query = OrderPayment::find()->where([
                'purchase_status' => self::PURCHASE_STATUS_PASS,
                'is_agreement'    => self::IS_ADVANCECHARGE_YES,
            ]);
        }
        $query->andWhere(['is_contract' => $is_contract]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'agreement_at', 'delivery_date', 'stock_at']
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
            'order_payment.id'                  => $this->id,
            'order_payment.order_id'            => $this->order_id,
            'order_payment.order_purchase_id'   => $this->order_purchase_id,
            'order_payment.payment_at'          => $this->payment_at,
            'order_payment.is_payment'          => $this->is_payment,
            'order_payment.admin_id'            => $this->admin_id,
            'order_payment.updated_at'          => $this->updated_at,
            'order_payment.created_at'          => $this->created_at,
            'order_payment.supplier_id'         => $this->supplier_id,
            'order_payment.is_bill'             => $this->is_bill,
            'order_payment.is_stock'            => $this->is_stock,
            'order_payment.is_advancecharge'    => $this->is_advancecharge,
            'order_payment.is_complete'         => $this->is_complete,
            'order_payment.stock_admin_id'      => $this->stock_admin_id,
            'is_reim'      => $this->is_reim,
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

        if ($this->take_time && strpos($this->take_time, ' - ')) {
            list($take_time_start, $take_time_end) = explode(' - ', $this->take_time);
            $take_time_start .= ' 00:00:00';
            $take_time_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.take_time', $take_time_start, $take_time_end]);
        }

        if ($this->stock_at && strpos($this->stock_at, ' - ')) {
            list($stock_at_start, $stock_at_end) = explode(' - ', $this->stock_at);
            $stock_at_start .= ' 00:00:00';
            $stock_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.stock_at', $stock_at_start, $stock_at_end]);
        }

        if ($this->delivery_date && strpos($this->delivery_date, ' - ')) {
            list($delivery_date_start, $delivery_date_end) = explode(' - ', $this->delivery_date);
            $delivery_date_start .= ' 00:00:00';
            $delivery_date_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_payment.delivery_date', $delivery_date_start, $delivery_date_end]);
        }

        return $dataProvider;
    }
}
