<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Stock;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * StockSearch represents the model behind the search form of `backend\models\Stock`.
 */
class StockSearch extends Stock
{
    public $goods_number;
    public $description;
    public $description_en;
    public $supplier_name;
    public $is_zero;
    public $stock_low;
    public $stock_high;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'good_id', 'supplier_id', 'number', 'sort', 'is_deleted', 'is_emerg', 'is_zero',
                'stock_low', 'stock_high'], 'integer'],
            [['good_id', 'supplier_name', 'position', 'updated_at', 'created_at', 'goods_number', 'description',
                'description_en'], 'safe'],
            [['price', 'tax_price'], 'number'],
            [['number', 'suggest_number', 'high_number', 'low_number'], 'integer', 'min' => 0],
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
        $query = Stock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'price', 'tax_price', 'number', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number || $this->description || $this->description_en || $this->is_emerg !== '') {
            $query->leftJoin('goods as a', 'a.id = stock.good_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'a.description', $this->description]);
            $query->andFilterWhere(['like', 'a.description_en', $this->description_en]);
            $query->andFilterWhere(['like', 'a.is_emerg', $this->is_emerg]);
        }
        if ($this->supplier_name) {
            $query->leftJoin('supplier as s', 's.id = stock.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }

        if ($this->stock_low !== NULL && $this->stock_low !== '') {
            if ($this->stock_low == 0) {
                $query->andWhere('number >= low_number');
            }
            if ($this->stock_low == 1) {
                $query->andWhere('number < low_number');
            }
        }

        if ($this->stock_high !== NULL && $this->stock_high !== '') {
            if ($this->stock_high == 0) {
                $query->andWhere('number <= high_number');
            }
            if ($this->stock_high == 1) {
                $query->andWhere('number > high_number');
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'stock.id'                  => $this->id,
            'stock.supplier_id'         => $this->supplier_id,
            'stock.price'               => $this->price,
            'stock.tax_price'           => $this->tax_price,
            'stock.number'              => $this->number,
            'stock.suggest_number'      => $this->suggest_number,
            'stock.high_number'         => $this->high_number,
            'stock.low_number'          => $this->low_number,
            'stock.sort'                => $this->sort,
            'stock.is_deleted'          => self::IS_DELETED_NO,
        ]);

        $query->andFilterWhere(['like', 'stock.good_id', $this->good_id])
            ->andFilterWhere(['like', 'stock.position', $this->position]);

        if ($this->is_zero && !$this->number) {
            if ($this->is_zero == 1) {
                $query->andFilterWhere(['stock.number' => 0]);
            } else {
                $query->andFilterWhere(['!=', 'stock.number', 0]);
            }
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'stock.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'stock.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
