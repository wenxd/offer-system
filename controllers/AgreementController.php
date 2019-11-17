<?php
namespace app\controllers;

use Yii;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;

class AgreementController extends BaseController
{
    public function actionIndex()
    {
        $userId   = Yii::$app->user->identity->id;
        //询价员
        $use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务', '报价员']])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

        if (in_array($userId, $adminIds)) {
            return $this->redirect(['order-agreement/index']);
        } else {
            return $this->redirect(['order-payment/index']);
        }
    }
}
