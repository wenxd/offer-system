<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = '更新入库';
$this->params['breadcrumbs'][] = ['label' => '入库记录', 'url' => ['stock-in-log/index']];
?>
<div class="stock-log-update">

    <?= $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
