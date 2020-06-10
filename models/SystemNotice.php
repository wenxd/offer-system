<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_notice".
 *
 * @property int $id
 * @property int $admin_id 后端用户ID
 * @property string $content 通知详情
 * @property int $is_read 是否已读：0未读 1已读
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $notice_at 通知时间
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class SystemNotice extends \yii\db\ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_READ_NO       = '0';
    const IS_READ_YES      = '1';

    public static $read = [
        self::IS_READ_NO  => '未读',
        self::IS_READ_YES => '已读',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_notice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['admin_id', 'is_read', 'is_deleted'], 'integer'],
            [['notice_at', 'updated_at', 'created_at'], 'safe'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'admin_id'   => '后端用户ID',
            'content'    => '通知详情',
            'is_read'    => '是否已读',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'notice_at'  => '通知时间',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
