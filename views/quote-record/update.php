<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteRecord */

$this->title = '更新询价单';
$this->params['breadcrumbs'][] = ['label' => '询价单列表', 'url' => ['order-inquiry/detail', 'id' => $model->id]];
?>
<div class="quote-record-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
