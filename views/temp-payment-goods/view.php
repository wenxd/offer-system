<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-goods-view">

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
            'order_id',
            'order_payment_id',
            'order_payment_sn',
            'order_purchase_id',
            'order_purchase_sn',
            'purchase_goods_id',
            'serial',
            'goods_id',
            'type',
            'relevance_id',
            'number',
            'tax_rate',
            'price',
            'tax_price',
            'all_price',
            'all_tax_price',
            'fixed_price',
            'fixed_tax_price',
            'fixed_all_price',
            'fixed_all_tax_price',
            'fixed_number',
            'inquiry_admin_id',
            'updated_at',
            'created_at',
            'is_quality',
            'supplier_id',
            'delivery_time',
            'before_supplier_id',
            'before_delivery_time',
        ],
    ]) ?>

</div>
