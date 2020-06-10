<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SystemConfig */

$this->title = '更新系统配置';
$this->params['breadcrumbs'][] = ['label' => '系统配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
?>
<div class="system-config-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
