<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = '更新入库';
$this->params['breadcrumbs'][] = ['label' => 'Stock Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stock-log-update">

    <?= $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
