<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\grid\CheckboxColumn;
use kartik\daterange\DateRangePicker;
use app\models\OrderFinal;
use app\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderFinalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '成本单列表';
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
                'attribute' => 'final_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->final_sn, Url::to(['order-final/view', 'id' => $model->id]));
                    } else {
                        return '';
                    }
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
                'attribute' => 'order_type',
                'label'     => '订单类型',
                'contentOptions'=>['style'=>'min-width: 100px;'],
                'format'    => 'raw',
                'filter'    => Order::$orderType,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Order::$orderType[$model->order->order_type];
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
                'attribute' => 'is_quote',
                'format'    => 'raw',
                'filter'    => OrderFinal::$quote,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderFinal::$quote[$model->is_quote];
                }
            ],
            [
                'attribute' => 'is_purchase',
                'format'    => 'raw',
                'filter'    => OrderFinal::$purchase,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderFinal::$purchase[$model->is_purchase];
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
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderFinalSearch[updated_at]',
                    'value' => Yii::$app->request->get('OrderFinalSearch')['updated_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderFinalSearch[created_at]',
                    'value' => Yii::$app->request->get('OrderFinalSearch')['created_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'value'          => function ($model, $key, $index, $column){
                    if ($model->order->order_type == Order::ORDER_TYPE_PROJECT_YES) {
                        if (!$model->is_agreement) {
                            return Html::a('<i class="fa fa-plus"></i> 生成报价单', Url::to(['detail', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-primary btn-xs btn-flat',
                            ]);
                        } else {
                            return '';
                        }
                    } else {
                        return Html::a('<i class="fa fa-plus"></i> 生成采购单', Url::to(['create-purchase', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-success btn-xs btn-flat',
                        ]);
                    }
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
