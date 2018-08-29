<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */

$this->title = '零件小详情';
$this->params['breadcrumbs'][] = ['label' => '零件管理列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_number',
            'description',
            'original_company',
            'original_company_remark',
            'unit',
            'technique_remark',
            [
                'attribute' => 'img_url',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->img_url, ['width' => 300]);
                }
            ],
            'competitor',
            'competitor_offer',
            [
                'attribute' => 'is_process',
                'value'     => function ($model) {
                    return Goods::$process[$model->is_process];
                }
            ],
            'offer_date',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
