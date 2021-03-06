<?php

namespace app\models;

use app\models\InquiryGoods;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * InquiryGoodsSearch represents the model behind the search form of `app\models\InquiryGoods`.
 */
class InquiryGoodsSearch extends InquiryGoods
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
            [['id', 'order_id', 'goods_id', 'number', 'is_inquiry', 'is_result', 'is_deleted', 'admin_id'], 'integer'],
            [['inquiry_sn', 'serial', 'reason', 'updated_at', 'created_at', 'not_result_at'], 'safe'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'order_sn'], 'string'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'order_sn'], 'trim'],
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
        $user_super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
        $admin_id = Yii::$app->user->identity->id;
        if ($admin_id == $user_super->user_id) {
            $query = self::find()->where(['is_result_tag' => 1]);
        } else {
            $query = self::find()->where(['is_result_tag' => 1, 'admin_id' => $admin_id]);
        }

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

        if ($this->goods_number || $this->goods_number_b || $this->description || $this->description_en || $this->original_company) {
            $query->leftJoin('goods as g', 'g.id = inquiry_goods.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'g.goods_number_b', $this->goods_number_b]);
            $query->andFilterWhere(['like', 'g.description', $this->description]);
            $query->andFilterWhere(['like', 'g.description_en', $this->description_en]);
            $query->andFilterWhere(['like', 'g.original_company', $this->original_company]);
        }

        if ($this->order_sn) {
            $query->leftJoin('order as o', 'o.id = inquiry_goods.order_id');
            $query->andFilterWhere(['like', 'o.order_sn', $this->order_sn]);
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
