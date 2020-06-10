<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PurchaseGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-goods-view">

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
            'order_final_id',
            'order_purchase_id',
            'order_purchase_sn',
            'goods_id',
            'type',
            'relevance_id',
            'is_purchase',
            'number',
            'is_deleted',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
