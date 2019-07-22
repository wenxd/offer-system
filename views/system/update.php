<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SystemConfig */

$this->title = 'Update System Config: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'System Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="system-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
