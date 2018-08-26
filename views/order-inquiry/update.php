<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = 'Update Order Inquiry: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Inquiries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-inquiry-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
