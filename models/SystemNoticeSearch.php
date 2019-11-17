<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SystemNotice;
use yii\helpers\ArrayHelper;

/**
 * SystemNoticeSearch represents the model behind the search form of `app\models\SystemNotice`.
 */
class SystemNoticeSearch extends SystemNotice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'admin_id', 'is_read', 'is_deleted'], 'integer'],
            [['content', 'notice_at', 'updated_at', 'created_at'], 'safe'],
            [['id', 'content', 'notice_at', 'updated_at', 'created_at'], 'trim'],
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
//        $use_admin = AuthAssignment::find()->where(['item_name' => ['询价员', '采购员', '库管员']])->all();
//        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        $userId   = Yii::$app->user->identity->id;
        $query = self::find()->where(['admin_id' => $userId]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['id', 'notice_at']
            ]
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
            'admin_id'   => $this->admin_id,
            'is_read'    => $this->is_read,
            'is_deleted' => self::IS_DELETED_NO,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        if ($this->notice_at && strpos($this->notice_at, ' - ')) {
            list($notice_at_start, $notice_at_end) = explode(' - ', $this->notice_at);
            $notice_at_start .= ' 00:00:00';
            $notice_at_end   .= ' 23::59:59';
            $query->andFilterWhere(['between', 'notice_at', $notice_at_start, $notice_at_end]);
        }

        $query->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
