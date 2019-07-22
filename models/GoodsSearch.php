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
            [['id', 'is_process', 'is_deleted', 'is_special', 'is_nameplate', 'is_emerg', 'is_assembly'], 'integer'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'original_company_remark',
                'unit', 'technique_remark', 'img_id', 'nameplate_img_id', 'updated_at', 'created_at', 'device_one', 'device_two',
                'device_three', 'device_four', 'device_five', 'device_info'], 'safe'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'original_company_remark',
                'technique_remark', 'device_one', 'device_two', 'device_three', 'device_four', 'device_five', 'device_info'], 'trim'],
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
                'attributes' => ['id', 'updated_at', 'created_at']
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
            'is_process'   => $this->is_process,
            'is_special'   => $this->is_special,
            'is_nameplate' => $this->is_nameplate,
            'is_emerg'     => $this->is_emerg,
            'is_assembly'  => $this->is_assembly,
            'is_deleted'   => self::IS_DELETED_NO,
        ]);

        $query->andFilterWhere(['like', 'goods_number', $this->goods_number])
            ->andFilterWhere(['like', 'goods_number_b', $this->goods_number_b])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'description_en', $this->description_en])
            ->andFilterWhere(['like', 'original_company', $this->original_company])
            ->andFilterWhere(['like', 'original_company_remark', $this->original_company_remark])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'technique_remark', $this->technique_remark])
            ->andFilterWhere(['like', 'device_one', $this->device_one])
            ->andFilterWhere(['like', 'device_two', $this->device_two])
            ->andFilterWhere(['like', 'device_three', $this->device_three])
            ->andFilterWhere(['like', 'device_four', $this->device_four])
            ->andFilterWhere(['like', 'device_five', $this->device_five])
            ->andFilterWhere(['like', 'img_id', $this->img_id])
            ->andFilterWhere(['like', 'device_info', $this->device_info]);

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
