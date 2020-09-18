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
    public $goods_number_b;
    public $original_company;
    public $original_company_remark;
    public $unit;
    public $is_process;
    public $img_id;
    public $inquiry_sn;
    public $is_assembly;

    public $supplier_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'delivery_time', 'number',
                'admin_id', 'is_upload', 'is_confirm_better', 'is_purchase', 'is_assembly'], 'integer'],
            [['good_id', 'supplier_name', 'inquiry_datetime', 'updated_at', 'created_at', 'goods_number', 'tax_price',
                'offer_date', 'remark', 'original_company', 'original_company_remark', 'unit', 'technique_remark',
                'is_process', 'goods_number_b', 'tax_rate', 'inquiry_sn'], 'safe'],
            [['price', 'tax_price', 'all_price', 'all_tax_price'], 'number', 'min' => 0],
            [['id', 'good_id', 'supplier_id', 'supplier_name', 'price', 'tax_price', 'all_price', 'all_tax_price', 'remark'], 'trim']
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
                'attributes' => ['id', 'price', 'tax_price', 'delivery_time', 'inquiry_datetime', 'updated_at', 'created_at']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->goods_number || $this->original_company || $this->original_company_remark || $this->unit
            || $this->is_process || $this->goods_number_b) {
            $query->leftJoin('goods as a', 'a.id = inquiry.good_id');
            $query->andFilterWhere(['like', 'a.goods_number', $this->goods_number]);
            $query->andFilterWhere(['like', 'a.goods_number_b', $this->goods_number_b]);
            $query->andFilterWhere(['like', 'a.original_company', $this->original_company]);
            $query->andFilterWhere(['like', 'a.original_company_remark', $this->original_company_remark]);
            $query->andFilterWhere(['like', 'a.unit', $this->unit]);
            $query->andFilterWhere(['like', 'a.is_process', $this->is_process]);
        }
        if ($this->supplier_name) {
            $query->leftJoin('supplier as s', 's.id = inquiry.supplier_id');
            $query->andFilterWhere(['like', 's.name', $this->supplier_name]);
        }

        if ($this->inquiry_sn) {
            $query->leftJoin('order_inquiry as oi', 'oi.id = inquiry.order_inquiry_id');
            $query->andFilterWhere(['like', 'oi.inquiry_sn', $this->inquiry_sn]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'inquiry.id'                => $this->id,
            'inquiry.supplier_id'       => $this->supplier_id,
            'inquiry.price'             => $this->price,
            'inquiry.tax_price'         => $this->tax_price,
            'inquiry.delivery_time'     => $this->delivery_time,
            'inquiry.remark'            => $this->remark,
            'inquiry.sort'              => $this->sort,
            'inquiry.is_better'         => $this->is_better,
            'inquiry.is_newest'         => $this->is_newest,
            'inquiry.is_deleted'        => self::IS_BETTER_NO,
            'inquiry.admin_id'          => $this->admin_id,
            'inquiry.is_upload'         => $this->is_upload,
            'inquiry.is_confirm_better' => $this->is_confirm_better,
            'inquiry.is_purchase'       => $this->is_purchase,
            'inquiry.tax_rate'          => $this->tax_rate,
            'inquiry.technique_remark'  => $this->technique_remark,
            'inquiry.is_assembly'  => $this->is_assembly,
        ]);

        $query->andFilterWhere(['inquiry.good_id' => $this->good_id]);
        $query->andFilterWhere(['like', 'inquiry.remark', $this->remark]);

        if ($this->inquiry_datetime && strpos($this->inquiry_datetime, ' - ')) {
            list($inquiry_at_start, $inquiry_at_end) = explode(' - ', $this->inquiry_datetime);
            $inquiry_at_start .= ' 00:00:00';
            $inquiry_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry.inquiry_datetime', $inquiry_at_start, $inquiry_at_end]);
        }

        if ($this->offer_date && strpos($this->offer_date, ' - ')) {
            list($offer_date_start, $offer_date_end) = explode(' - ', $this->offer_date);
            $offer_date_start .= ' 00:00:00';
            $offer_date_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry.offer_date', $offer_date_start, $offer_date_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'inquiry.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
