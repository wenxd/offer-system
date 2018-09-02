<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inquiry;

/**
 * InquirySearch represents the model behind the search form of `backend\models\Inquiry`.
 */
class InquiryBetterSearch extends InquiryBetter
{
    public $goods_number;
    public $supplier_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'is_priority'], 'integer'],
            [['good_id', 'supplier_name', 'inquiry_datetime', 'updated_at', 'created_at', 'goods_number'], 'safe'],
            [['inquiry_price'], 'number', 'min' => 0],
            [['id', 'good_id', 'supplier_id', 'supplier_name', 'inquiry_price'], 'trim']
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
        $query = Inquiry::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'inquiry_price', 'inquiry_datetime', 'updated_at', 'created_at']
            ]
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
        if ($this->supplier_name) {
            $query->leftJoin('supplier as s', 's.id = stock.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id'            => $this->id,
            'supplier_id'   => $this->supplier_id,
            'inquiry_price' => $this->inquiry_price,
            'sort'          => $this->sort,
            'is_better'     => $this->is_better,
            'is_newest'     => $this->is_newest,
            'is_deleted'    => $this->is_deleted,
            'is_priority'   => self::IS_PRIORITY_YES,
        ]);

        $query->andFilterWhere(['like', 'good_id', $this->good_id]);

        if ($this->inquiry_datetime && strpos($this->inquiry_datetime, ' - ')) {
            list($inquiry_at_start, $inquiry_at_end) = explode(' - ', $this->inquiry_datetime);
            $inquiry_at_start .= ' 00:00:00';
            $inquiry_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry_datetime', $inquiry_at_start, $inquiry_at_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
