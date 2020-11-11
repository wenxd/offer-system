<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Supplier;
use yii\helpers\ArrayHelper;

/**
 * SupplierSearch represents the model behind the search form of `backend\models\Supplier`.
 */
class SupplierSearch extends Supplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sort', 'is_deleted', 'is_confirm', 'admin_id'], 'integer'],
            [['name', 'short_name', 'mobile', 'telephone', 'email', 'updated_at', 'created_at', 'grade',
                'grade_reason', 'advantage_product', 'full_name', 'contacts', 'agree_at'], 'safe'],
            [['id', 'name', 'mobile', 'telephone', 'email', 'grade', 'grade_reason', 'advantage_product',
                'full_name', 'contacts'], 'trim']
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
//        $use_admin = AuthAssignment::find()->where(['item_name' => ['询价员', '采购员']])->all();
//        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        $userId   = Yii::$app->user->identity->id;
        $use_admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        $super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
        if (!in_array($userId, $adminIds)) {
            $query = Supplier::find()->where(['is_confirm' => self::IS_CONFIRM_YES])
            ->andWhere(['admin_id' => $userId]);
        } else {
            $query = Supplier::find();
        }
//        if (Yii::$app->user->identity->username != 'admin') {
//            $query->andWhere(['admin_id' => $userId]);
//        }

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
            'id'         => $this->id,
            'sort'       => $this->sort,
            'is_deleted' => self::IS_DELETED_NO,
            'is_confirm' => $this->is_confirm,
            'grade'      => $this->grade,
            'admin_id'   => $this->admin_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'grade_reason', $this->grade_reason])
            ->andFilterWhere(['like', 'advantage_product', $this->advantage_product])
            ->andFilterWhere(['like', 'contacts', $this->contacts])
            ->andFilterWhere(['like', 'full_name', $this->full_name]);

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

        if ($this->agree_at && strpos($this->agree_at, ' - ')) {
            list($agree_at_start, $agree_at_end) = explode(' - ', $this->agree_at);
            $agree_at_start .= ' 00:00:00';
            $agree_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'agree_at', $agree_at_start, $agree_at_end]);
        }
        return $dataProvider;
    }
}
