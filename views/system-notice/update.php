<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SystemNotice */

$this->title = 'Update System Notice: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'System Notices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="system-notice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
