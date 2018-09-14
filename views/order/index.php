<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Order;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单管理列表';
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
                    'attribute' => 'customer_name',
                    'filter'    => Html::activeTextInput($searchModel, 'customer_name',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->customer) {
                            return $model->customer->name;
                        }
                    }
                ],
                'order_sn',
                'description',
                [
                    'attribute' => 'provide_date',
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[provide_date]',
                        'value' => Yii::$app->request->get('OrderSearch')['provide_date'],
                    ])
                ],
                [
                    'attribute' => 'updated_at',
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[updated_at]',
                        'value' => Yii::$app->request->get('OrderSearch')['updated_at'],
                    ])
                ],
                [
                    'attribute' => 'created_at',
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[created_at]',
                        'value' => Yii::$app->request->get('OrderSearch')['created_at'],
                    ])
                ],
                'order_price',
                'remark',
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'filter'    => Order::$status,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$status[$model->status];
                    }
                ],
                [
                    'attribute' => 'type',
                    'format'    => 'raw',
                    'filter'    => Order::$type,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$type[$model->type];
                    }
                ],
                [
                    'attribute' => '操作',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column){
                        return Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'order_sn' => $model['order_sn']]), [
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
