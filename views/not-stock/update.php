<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempNotStock */

$this->title = 'Update Temp Not Stock: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temp Not Stocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="temp-not-stock-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
