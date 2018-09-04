<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = '库存详情';
$this->params['breadcrumbs'][] = ['label' => '库存列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'good_id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->goods) {
                        return $model->goods->goods_number;
                    } else {
                        return '';
                    }
                }
            ],
            'supplier_id',
            [
                'attribute' => 'supplier_name',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->supplier) {
                        return $model->supplier->name;
                    } else {
                        return '';
                    }
                }
            ],
            'tax_rate',
            'price',
            'tax_price',
            'position',
            'number',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
