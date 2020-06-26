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
            'purchase_sn',
            'goods_id',
            'number',
            'type',
            [
                'attribute' => 'operate_time',
                'label'     => '出库时间',
            ],
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
