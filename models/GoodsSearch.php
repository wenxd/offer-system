<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Goods;
use yii\helpers\ArrayHelper;

/**
 * GoodsSearch represents the model behind the search form of `app\models\Goods`.
 */
class GoodsSearch extends Goods
{
    public $is_inquiry;
    public $is_inquiry_better;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_process', 'is_deleted', 'is_special', 'is_nameplate', 'is_emerg', 'is_assembly', 'is_inquiry'
            , 'is_inquiry_better'], 'integer'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'original_company_remark',
                'unit', 'technique_remark', 'img_id', 'nameplate_img_id', 'updated_at', 'created_at', 'device_info', 'material'], 'safe'],
            [['goods_number', 'goods_number_b', 'description', 'description_en', 'original_company', 'original_company_remark',
                'technique_remark', 'device_info', 'material'], 'trim'],
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
        $goods = self::find()->all();
        $goodsIds = ArrayHelper::getColumn($goods, 'id');
        if ($this->is_inquiry === '0') {
            $where = [];
            if ($this->is_inquiry_better === '0') {
                $where['is_better'] = 0;
            }
            if ($this->is_inquiry_better === '1') {
                $where['is_better'] = 1;
            }
            $inquiry = Inquiry::find()->where($where)->all();
            $inquiryGoodsIds = ArrayHelper::getColumn($inquiry, 'good_id');
            $query->andWhere(['not in', 'id', $inquiryGoodsIds]);
        }
        if ($this->is_inquiry === '1') {
            $where = [];
            if ($this->is_inquiry_better === '0') {
                $where['is_better'] = 0;
            }
            if ($this->is_inquiry_better === '1') {
                $where['is_better'] = 1;
            }
            $inquiry = Inquiry::find()->where($where)->andWhere(['good_id' => $goodsIds])->all();
            $inquiryGoodsIds = ArrayHelper::getColumn($inquiry, 'good_id');
            $query->andWhere(['id' => $inquiryGoodsIds]);
        }
        if ($this->is_inquiry === '') {
            $where = [];
            if ($this->is_inquiry_better === '0') {
                $where['is_better'] = 0;
            }
            if ($this->is_inquiry_better === '1') {
                $where['is_better'] = 1;
            }
            $inquiry = Inquiry::find()->where($where)->andWhere(['good_id' => $goodsIds])->all();
            $inquiryGoodsIds = ArrayHelper::getColumn($inquiry, 'good_id');
            $query->andWhere(['id' => $inquiryGoodsIds]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'goods.id' => $this->id,
            'goods.is_process'   => $this->is_process,
            'goods.is_special'   => $this->is_special,
            'goods.is_nameplate' => $this->is_nameplate,
            'goods.is_emerg'     => $this->is_emerg,
            'goods.is_assembly'  => $this->is_assembly,
            'goods.is_deleted'   => self::IS_DELETED_NO,
        ]);

        $query->andFilterWhere(['like', 'goods.goods_number', $this->goods_number])
            ->andFilterWhere(['like', 'goods.goods_number_b', $this->goods_number_b])
            ->andFilterWhere(['like', 'goods.description', $this->description])
            ->andFilterWhere(['like', 'goods.description_en', $this->description_en])
            ->andFilterWhere(['like', 'goods.original_company', $this->original_company])
            ->andFilterWhere(['like', 'goods.original_company_remark', $this->original_company_remark])
            ->andFilterWhere(['like', 'goods.unit', $this->unit])
            ->andFilterWhere(['like', 'goods.technique_remark', $this->technique_remark])
            ->andFilterWhere(['like', 'goods.img_id', $this->img_id])
            ->andFilterWhere(['like', 'goods.material', $this->material])
            ->andFilterWhere(['like', 'goods.device_info', $this->device_info]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'goods.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'goods.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
