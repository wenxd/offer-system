<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderPurchase */

$this->title = '添加入库记录';
$this->params['breadcrumbs'][] = ['label' => 'Order Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-purchase-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
