<?php

namespace app\models;
use Yii;
use yii\helpers\ArrayHelper;

class Helper
{
    public static function getAdminList($roleArr)
    {
        $use_admin = AuthAssignment::find()->where(['item_name' => $roleArr])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
        $adminList = Admin::find()->where(['id' => $adminIds])->all();
        $admins = [];
        foreach ($adminList as $key => $admin) {
            $admins[$admin->id] = $admin->username;
        }
        return ['' => '请选择'] + $admins;
    }

    public static function getAdminListAll()
    {
        $adminList = Admin::find()->where(['status' => Admin::STATUS_ACTIVE])->all();
        $admins = [];
        foreach ($adminList as $key => $admin) {
            $admins[$admin->id] = $admin->username;
        }
        return $admins;
    }
}
