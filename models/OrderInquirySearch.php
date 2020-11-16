<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderInquiry;
use yii\helpers\ArrayHelper;

/**
 * OrderInquirySearch represents the model behind the search form of `app\models\OrderInquiry`.
 */
class OrderInquirySearch extends OrderInquiry
{
    public $order_sn;
    public $order_end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'is_inquiry', 'admin_id', 'is_deleted'], 'integer'],
            [['inquiry_sn', 'goods_info', 'end_date', 'updated_at', 'created_at', 'order_sn'], 'safe'],
            [['inquiry_sn', 'order_sn'], 'trim'],
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
        $userId   = Yii::$app->user->identity->id;
        $userName = Yii::$app->user->identity->username;
        //询价员
        $use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        if (in_array($userId, $adminIds)) {
            $query = OrderInquiry::find()->where(['admin_id' => $userId]);
        } else {
            $query = OrderInquiry::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'end_date', 'updated_at', 'created_at']
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
            'order_inquiry.id'         => $this->id,
            'order_inquiry.order_id'   => $this->order_id,
            'order_inquiry.is_inquiry' => $this->is_inquiry,
            'order_inquiry.admin_id'   => $this->admin_id,
            'order_inquiry.is_deleted' => $this->is_deleted,
        ]);

        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = order_inquiry.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }
        $query->andFilterWhere(['like', 'inquiry_sn', $this->inquiry_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        if ($this->end_date && strpos($this->end_date, ' - ')) {
            list($end_date_start, $end_date_end) = explode(' - ', $this->end_date);
            $query->andFilterWhere(['between', 'order_inquiry.end_date', $end_date_start, $end_date_end]);
        }

        if ($this->final_at && strpos($this->final_at, ' - ')) {
            list($final_at_start, $final_at_end) = explode(' - ', $this->final_at);
            $final_at_start .= ' 00:00:00';
            $final_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_inquiry.final_at', $final_at_start, $final_at_end]);
        }

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_inquiry.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_inquiry.created_at', $created_at_start, $created_at_end]);
        }
        return $dataProvider;
    }
}
