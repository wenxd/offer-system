<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderPayment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'payment_sn',
            'order_id',
            'order_purchase_id',
            'order_purchase_sn',
            'goods_info',
            'payment_at',
            'is_payment',
            'admin_id',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
