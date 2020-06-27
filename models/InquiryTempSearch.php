<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InquiryTemp;

/**
 * InquiryTempSearch represents the model behind the search form of `app\models\InquiryTemp`.
 */
class InquiryTempSearch extends InquiryTemp
{
    public $goods_number_b;
    public $original_company;
    public $unit;
    public $supplier_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'good_id', 'supplier_id', 'number', 'sort', 'is_better', 'is_newest', 'is_priority', 'is_deleted', 'delivery_time', 'admin_id', 'order_id', 'order_inquiry_id', 'inquiry_goods_id', 'is_upload'], 'integer'],
            [['price', 'tax_price', 'tax_rate', 'all_tax_price', 'all_price', 'is_purchase'], 'number'],
            [['inquiry_datetime', 'offer_date', 'remark', 'better_reason', 'updated_at', 'created_at', 'goods_number_b',
                'original_company', 'unit', 'supplier_name', 'technique_remark'], 'safe'],
            [['inquiry_datetime', 'goods_number_b', 'original_company', 'unit', 'supplier_name', 'remark', 'technique_remark'], 'trim'],
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
        $query = InquiryTemp::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                //'attributes' => ['tax_price', 'inquiry_datetime']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->original_company || $this->unit || $this->goods_number_b) {
            $query->leftJoin('goods as a', 'a.id = inquiry_temp.good_id');
            $query->andFilterWhere(['like', 'a.goods_number_b', $this->goods_number_b]);
            $query->andFilterWhere(['like', 'a.original_company', $this->original_company]);
            $query->andFilterWhere(['like', 'a.unit', $this->unit]);
        }
        if ($this->supplier_name) {
            $query->leftJoin('supplier as s', 's.id = inquiry_temp.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'inquiry_temp.id'                => $this->id,
            'inquiry_temp.good_id'           => $this->good_id,
            'inquiry_temp.supplier_id'       => $this->supplier_id,
            'inquiry_temp.price'             => $this->price,
            'inquiry_temp.tax_price'         => $this->tax_price,
            'inquiry_temp.tax_rate'          => $this->tax_rate,
            'inquiry_temp.all_tax_price'     => $this->all_tax_price,
            'inquiry_temp.all_price'         => $this->all_price,
            'inquiry_temp.number'            => $this->number,
            'inquiry_temp.sort'              => $this->sort,
            'inquiry_temp.is_better'         => $this->is_better,
            'inquiry_temp.is_newest'         => $this->is_newest,
            'inquiry_temp.is_priority'       => $this->is_priority,
            'inquiry_temp.is_deleted'        => $this->is_deleted,
            'inquiry_temp.offer_date'        => $this->offer_date,
            'inquiry_temp.delivery_time'     => $this->delivery_time,
            'inquiry_temp.admin_id'          => $this->admin_id,
            'inquiry_temp.order_id'          => $this->order_id,
            'inquiry_temp.order_inquiry_id'  => $this->order_inquiry_id,
            'inquiry_temp.inquiry_goods_id'  => $this->inquiry_goods_id,
            'inquiry_temp.updated_at'        => $this->updated_at,
            'inquiry_temp.created_at'        => $this->created_at,
            'inquiry_temp.is_upload'         => $this->is_upload,
            'inquiry_temp.is_purchase'       => $this->is_purchase,
        ]);

        $query->andFilterWhere(['like', 'inquiry_temp.remark', $this->remark])
            ->andFilterWhere(['like', 'inquiry_temp.better_reason', $this->better_reason])
            ->andFilterWhere(['like', 'inquiry_temp.remark', $this->remark])
            ->andFilterWhere(['like', 'inquiry_temp.technique_remark', $this->technique_remark]);

        if ($this->inquiry_datetime && strpos($this->inquiry_datetime, ' - ')) {
            list($inquiry_at_start, $inquiry_at_end) = explode(' - ', $this->inquiry_datetime);
            $inquiry_at_start .= ' 00:00:00';
            $inquiry_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry_temp.inquiry_datetime', $inquiry_at_start, $inquiry_at_end]);
        }

        return $dataProvider;
    }
}
