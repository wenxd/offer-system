<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\grid\CheckboxColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderFinalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '最终订单列表';
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
                'attribute' => 'customer',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'customer',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->customer->name, Url::to(['customer/view', 'id' => $model->order->customer->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'short_name',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'short_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->customer->short_name, Url::to(['customer/view', 'id' => $model->order->customer->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'manage_name',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'manage_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return $model->order->manage_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'provide_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderFinalSearch[provide_date]',
                    'value' => Yii::$app->request->get('OrderFinalSearch')['provide_date'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->order->provide_date;
                }
            ],
            [
                'attribute' => 'final_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->final_sn, Url::to(['order-final/detail', 'id' => $model->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderFinalSearch[updated_at]',
                    'value' => Yii::$app->request->get('OrderFinalSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderFinalSearch[created_at]',
                    'value' => Yii::$app->request->get('OrderFinalSearch')['created_at'],
                ])
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'value'          => function ($model, $key, $index, $column){
                    return Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-xs btn-flat',
                        ]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>