<?php

use app\extend\widgets\Bar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use app\models\Order;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\StockLog;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StockInLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '入库记录';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '库管员B']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$super_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员']])->all();
$super_adminIds  = ArrayHelper::getColumn($super_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{index}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'order_sn',
                'label'     => '订单号',
                'format'    => 'raw',
                'visible'   => !in_array($userId, $adminIds),
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
                'attribute' => 'payment_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'payment_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if (in_array($userId, $adminIds)) {
                        return $model->payment_sn;
                    } else {
                        return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->order_payment_id]));
                    }
                }
            ],
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'label'     => '零件号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if ($model->goods) {
                        if (in_array($userId, $adminIds)) {
                            return $model->goods->goods_number;
                        } else {
                            return Html::a($model->goods->goods_number . ' ' . $model->goods->material_code, Url::to(['goods/view', 'id' => $model->goods->id]));
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'number',
                'label'     => '入库数量',
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '库管员',
                'filter'    => \app\models\Helper::getAdminList(['系统管理员', '库管员', '库管员B']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    }
                }
            ],
            [
                'attribute' => 'operate_time',
                'format'    => 'raw',
                'label'     => '入库时间',
                'filter'    => DateRangePicker::widget([
                    'name' => 'StockInLogSearch[operate_time]',
                    'value' => Yii::$app->request->get('StockInLogSearch')['operate_time'],
                ])
            ],
            'source',
            [
                'attribute' => 'position',
                'label'     => '入库位置',
            ],
            [
                'attribute' => 'is_manual',
                'filter'    => StockLog::$manual,
                'value'     => function ($model, $key, $index, $column) {
                    return $model->is_manual ? StockLog::$manual[$model->is_manual] : '否';
                }
            ],
            [
                'attribute' => 'order_type',
                'label'     => '订单类型',
                'filter'    => Order::$orderType,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Order::$orderType[$model->order->order_type];
                    } else {
                        return '';
                    }
                }
            ],
            'remark',
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'visible'        => in_array($userId, $super_adminIds),
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'value'          => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    return Html::a('<i class="fa fa-eidt"></i> 修改', Url::to(['update', 'id' => $model->id]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
