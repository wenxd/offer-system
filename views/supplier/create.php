<?php

use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Supplier */

$this->title = '创建供应商';

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId = Yii::$app->user->identity->id;
if (!in_array($userId, $adminIds)) {
    $this->params['breadcrumbs'][] = ['label' => '供应商列表', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}

?>
<div class="supplier-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
