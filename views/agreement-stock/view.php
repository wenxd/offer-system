<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementStock */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agreement Stocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="agreement-stock-view">

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
            'order_agreement_id',
            'order_agreement_sn',
            'order_purchase_id',
            'order_purchase_sn',
            'order_payment_id',
            'order_payment_sn',
            'goods_id',
            'price',
            'tax_price',
            'use_number',
            'all_price',
            'all_tax_price',
        ],
    ]) ?>

</div>
