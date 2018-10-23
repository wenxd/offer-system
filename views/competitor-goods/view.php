<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CompetitorGoods */

$this->title = '详情';
$this->params['breadcrumbs'][] = ['label' => '竞争对手与零件列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competitor-goods-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_id',
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
            'competitor_id',
            [
                'attribute' => 'competitor_name',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->competitor) {
                        return $model->competitor->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'customer',
                'contentOptions'=>['style'=>'min-width: 100px;'],
                'label'     => '针对客户',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->customer && $model->customers) {
                        return $model->customers->name;
                    } else {
                        return '';
                    }
                }
            ],
            'tax_rate',
            'price',
            'tax_price',
            'offer_date',
            'remark',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
