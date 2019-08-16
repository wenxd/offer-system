<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderPurchaseSearch represents the model behind the search form of `app\models\OrderPurchase`.
 */
class OrderFinancialCollectSearch extends OrderAgreement
{
    public $order_sn;
    public $order_agreement_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_quote_id', 'is_agreement', 'admin_id', 'is_deleted'], 'integer'],
            [['agreement_date', 'updated_at', 'created_at'], 'safe'],
            [['order_quote_sn', 'agreement_sn', 'order_sn'], 'string', 'max' => 255],
            [['goods_info'], 'string', 'max' => 512],
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
        $query = static::find();
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


        return $dataProvider;
    }
}
