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
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
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
            [['goods_id', 'is_deleted'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['inquiry_sn'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inquiry_sn' => '询价单号',
            'goods_id' => '零件ID',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
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
}
