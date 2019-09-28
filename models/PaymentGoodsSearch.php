<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentGoods;

/**
 * PaymentGoodsSearch represents the model behind the search form of `app\models\PaymentGoods`.
 */
class PaymentGoodsSearch extends PaymentGoods
{
    public $goods_number;
    public $goods_number_b;
    public $description;
    public $description_en;
    public $original_company;
    public $order_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_payment_id', 'order_purchase_id', 'purchase_goods_id', 'goods_id', 'type',
                'relevance_id', 'number', 'fixed_number', 'inquiry_admin_id', 'is_quality', 'supplier_id', 'before_supplier_id'], 'integer'],
            [['order_payment_sn', 'order_purchase_sn', 'serial', 'updated_at', 'created_at', 'goods_number',
                'goods_number_b', 'description', 'description_en', 'original_company'], 'safe'],
            [['tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price', 'fixed_price', 'fixed_tax_price',
                'fixed_all_price', 'fixed_all_tax_price', 'delivery_time', 'before_delivery_time'], 'number'],
            [['id', 'order_payment_sn', 'order_sn', 'goods_number', 'goods_number_b', 'description', 'description_en', 'original_company'], 'trim'],
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
        $query = PaymentGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'fixed_number', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->goods_number || $this->goods_number_b || $this->description || $this->description_en || $this->original_company) {
            $query->leftJoin('goods as a', 'a.id = payment_goods.goods_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'a.goods_number_b', $this->goods_number_b]);
            $query->andFilterWhere(['like', 'a.description', $this->description]);
            $query->andFilterWhere(['like', 'a.description_en', $this->description_en]);
            $query->andFilterWhere(['like', 'a.original_company', $this->original_company]);
        }

        if ($this->order_sn) {
            $query->leftJoin('order as b', 'b.id = payment_goods.order_id');
            $query->andFilterWhere(['like', 'b.order_sn', $this->order_sn]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'payment_goods.id'                    => $this->id,
            'payment_goods.order_id'              => $this->order_id,
            'payment_goods.order_payment_id'      => $this->order_payment_id,
            'payment_goods.order_purchase_id'     => $this->order_purchase_id,
            'payment_goods.purchase_goods_id'     => $this->purchase_goods_id,
            'payment_goods.goods_id'              => $this->goods_id,
            'payment_goods.type'                  => $this->type,
            'payment_goods.relevance_id'          => $this->relevance_id,
            'payment_goods.number'                => $this->number,
            'payment_goods.tax_rate'              => $this->tax_rate,
            'payment_goods.price'                 => $this->price,
            'payment_goods.tax_price'             => $this->tax_price,
            'payment_goods.all_price'             => $this->all_price,
            'payment_goods.all_tax_price'         => $this->all_tax_price,
            'payment_goods.fixed_price'           => $this->fixed_price,
            'payment_goods.fixed_tax_price'       => $this->fixed_tax_price,
            'payment_goods.fixed_all_price'       => $this->fixed_all_price,
            'payment_goods.fixed_all_tax_price'   => $this->fixed_all_tax_price,
            'payment_goods.fixed_number'          => $this->fixed_number,
            'payment_goods.inquiry_admin_id'      => $this->inquiry_admin_id,
            'payment_goods.updated_at'            => $this->updated_at,
            'payment_goods.created_at'            => $this->created_at,
            'payment_goods.is_quality'            => $this->is_quality,
            'payment_goods.supplier_id'           => $this->supplier_id,
            'payment_goods.delivery_time'         => $this->delivery_time,
            'payment_goods.before_supplier_id'    => $this->before_supplier_id,
            'payment_goods.before_delivery_time'  => $this->before_delivery_time,
        ]);

        $query->andFilterWhere(['like', 'payment_goods.order_payment_sn', $this->order_payment_sn])
            ->andFilterWhere(['like', 'payment_goods.order_purchase_sn', $this->order_purchase_sn])
            ->andFilterWhere(['like', 'payment_goods.serial', $this->serial]);

        return $dataProvider;
    }
}
