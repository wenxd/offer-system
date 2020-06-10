<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Inquiry;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */

$this->title = '询价详情';
$this->params['breadcrumbs'][] = ['label' => '询价列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-view">

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
            [
                'attribute' => 'original_company',
                'label'     => '原厂家',
                'value'     => function ($model) {
                    if ($model->goods) {
                        return $model->goods->original_company;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company_remark',
                'label'     => '原厂家备注',
                'value'     => function ($model) {
                    if ($model->goods) {
                        return $model->goods->original_company_remark;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'unit',
                'label'     => '单位',
                'contentOptions'=>['style'=>'min-width: 70px;'],
                'value'     => function ($model) {
                    if ($model->goods) {
                        return $model->goods->unit;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'technique_remark',
                'label'     => '技术备注',
                'value'     => function ($model) {
                    if ($model->goods) {
                        return $model->goods->technique_remark;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'is_process',
                'label'     => '加工',
                'filter'    => Goods::$process,
                'value'     => function ($model) {
                    if ($model->goods) {
                        return Goods::$process[$model->goods->is_process];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'img_id',
                'label'     => '图纸',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->goods) {
                        return HTML::img($model->goods->img_url, ['width' => '300px']);
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
            'delivery_time',
            [
                'attribute' => 'inquiry_datetime',
                'value'     => function($model){
                    return substr($model->inquiry_datetime, 0, 10);
                }
            ],
            'sort',
            [
                'attribute' => 'is_better',
                'value'     => function ($model) {
                    return Inquiry::$better[$model->is_better];
                }
            ],
            'better_reason',
            [
                'attribute' => 'is_newest',
                'value'     => function ($model) {
                    return Inquiry::$newest[$model->is_newest];
                }
            ],
            [
                'attribute' => 'created_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
        ],
    ]) ?>

</div>
