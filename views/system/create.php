<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SystemConfig */

$this->title = '创建系统配置';
$this->params['breadcrumbs'][] = ['label' => '系统配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
