<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: 27525
 * Date: 2020/10/16
 * Time: 16:26
 */

namespace app\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

class InquiryGoodsClarifySearch extends InquiryGoodsClarify
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inquiry_goods_id', 'order_id', 'order_inquiry_id', 'goods_id', 'is_inquiry', 'is_result', 'is_deleted', 'admin_id', 'is_result_tag'], 'integer'],
            [['updated_at', 'created_at', 'not_result_at'], 'safe'],
            [['inquiry_sn', 'reason', 'remark', 'clarify'], 'string', 'max' => 255],
        ];
    }

    public $goods_number;
    public $goods_number_b;
    public $description;
    public $description_en;
    public $original_company;
    public $order_sn;

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
        $query = self::find();
        if ($admin_id != $user_super->user_id) {
            $query->where(['admin_id' => $admin_id]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'clarify_id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->goods_number || $this->goods_number_b || $this->description || $this->description_en || $this->original_company) {
            $query->leftJoin('goods as g', 'g.id = inquiry_goods_clarify.goods_id');
            $query->andFilterWhere(['like', 'g.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'g.goods_number_b', $this->goods_number_b]);
            $query->andFilterWhere(['like', 'g.description', $this->description]);
            $query->andFilterWhere(['like', 'g.description_en', $this->description_en]);
            $query->andFilterWhere(['like', 'g.original_company', $this->original_company]);
        }

        if ($this->order_sn) {
            $query->leftJoin('order as o', 'o.id = inquiry_goods_clarify.order_id');
            $query->andFilterWhere(['like', 'o.order_sn', $this->order_sn]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'inquiry_goods_clarify.inquiry_goods_id'      => $this->inquiry_goods_id,
            'inquiry_goods_clarify.order_id'      => $this->order_id,
            'inquiry_goods_clarify.goods_id'      => $this->goods_id,
            'inquiry_goods_clarify.is_inquiry'    => $this->is_inquiry,
            'inquiry_goods_clarify.is_result'     => $this->is_result,
            'inquiry_goods_clarify.is_deleted'    => $this->is_deleted,
            'inquiry_goods_clarify.updated_at'    => $this->updated_at,
            'inquiry_goods_clarify.created_at'    => $this->created_at,
            'inquiry_goods_clarify.admin_id'      => $this->admin_id,
        ]);

        $query->andFilterWhere(['like', 'inquiry_sn', $this->inquiry_sn])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        if ($this->not_result_at && strpos($this->not_result_at, ' - ')) {
            list($not_result_at_start, $not_result_at_end) = explode(' - ', $this->not_result_at);
            $not_result_at_start .= ' 00:00:00';
            $not_result_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry_goods_clarify.created_at', $not_result_at_start, $not_result_at_end]);
        }

        return $dataProvider;
    }
}