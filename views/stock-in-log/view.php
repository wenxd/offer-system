<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Stock Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-log-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'order_purchase_id',
            'goods_id',
            'number',
            'type',
            'operate_time',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
