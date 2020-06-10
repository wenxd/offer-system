<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderPurchase */

$this->title = 'Create Order Purchase';
$this->params['breadcrumbs'][] = ['label' => 'Order Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-purchase-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
