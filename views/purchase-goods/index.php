<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\PurchaseGoods;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchaseGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '采购记录列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'label'     => '零件号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'goods_number_b',
                'format'    => 'raw',
                'label'     => '厂家号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number_b',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number_b, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format'    => 'raw',
                'label'     => '中文描述',
                'filter'    => Html::activeTextInput($searchModel, 'description',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description_en',
                'format'    => 'raw',
                'label'     => '英文描述',
                'filter'    => Html::activeTextInput($searchModel, 'description_en',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description_en;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company',
                'format'    => 'raw',
                'label'     => '原厂家',
                'filter'    => Html::activeTextInput($searchModel, 'original_company',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->original_company;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'delivery_time',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->delivery_time;
                }
            ],
            [
                'attribute' => 'tax_rate',
                'format'    => 'raw',
                'label'     => '税率',
                'filter'    => Html::activeTextInput($searchModel, 'tax_rate',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->tax_rate;
                }
            ],
            [
                'attribute' => 'fixed_price',
                'format'    => 'raw',
                'label'     => '采购未税单价',
                'filter'    => Html::activeTextInput($searchModel, 'fixed_price',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_price;
                }
            ],
            [
                'attribute' => 'fixed_tax_price',
                'format'    => 'raw',
                'label'     => '采购含税单价',
                'filter'    => Html::activeTextInput($searchModel, 'fixed_tax_price',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_tax_price;
                }
            ],
            [
                'attribute' => 'fixed_number',
                'format'    => 'raw',
                'label'     => '采购数量',
                'filter'    => Html::activeTextInput($searchModel, 'fixed_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_number;
                }
            ],
            [
                'attribute' => 'unit',
                'format'    => 'raw',
                'label'     => '单位',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->goods->unit;
                }
            ],
            [
                'attribute' => 'fixed_all_price',
                'format'    => 'raw',
                'label'     => '采购未税总价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_price * $model->fixed_number;
                }
            ],
            [
                'attribute' => 'fixed_all_tax_price',
                'format'    => 'raw',
                'label'     => '采购含税总价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_tax_price * $model->fixed_number;
                }
            ],
            [
                'attribute' => 'order_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'order_purchase_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
