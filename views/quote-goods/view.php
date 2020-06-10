<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-goods-view">

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
            'order_final_sn',
            'order_quote_id',
            'order_quote_sn',
            'goods_id',
            'type',
            'relevance_id',
            'number',
            'is_quote',
            'is_deleted',
            'updated_at',
            'created_at',
            'serial',
            'tax_rate',
            'price',
            'tax_price',
            'all_price',
            'all_tax_price',
            'quote_price',
            'quote_tax_price',
            'quote_all_price',
            'quote_all_tax_price',
            'delivery_time:datetime',
        ],
    ]) ?>

</div>
