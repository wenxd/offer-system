<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\InquiryBetter;
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
                'label'     => '是否加工',
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
            'price',
            'inquiry_datetime',
            'sort',
            [
                'attribute' => 'is_better',
                'value'     => function ($model) {
                    return InquiryBetter::$better[$model->is_better];
                }
            ],
            [
                'attribute' => 'is_newest',
                'value'     => function ($model) {
                    return InquiryBetter::$newest[$model->is_newest];
                }
            ],
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
