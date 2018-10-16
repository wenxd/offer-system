<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = 'Create Stock Log';
$this->params['breadcrumbs'][] = ['label' => 'Stock Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
