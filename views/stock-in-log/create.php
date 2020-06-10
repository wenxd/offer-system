<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = '添加入库记录';
$this->params['breadcrumbs'][] = ['label' => '入库列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
