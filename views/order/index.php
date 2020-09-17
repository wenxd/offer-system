<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Order;
use app\models\AuthAssignment;
use app\extend\widgets\Bar;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单管理列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget(['template' => '{create}'])?>
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
                [
                    'attribute' => 'order_sn',
                    'visible'   => !in_array($userId, $adminIds),
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                         return Html::a($model->order_sn, Url::to(['order/detail', 'id' => $model->id]));
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' =>['style'=>'min-width: 150px;'],
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderSearch[created_at]',
                        'value' => Yii::$app->request->get('OrderSearch')['created_at'],
                    ]),
                    'value'     => function($model){
                        return substr($model->created_at, 0, 10);
                    }
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
                    'attribute' => 'is_inquiry',
                    'label'     => '是否有询价记录',
                    'format'    => 'raw',
                    'filter'    => ['0' => '否', '1' => '是'],
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::getInquiry($model->id);
                    }
                ],
                [
                    'attribute' => 'is_dispatch',
                    'format'    => 'raw',
                    'filter'    => Order::$dispatch,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$dispatch[$model->is_dispatch];
                    }
                ],
                [
                    'attribute' => 'is_final',
                    'format'    => 'raw',
                    'filter'    => Order::$final,
                    'value'     => function ($model, $key, $index, $column) {
                        return Order::$final[$model->is_final];
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
                    'contentOptions' =>['style'=>'min-width: 200px;'],
                    'value'          => function ($model, $key, $index, $column){
                        $html = Html::a('<i class="fa fa-paper-plane-o"></i>询价单(顶)', Url::to(['create-inquiry-new', 'id' => $model['id'], 'level' => 1]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-primary btn-xs btn-flat',
                        ]);
                        $html .= Html::a('<i class="fa fa-paper-plane-o"></i>询价单(子)', Url::to(['create-inquiry-new', 'id' => $model['id'], 'level' => 2]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-xs btn-flat',
                        ]);

                        if (!$model->cost) {
                            $html .= Html::a('<i class="fa fa-heart"></i> 生成成本单', Url::to(['create-final', 'id' => $model['id'], 'key' => date('YmdHis') . rand(10, 99)]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-xs btn-flat',
                            ]);
                        }
                        return $html;
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
