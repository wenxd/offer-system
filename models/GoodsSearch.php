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
            'is_deleted' => $this->is_deleted,
            'offer_date' => $this->offer_date,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
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

        return $dataProvider;
    }
}
