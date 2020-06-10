<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/15
 * Time: 19:07
 */

namespace app\controllers;

use app\models\AuthAssignment;
use Yii;

class AdminUserController extends BaseController
{
    public function actionCheckRole()
    {
        $admin_id  = Yii::$app->request->post('admin_id');
        $role_name = Yii::$app->request->post('role_name');

        $isFind = AuthAssignment::find()->where(['user_id' => $admin_id, 'item_name' => $role_name])->one();

        if (!$isFind) {
            return json_encode(['code' => 500, 'msg' => '此员工没有配置角色']);
        }
        return json_encode(['code' => 200, 'msg' => '此员工配置了']);
    }

}
