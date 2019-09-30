<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InquiryGoods;

/**
 * InquiryGoodsSearch represents the model behind the search form of `app\models\InquiryGoods`.
 */
class InquiryGoodsSearch extends InquiryGoods
{
    public $goods_number;
    public $goods_number_b;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'goods_id', 'number', 'is_inquiry', 'is_result', 'is_deleted', 'admin_id'], 'integer'],
            [['inquiry_sn', 'serial', 'reason', 'updated_at', 'created_at', 'not_result_at'], 'safe'],
            [['goods_number', 'goods_number_b'], 'string'],
            [['goods_number', 'goods_number_b'], 'trim'],
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
        $query = InquiryGoods::find()->where(['is_result' => self::IS_RESULT_YES]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'not_result_at']
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->goods_number || $this->goods_number_b) {
            $query->leftJoin('goods as g', 'g.id = inquiry_goods.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'g.goods_number_b', $this->goods_number_b]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'inquiry_goods.id'            => $this->id,
            'inquiry_goods.order_id'      => $this->order_id,
            'inquiry_goods.goods_id'      => $this->goods_id,
            'inquiry_goods.number'        => $this->number,
            'inquiry_goods.is_inquiry'    => $this->is_inquiry,
            'inquiry_goods.is_result'     => $this->is_result,
            'inquiry_goods.is_deleted'    => $this->is_deleted,
            'inquiry_goods.updated_at'    => $this->updated_at,
            'inquiry_goods.created_at'    => $this->created_at,
            'inquiry_goods.admin_id'      => $this->admin_id,
        ]);

        $query->andFilterWhere(['like', 'inquiry_sn', $this->inquiry_sn])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        if ($this->not_result_at && strpos($this->not_result_at, ' - ')) {
            list($not_result_at_start, $not_result_at_end) = explode(' - ', $this->not_result_at);
            $not_result_at_start .= ' 00:00:00';
            $not_result_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry_goods.created_at', $not_result_at_start, $not_result_at_end]);
        }

        return $dataProvider;
    }
}
