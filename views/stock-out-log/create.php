<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = '添加出库记录';
$this->params['breadcrumbs'][] = ['label' => '出库列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
