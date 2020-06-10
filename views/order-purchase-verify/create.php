<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderPayment */

$this->title = 'Create Order Payment';
$this->params['breadcrumbs'][] = ['label' => 'Order Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
