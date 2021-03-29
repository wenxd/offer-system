<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "first_party".
 *
 * @property int $id
 * @property int $pid 父级ID
 * @property string $name 名称
 * @property int $user_id 创建人ID
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class FirstPartySearch extends FirstParty
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = self::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'updated_at', 'created_at']
            ],
            'pagination' => ['pageSize' => 10]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
