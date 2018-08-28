<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id 自增id
 * @property string $name 客户名称
 * @property string $mobile 客户电话
 * @property string $company_telephone 公司电话
 * @property string $company_fax 公司传真
 * @property string $company_address 公司地址
 * @property string $company_email 公司邮箱
 * @property string $company_contacts 公司联系人
 */
class Customer extends \yii\db\ActiveRecord
{
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
            [['name', 'mobile', 'company_telephone', 'company_fax', 'company_address', 'company_email', 'company_contacts'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'name' => '客户名称',
            'mobile' => '客户电话',
            'company_telephone' => '公司电话',
            'company_fax' => '公司传真',
            'company_address' => '公司地址',
            'company_email' => '公司邮箱',
            'company_contacts' => '公司联系人',
        ];
    }
}
