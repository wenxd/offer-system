<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inquiry_goods".
 *
 * @property int $id
 * @property string $inquiry_sn 询价单号
 * @property int $goods_id 零件ID
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property int $is_inquiry 是否询价 0否 1是
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $not_result_at
 * @property string $is_result
 * @property string $reason
 * @property string $admin_id
 * @property string $inquiry_at
 * @property string $is_result_tag
 * @property string $number
 * @property string $order_id
 * @property string $order_inquiry_id
 * @property string $supplier_id
 * @property string $remark
 * @property int $level 零件等级：1顶级，2子级
 * @property string $belong_to 所属json
 */
class InquiryGoods extends \yii\db\ActiveRecord
{
    const IS_DELETED_NO   = '0';
    const IS_DELETED_YES  = '1';

    const IS_INQUIRY_NO  = '0';
    const IS_INQUIRY_YES = '1';
    //是否寻不出
    const IS_RESULT_NO   = '0'; //否
    const IS_RESULT_YES  = '1'; //是

    public static $Inquiry = [
        self::IS_INQUIRY_NO  => '否',
        self::IS_INQUIRY_YES => '是',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inquiry_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'is_deleted', 'is_result', 'admin_id', 'is_result_tag', 'number', 'is_inquiry', 'level', 'order_id', 'order_inquiry_id'], 'integer'],
            [['updated_at', 'created_at', 'not_result_at', 'inquiry_at', 'supplier_id'], 'safe'],
            [['inquiry_sn', 'reason', 'remark', 'belong_to', 'serial'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'inquiry_sn'    => '询价单号',
            'goods_id'      => '零件ID',
            'is_deleted'    => '是否删除：0未删除 1已删除',
            'updated_at'    => '更新时间',
            'created_at'    => '创建时间',
            'not_result_at' => '未询出时间',
            'reason'        => '澄清问题',
            'admin_id'      => '询价员',
            'is_inquiry'    => '是否询价',
            'inquiry_at'    => '确认询价时间',
            'number'        => '数量',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getOrderInquiry()
    {
        return $this->hasOne(OrderInquiry::className(), ['inquiry_sn' => 'inquiry_sn']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * 澄清
     */
    public function getClarify()
    {
        return $this->hasMany(InquiryGoodsClarify::className(), ['inquiry_goods_id' => 'id']);
    }
}
