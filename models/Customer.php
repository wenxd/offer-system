<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer".
 *
 * @property int $id 自增id
 * @property string $name 客户名称
 * @property string $short_name 客户简称
 * @property string $mobile 客户电话
 * @property string $company_telephone 公司电话
 * @property string $company_fax 公司传真
 * @property string $company_address 公司地址
 * @property string $company_email 公司邮箱
 * @property string $company_contacts 公司联系人
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Customer extends ActiveRecord
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
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'short_name', 'mobile', 'company_telephone', 'company_fax', 'company_address', 'company_email', 'company_contacts'], 'string', 'max' => 255],
            ['company_email', 'email'],
            [
                ['name', 'mobile'],
                'required',
                'on' => 'customer'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => '自增id',
            'name'              => '客户名称',
            'short_name'        => '客户简称',
            'mobile'            => '客户电话',
            'company_telephone' => '公司电话',
            'company_fax'       => '公司传真',
            'company_address'   => '公司地址',
            'company_email'     => '公司邮箱',
            'company_contacts'  => '公司联系人',
            'is_deleted'        => '是否删除',
            'updated_at'        => '更新时间',
            'created_at'        => '创建时间',
        ];
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

    public static function getAllDropDown()
    {
        $list = self::find()->all();

        $return = [];
        foreach ($list as $row) {
            $return[$row->id] = $row->name;
        }
        return ['0' => '请选择'] + $return;
    }

    public static function getSelectDropDown()
    {
        $list = self::find()->all();

        $return = [];
        foreach ($list as $row) {
            $return[$row->id] = $row->name;
        }
        return $return;
    }
}
