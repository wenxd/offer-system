<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AgreementGoods;

/**
 * AgreementGoodsSearch represents the model behind the search form of `app\models\AgreementGoods`.
 */
class AgreementGoodsSearch extends AgreementGoods
{
    public $order_sn;
    public $customer_id;
    public $goods_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_agreement_id', 'order_quote_id', 'goods_id', 'type', 'relevance_id', 'number',
                'is_agreement', 'is_deleted', 'inquiry_admin_id', 'is_out', 'customer_id'], 'integer'],
            [['order_agreement_sn', 'order_quote_sn', 'serial', 'agreement_sn', 'purchase_date', 'agreement_date',
                'updated_at', 'created_at', 'goods_number', 'quote_delivery_time'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'quote_price', 'quote_tax_price',
                'quote_all_price', 'quote_all_tax_price'], 'number'],
            [['goods_number'], 'trim'],
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
        $query = AgreementGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'updated_at', 'created_at']
            ],
            'pagination' => ['pageSize' => 1000]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->customer_id) {
            $query->leftJoin('order as o', 'o.id = agreement_goods.order_id');
            $query->andFilterWhere(['o.customer_id' => $this->customer_id]);
        }

        if ($this->goods_number) {
            $query->leftJoin('goods as g', 'g.id = agreement_goods.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'agreement_goods.id'                    => $this->id,
            'agreement_goods.order_id'              => $this->order_id,
            'agreement_goods.order_agreement_id'    => $this->order_agreement_id,
            'agreement_goods.order_quote_id'        => $this->order_quote_id,
            'agreement_goods.goods_id'              => $this->goods_id,
            'agreement_goods.type'                  => $this->type,
            'agreement_goods.relevance_id'          => $this->relevance_id,
            'agreement_goods.tax_rate'              => $this->tax_rate,
            'agreement_goods.price'                 => $this->price,
            'agreement_goods.tax_price'             => $this->tax_price,
            'agreement_goods.all_price'             => $this->all_price,
            'agreement_goods.all_tax_price'         => $this->all_tax_price,
            'agreement_goods.quote_price'           => $this->quote_price,
            'agreement_goods.quote_tax_price'       => $this->quote_tax_price,
            'agreement_goods.quote_all_price'       => $this->quote_all_price,
            'agreement_goods.quote_all_tax_price'   => $this->quote_all_tax_price,
            'agreement_goods.number'                => $this->number,
            'agreement_goods.is_agreement'          => $this->is_agreement,
            'agreement_goods.is_deleted'            => $this->is_deleted,
            'agreement_goods.updated_at'            => $this->updated_at,
            'agreement_goods.created_at'            => $this->created_at,
            'agreement_goods.inquiry_admin_id'      => $this->inquiry_admin_id,
            'agreement_goods.is_out'                => $this->is_out,
            'agreement_goods.quote_delivery_time'   => $this->quote_delivery_time,
        ]);

        $query->andFilterWhere(['like', 'agreement_goods.order_agreement_sn', $this->order_agreement_sn])
              ->andFilterWhere(['like', 'agreement_goods.order_quote_sn', $this->order_quote_sn])
              ->andFilterWhere(['like', 'agreement_goods.serial', $this->serial])
              ->andFilterWhere(['like', 'agreement_goods.agreement_sn', $this->agreement_sn])
              ->andFilterWhere(['like', 'agreement_goods.purchase_date', $this->purchase_date])
              ->andFilterWhere(['like', 'agreement_goods.agreement_date', $this->agreement_date]);

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'agreement_goods.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
