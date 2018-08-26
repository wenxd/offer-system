<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%supplier}}".
 *
 * @property int $id 自增id
 * @property string $name 供应商名称
 * @property string $mobile 供应商电话
 * @property string $telephone 供应商座机
 * @property string $email 供应商邮箱
 * @property int $sort 排序
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Supplier extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    # 修改之前
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                #设置默认值
                'value' => date('Y-m-d H:i:s', time())
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%supplier}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'mobile', 'telephone', 'email'], 'string', 'max' => 255],
            [
                ['name', 'mobile', 'email'],
                'required',
                'on' => 'supplier'
            ],
            [['mobile'],'match','pattern'=>'/^[1][0-9]{10}$/'],
            ['email', 'email']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'name' => '供应商名称',
            'mobile' => '供应商电话',
            'telephone' => '供应商座机',
            'email' => '供应商邮箱',
            'sort' => '排序',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->is_deleted == self::IS_DELETED_YES) {
            $inquiryIsHasSupplier = Inquiry::find()->where(['supplier_id' => $this->id])->one();
            $stockIsHasSupplier   = Stock::find()->where(['supplier_id' => $this->id])->one();
            if ($inquiryIsHasSupplier || $stockIsHasSupplier) {
                $this->addError('id', '此供应商下有零件了，不能删除');
                return false;
            }
        }

        if (!$insert) {
            Stock::updateAll(['supplier_name' => $this->name], ['supplier_id' => $this->id]);
            Inquiry::updateAll(['supplier_name' => $this->name], ['supplier_id' => $this->id]);
        }

        return parent::beforeSave($insert);
    }

    public static function getCreateDropDown()
    {
        $list = self::find()->where(['is_deleted' => static::IS_DELETED_NO])->all();

        $return = [];
        foreach ($list as $row) {
            $return[$row->id] = $row->name;
        }
        return $return;
    }
}
