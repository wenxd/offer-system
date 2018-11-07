<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inquiry;

/**
 * InquirySearch represents the model behind the search form of `backend\models\Inquiry`.
 */
class InquirySearch extends Inquiry
{
    public $goods_number;
    public $original_company;
    public $original_company_remark;
    public $unit;
    public $technique_remark;
    public $is_process;
    public $img_id;

    public $supplier_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'delivery_time'], 'integer'],
            [['good_id', 'supplier_name', 'inquiry_datetime', 'updated_at', 'created_at', 'goods_number', 'tax_price',
                'offer_date', 'remark', 'original_company', 'original_company_remark', 'unit', 'technique_remark',
                'is_process'], 'safe'],
            [['price', 'tax_price'], 'number', 'min' => 0],
            [['id', 'good_id', 'supplier_id', 'supplier_name', 'price', 'tax_price', 'remark'], 'trim']
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
                'attributes' => ['id', 'price', 'tax_price', 'inquiry_datetime', 'updated_at', 'created_at']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number || $this->original_company || $this->original_company_remark || $this->unit
            || $this->technique_remark || $this->is_process) {
            $query->leftJoin('goods as a', 'a.id = inquiry.good_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'a.original_company', $this->original_company]);
            $query->andFilterWhere(['like', 'a.original_company_remark', $this->original_company_remark]);
            $query->andFilterWhere(['like', 'a.unit', $this->unit]);
            $query->andFilterWhere(['like', 'a.is_process', $this->is_process]);
            $query->andFilterWhere(['like', 'a.technique_remark', $this->technique_remark]);
        }
        if ($this->supplier_name) {
            $query->leftJoin('supplier as s', 's.id = inquiry.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id'            => $this->id,
            'supplier_id'   => $this->supplier_id,
            'price'         => $this->price,
            'tax_price'     => $this->tax_price,
            'delivery_time' => $this->delivery_time,
            'remark'        => $this->remark,
            'sort'          => $this->sort,
            'is_better'     => $this->is_better,
            'is_newest'     => $this->is_newest,
            'is_deleted'    => $this->is_deleted,
        ]);

        $query->andFilterWhere(['good_id' => $this->good_id]);
        $query->andFilterWhere(['like', 'remark', $this->remark]);

        if ($this->inquiry_datetime && strpos($this->inquiry_datetime, ' - ')) {
            list($inquiry_at_start, $inquiry_at_end) = explode(' - ', $this->inquiry_datetime);
            $inquiry_at_start .= ' 00:00:00';
            $inquiry_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry_datetime', $inquiry_at_start, $inquiry_at_end]);
        }

        if ($this->offer_date && strpos($this->offer_date, ' - ')) {
            list($offer_date_start, $offer_date_end) = explode(' - ', $this->offer_date);
            $offer_date_start .= ' 00:00:00';
            $offer_date_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'offer_date', $offer_date_start, $offer_date_end]);
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
