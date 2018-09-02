<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stock;

/**
 * StockSearch represents the model behind the search form of `backend\models\Stock`.
 */
class StockSearch extends Stock
{
    public $goods_number;
    public $supplier_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'number', 'sort', 'is_deleted'], 'integer'],
            [['good_id', 'supplier_name', 'position', 'updated_at', 'created_at', 'goods_number'], 'safe'],
            [['price'], 'number'],
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
                'attributes' => ['id', 'price', 'number', 'updated_at', 'created_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number) {
            $query->leftJoin('goods as a', 'a.id = stock.good_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
        }
        if ($this->goods_number) {
            $query->leftJoin('supplier as s', 's.id = stock.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'stock.id' => $this->id,
            'stock.supplier_id' => $this->supplier_id,
            'stock.price' => $this->price,
            'stock.number' => $this->number,
            'stock.sort' => $this->sort,
            'stock.is_deleted' => self::IS_DELETED_NO,
        ]);

        $query->andFilterWhere(['like', 'stock.good_id', $this->good_id])
//            ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
            ->andFilterWhere(['like', 'stock.position', $this->position]);

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
