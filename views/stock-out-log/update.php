<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = '更新出库';
$this->params['breadcrumbs'][] = ['label' => '出库列表', 'url' => ['stock-out-log/index']];
?>
<div class="stock-log-update">

    <?= $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
