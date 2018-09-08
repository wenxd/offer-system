<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Goods;

/**
 * GoodsSearch represents the model behind the search form of `app\models\Goods`.
 */
class GoodsSearch extends Goods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_process', 'is_deleted'], 'integer'],
            [['goods_number', 'description', 'original_company', 'original_company_remark', 'unit', 'technique_remark', 'img_id', 'competitor', 'competitor_offer', 'offer_date', 'updated_at', 'created_at'], 'safe'],
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
        $query = Goods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'competitor_offer', 'offer_date', 'updated_at', 'created_at']
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
            'id' => $this->id,
            'is_process' => $this->is_process,
            'is_deleted' => self::IS_DELETED_NO,
        ]);

        $query->andFilterWhere(['like', 'goods_number', $this->goods_number])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'original_company', $this->original_company])
            ->andFilterWhere(['like', 'original_company_remark', $this->original_company_remark])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'technique_remark', $this->technique_remark])
            ->andFilterWhere(['like', 'img_id', $this->img_id])
            ->andFilterWhere(['like', 'competitor', $this->competitor])
            ->andFilterWhere(['like', 'competitor_offer', $this->competitor_offer]);

        if ($this->offer_date && strpos($this->offer_date, ' - ')) {
            list($offer_at_start, $offer_at_end) = explode(' - ', $this->offer_date);
            $offer_at_start .= ' 00:00:00';
            $offer_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'updated_at', $offer_at_start, $offer_at_end]);
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
