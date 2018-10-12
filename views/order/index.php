<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Order;
use app\extend\widgets\Bar;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单管理列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget()?>
    </div>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager'        => [
                'firstPageLabel' => '首页',
                'prevPageLabel'  => '上一页',
                'nextPageLabel'  => '下一页',
                'lastPageLabel'  => '尾页',
            ],
            'columns' => [
                'id',
                [
                    'attribute' => 'customer_name',
                    'filter'    => Html::activeTextInput($searchModel, 'customer_name',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->customer) {
                            return $model->customer->name;
                        }
                    }
                ],
                'order_sn',
                [
                    'attribute' => 'provide_date',
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[provide_date]',
                        'value' => Yii::$app->request->get('OrderSearch')['provide_date'],
                    ])
                ],
                [
                    'attribute' => 'updated_at',
                    'contentOptions' =>['style'=>'min-width: 150px;'],
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[updated_at]',
                        'value' => Yii::$app->request->get('OrderSearch')['updated_at'],
                    ])
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' =>['style'=>'min-width: 150px;'],
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[created_at]',
                        'value' => Yii::$app->request->get('OrderSearch')['created_at'],
                    ])
                ],
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'filter'    => Order::$status,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$status[$model->status];
                    }
                ],
                [
                    'attribute' => 'order_type',
                    'format'    => 'raw',
                    'filter'    => Order::$orderType,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$orderType[$model->order_type];
                    }
                ],
                [
                    'attribute'      => '操作',
                    'format'         => 'raw',
                    'contentOptions' =>['style'=>'min-width: 260px;'],
                    'value'          => function ($model, $key, $index, $column){
                        return Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'order_sn' => $model['order_sn']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-xs btn-flat',
                        ]) . Html::a('<i class="fa fa-paper-plane-o"></i> 生成询价单', Url::to(['create-inquiry', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-primary btn-xs btn-flat',
                        ]) . Html::a('<i class="fa fa-heart"></i> 生成最终订单', Url::to(['create-final', 'id' => $model['id'], 'key' => date('YmdHis') . rand(10, 99)]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-xs btn-flat',
                        ]);
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
