<?php

namespace app\models;

use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * OrderQuoteSearch represents the model behind the search form of `app\models\OrderQuote`.
 */
class OrderQuoteSearch extends OrderQuote
{
    public $order_sn;
    public $order_final_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'is_quote', 'admin_id', 'is_deleted', 'customer_id', 'quote_only_one'], 'integer'],
            [['quote_sn', 'goods_info', 'agreement_date', 'updated_at', 'created_at', 'order_sn', 'order_final_sn'], 'safe'],
            [['id', 'quote_sn', 'order_sn', 'order_final_sn'], 'trim'],
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
        $use_admin = AuthAssignment::find()->where(['item_name' => '报价员'])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

        $userId   = Yii::$app->user->identity->id;
        if (in_array($userId, $adminIds)) {
            $query = self::find()->where(['admin_id' => $userId]);
        } else {
            $query = self::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id']
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
            'order_quote.id'             => $this->id,
            'order_quote.order_id'       => $this->order_id,
            'order_quote.agreement_date' => $this->agreement_date,
            'order_quote.is_quote'       => $this->is_quote,
            'order_quote.admin_id'       => $this->admin_id,
            'order_quote.is_deleted'     => $this->is_deleted,
            'order_quote.customer_id'    => $this->customer_id,
            'order_quote.quote_only_one' => $this->quote_only_one,
        ]);

        if ($this->order_sn) {
            $query->leftJoin('order as a', 'a.id = order_quote.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $this->order_sn]);
        }

        if ($this->order_final_sn) {
            $query->leftJoin('order_final as of', 'of.id = order_quote.order_final_id');
            $query->andFilterWhere(['like', 'of.final_sn', $this->order_final_sn]);
        }

        $query->andFilterWhere(['like', 'quote_sn', $this->quote_sn])
            ->andFilterWhere(['like', 'goods_info', $this->goods_info]);

        if ($this->updated_at && strpos($this->updated_at, ' - ')) {
            list($updated_at_start, $updated_at_end) = explode(' - ', $this->updated_at);
            $updated_at_start .= ' 00:00:00';
            $updated_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_quote.updated_at', $updated_at_start, $updated_at_end]);
        }

        if ($this->created_at && strpos($this->created_at, ' - ')) {
            list($created_at_start, $created_at_end) = explode(' - ', $this->created_at);
            $created_at_start .= ' 00:00:00';
            $created_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'order_quote.created_at', $created_at_start, $created_at_end]);
        }

        return $dataProvider;
    }
}
