<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%inquiry}}".
 *
 * @property int $id 自增id
 * @property string $good_id 厂家号
 * @property int $supplier_id 供应商ID
 * @property string $price 咨询价格
 * @property string $inquiry_datetime 咨询时间
 * @property int $sort 排序
 * @property int $is_better 是否优选：0否 1是
 * @property int $is_newest 是否最新询价：0否 1是
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $number  询价数量
 * @property string $all_price 未税总价
 * @property string $tax_price
 * @property string $tax_rate
 * @property string $is_upload
 * @property string $is_confirm_better
 * @property string $better_reason
 * @property string $delivery_time
 * @property string $is_purchase
 */
class Inquiry extends ActiveRecord
{
    const IS_DELETED_NO   = '0';
    const IS_DELETED_YES  = '1';

    const IS_BETTER_NO    = '0';
    const IS_BETTER_YES   = '1';

    const IS_NEWEST_NO    = '0';
    const IS_NEWEST_YES   = '1';

    const IS_PRIORITY_NO  = '0';
    const IS_PRIORITY_YES = '1';

    const IS_UPLOAD_NO     = '0';
    const IS_UPLOAD_YES    = '1';

    const IS_PURCHASE_NO     = '0';
    const IS_PURCHASE_YES    = '1';

    public static $newest = [
        self::IS_NEWEST_NO   => '否',
        self::IS_NEWEST_YES  => '是',
    ];

    public static $better = [
        self::IS_BETTER_NO   => '否',
        self::IS_BETTER_YES  => '是',
    ];

    public static $priority = [
        self::IS_PRIORITY_NO   => '否',
        self::IS_UPLOAD_YES    => '是',
    ];

    public static $upload = [
        self::IS_UPLOAD_NO     => '否',
        self::IS_PRIORITY_YES  => '是',
    ];

    public static $purchase = [
        self::IS_PURCHASE_NO   => '否',
        self::IS_PURCHASE_YES  => '是',
    ];

    public $supplier_name;
    public $goods_number;
    public $goods_number_b;

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
        return 'inquiry';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['good_id', 'supplier_id', 'sort', 'is_better', 'is_newest', 'is_deleted', 'is_priority', 'admin_id',
                 'order_id', 'order_inquiry_id', 'is_upload', 'is_confirm_better', 'is_purchase'], 'integer'],
            [['updated_at', 'created_at', 'offer_date', 'supplier_name', 'goods_number', 'goods_number_b'], 'safe'],
            [['price', 'tax_rate', 'tax_price', 'number', 'all_price', 'all_tax_price', 'delivery_time'], 'number', 'min' => 0],
            [['inquiry_datetime', 'remark', 'better_reason', 'goods_number_b'], 'string', 'max' => 255],
            [['good_id', 'supplier_name', 'inquiry_datetime'], 'required', "on" => ["create", "update"]],
            [['price', 'tax_rate', 'tax_price', 'number', 'all_price', 'all_tax_price', 'delivery_time'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => '自增id',
            'good_id'               => '零件ID',
            'goods_number'          => '零件号',
            'goods_number_b'        => '厂家号',
            'supplier_id'           => '供应商ID',
            'supplier_name'         => '供应商名称',
            'price'                 => '未税单价',
            'tax_price'             => '含税单价',
            'tax_rate'              => '税率',
            'number'                => '询价数量',
            'all_price'             => '未税总价',
            'all_tax_price'         => '含税总价',
            'inquiry_datetime'      => '咨询时间',
            'offer_date'            => '交货日期',
            'delivery_time'         => '货期',
            'remark'                => '询价备注',
            'sort'                  => '排序',
            'is_better'             => '优选',
            'better_reason'         => '优选理由',
            'is_newest'             => '最新询价',
            'is_priority'           => '优先询价',
            'is_deleted'            => '是否删除：0未删除 1已删除',
            'updated_at'            => '更新时间',
            'created_at'            => '创建时间',
            'is_upload'             => '是否导入',
            'is_confirm_better'     => '是否确认优选',
            'is_purchase'           => '是否采购记录',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->is_deleted) {
            return parent::beforeSave($insert);
        }

        if ($insert) {
            self::updateAll(['is_newest' => self::IS_NEWEST_NO], ['good_id' => $this->good_id]);
            $this->is_newest = self::IS_NEWEST_YES;
        }

        $action = urldecode(Yii::$app->request->getQueryParam('r'));
        list($controller, $function) = explode('/', $action);

        if ($function == 'create' || $function == 'update' || $function == 'add') {
            $supplier = Supplier::find()->where(['name' => trim($this->supplier_name)])->one();
            if (!$supplier) {
                $this->addError('id', '此供应商不存在');
                return false;
            }
            $this->supplier_id = $supplier->id;

            if ($this->goods_number) {
                $goods = Goods::find()->where(['goods_number' => $this->goods_number])->one();
                if ($goods) {
                    $this->goods_number_b = $goods->goods_number_b;
                } else {
                    $this->addError('goods_number', '此零件A不存在');
                    return false;
                }
            }

            if ($this->goods_number_b) {
                $goods = Goods::find()->where(['goods_number_b' => $this->goods_number_b])->one();
                if ($goods) {
                    $this->goods_number = $goods->goods_number;
                } else {
                    $this->addError('goods_number_b', '此零件B不存在');
                    return false;
                }
            }

            if (!$this->goods_number && !$this->goods_number_b) {
                $this->addError('id', '厂家号不能为空');
                return false;
            }
        }

        if ($this->is_better) {
            self::updateAll(['is_better' => self::IS_BETTER_NO, 'is_confirm_better' => 1], ['good_id' => $this->good_id]);
        }

        if ($function == 'add') {
            $use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
            $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
            $admin_id = Yii::$app->user->identity->id;
            if (in_array($admin_id, $adminIds)) {
                $superAdmin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
                $systemNotice = new SystemNotice();
                $systemNotice->admin_id  = $superAdmin->user_id;
                $systemNotice->content   = Yii::$app->user->identity->username . '有优选询价记录，请确认';
                $systemNotice->notice_at = date('Y-m-d H:i:s');
                $systemNotice->save();
            }
        }

        return parent::beforeSave($insert);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderInquiry()
    {
        return $this->hasOne(OrderInquiry::className(), ['id' => 'order_inquiry_id']);
    }
}
