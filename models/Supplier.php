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
 * @property string $short_name 供应商名称
 * @property string $mobile 供应商电话
 * @property string $telephone 供应商座机
 * @property string $email 供应商邮箱
 * @property int $sort 排序
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $full_name
 * @property string $admin_id
 * @property string $agree_at
 * @property string $is_confirm
 */
class Supplier extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_CONFIRM_NO    = '0';
    const IS_CONFIRM_YES   = '1';

    public static $confirm = [
        self::IS_CONFIRM_NO  => '否',
        self::IS_CONFIRM_YES => '是',
    ];

    public static $grade = [
        '1' => '一级',
        '2' => '二级',
        '3' => '三级',
    ];

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
            [['sort', 'is_deleted', 'is_confirm', 'admin_id'], 'integer'],
            [['updated_at', 'created_at', 'agree_at'], 'safe'],
            [['name', 'short_name', 'mobile', 'telephone', 'email', 'grade', 'grade_reason', 'advantage_product',
                'full_name', 'contacts'], 'string', 'max' => 255],
            [
                ['name', 'short_name', 'grade_reason', 'advantage_product'],
                'required',
                'on' => 'supplier'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                   => '自增id',
            'name'                 => '供应商名称',
            'short_name'           => '供应商简称',
            'contacts'             => '供应商联系人',
            'mobile'               => '供应商电话',
            'telephone'            => '供应商座机',
            'email'                => '供应商邮箱',
            'sort'                 => '排序',
            'grade'                => '评级',
            'grade_reason'         => '入库原因',
            'advantage_product'    => '优势产品',
            'is_confirm'           => '审批',
            'is_deleted'           => '是否删除：0未删除 1已删除',
            'updated_at'           => '更新时间',
            'created_at'           => '创建时间',
            'full_name'            => '供应商全称',
            'admin_id'             => '申请人',
            'agree_at'             => '入库时间',
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
        //超级管理员
        $user_super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
        $admin_name = Yii::$app->user->identity->username;
        $admin_id = Yii::$app->user->identity->id;
        if ($insert) {
            $this->admin_id = $admin_id;
            $isHasSupplier = Supplier::find()->where(['name' => $this->name])->one();
            if ($isHasSupplier) {
                $this->addError('name', '此供应商名称已存在');
                return false;
            }
            $isHasSupplier = Supplier::find()->where(['short_name' => $this->short_name])->one();
            if ($isHasSupplier) {
                $this->addError('short_name', '此供应商简称已存在');
                return false;
            }
        }
        if ($admin_id != $user_super->user_id) {
            //给超管通知
            $notice = new SystemNotice();
            $notice->admin_id = $user_super->user_id;
            $notice->content = $admin_name . '创建了供应商' . $this->name . '，请确认';
            $notice->notice_at = date('Y-m-d H:i:s');
            $notice->save();
        }
        if ($this->is_confirm && !$this->agree_at) {
            $this->agree_at = date('Y-m-d H:i:s');
        }

        $this->name = trim($this->name);
        $this->short_name = trim($this->short_name);
        $this->full_name = trim($this->full_name);

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

    public static function getAllDropDown()
    {
        $list = self::find()->all();

        $return = [];
        foreach ($list as $row) {
            $return[$row->id] = $row->name;
        }
        return ['' => '请选择'] + $return;
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}
