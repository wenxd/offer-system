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
            'competitor_price',
            'offer_date',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
