<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CompetitorGoods;

/**
 * CompetitorGoodsSearch represents the model behind the search form of `app\models\CompetitorGoods`.
 */
class CompetitorGoodsSearch extends CompetitorGoods
{
    public $goods_number;
    public $competitor_name;
    public $material_code;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'competitor_id', 'is_deleted', 'customer', 'number', 'is_issue'], 'integer'],
            [['tax_rate', 'price', 'tax_price', 'delivery_time', 'all_price', 'all_tax_price', 'stock_number'], 'number'],
            [['offer_date', 'updated_at', 'created_at', 'goods_number', 'competitor_name', 'unit',
                'remark', 'material_code'], 'safe'],
            [['id', 'goods_id', 'goods_number', 'competitor_id', 'competitor_name', 'price', 'unit', 'remark',
                'delivery_time', 'all_price', 'all_tax_price', 'stock_number', 'material_code'], 'trim'],
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
        $query = CompetitorGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['id', 'price', 'tax_price', 'offer_date', 'updated_at', 'created_at']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number || $this->material_code) {
            $query->leftJoin('goods as a', 'a.id = competitor_goods.goods_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'a.material_code', $this->material_code]);
        }
        if ($this->competitor_name) {
            $query->leftJoin('competitor as c', 'c.id = competitor_goods.competitor_id');
            $query->andFilterWhere(['like', 'c.name', $this->competitor_name]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'competitor_goods.id'               => $this->id,
            'competitor_goods.goods_id'         => $this->goods_id,
            'competitor_goods.competitor_id'    => $this->competitor_id,
            'competitor_goods.customer'         => $this->customer,
            'competitor_goods.tax_rate'         => $this->tax_rate,
            'competitor_goods.price'            => $this->price,
            'competitor_goods.tax_price'        => $this->tax_price,
            'competitor_goods.number'           => $this->number,
            'competitor_goods.unit'             => $this->unit,
            'competitor_goods.is_deleted'       => self::IS_DELETED_NO,
            'competitor_goods.delivery_time'    => $this->delivery_time,
            'competitor_goods.all_price'        => $this->all_price,
            'competitor_goods.all_tax_price'    => $this->all_tax_price,
            'competitor_goods.stock_number'     => $this->stock_number,
            'competitor_goods.is_issue'         => $this->is_issue,
        ]);

        if ($this->offer_date && strpos($this->offer_date, ' - ')) {
            list($offer_at_start, $offer_at_end) = explode(' - ', $this->offer_date);
            $offer_at_start .= ' 00:00:00';
            $offer_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'competitor_goods.updated_at', $offer_at_start, $offer_at_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'competitor_goods.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'competitor_goods.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
