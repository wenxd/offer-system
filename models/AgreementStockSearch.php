<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AgreementStock;
use yii\helpers\ArrayHelper;

/**
 * AgreementStockSearch represents the model behind the search form of `app\models\AgreementStock`.
 */
class AgreementStockSearch extends AgreementStock
{
    public $order_sn;
    public $goods_number;
    public $description;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_agreement_id', 'order_purchase_id', 'order_payment_id', 'goods_id', 'use_number', 'is_confirm'], 'integer'],
            [['order_agreement_sn', 'order_purchase_sn', 'order_payment_sn', 'order_sn', 'description', 'goods_number'], 'safe'],
            [['price', 'tax_price', 'all_price', 'all_tax_price'], 'number'],
            [['order_agreement_sn', 'order_purchase_sn', 'order_payment_sn', 'order_sn', 'description', 'goods_number'], 'trim'],
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
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = agreement_stock.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }

        if ($this->goods_number !== '' || $this->description !== '') {
            $query->leftJoin('goods as g', 'g.id = agreement_stock.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'g.description', $this->description]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'agreement_stock.id'                    => $this->id,
            'agreement_stock.order_id'              => $this->order_id,
            'agreement_stock.order_agreement_id'    => $this->order_agreement_id,
            'agreement_stock.order_purchase_id'     => $this->order_purchase_id,
            'agreement_stock.order_payment_id'      => $this->order_payment_id,
            'agreement_stock.goods_id'              => $this->goods_id,
            'agreement_stock.price'                 => $this->price,
            'agreement_stock.tax_price'             => $this->tax_price,
            'agreement_stock.use_number'            => $this->use_number,
            'agreement_stock.all_price'             => $this->all_price,
            'agreement_stock.all_tax_price'         => $this->all_tax_price,
            'agreement_stock.is_confirm'            => $this->is_confirm,
        ]);

        $query->andFilterWhere(['like', 'agreement_stock.order_agreement_sn', $this->order_agreement_sn])
            ->andFilterWhere(['like', 'agreement_stock.order_purchase_sn', $this->order_purchase_sn])
            ->andFilterWhere(['like', 'agreement_stock.order_payment_sn', $this->order_payment_sn]);

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'agreement_stock.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
