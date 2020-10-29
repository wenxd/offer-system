<?php

use app\extend\widgets\Bar;
use app\models\Helper;
use app\models\Supplier;
use app\models\OrderPayment;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use app\models\AuthAssignment;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '杂项支出合同管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务', '付款财务', '系统管理员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['采购员']])->all();
$purchaseAdminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{index}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index2']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'payment_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($userId, $purchaseAdminIds) {
                    if (in_array($userId, $purchaseAdminIds) && $model->is_complete) {
                        return $model->payment_sn;
                    } else {
                        return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->id]));
                    }
                }
            ],

            [
                'attribute'     => 'agreement_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'        => DateRangePicker::widget([
                    'name'  => 'OrderPaymentSearch[agreement_at]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['agreement_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->agreement_at, 0, 10);
                }
            ],
            [
                'attribute' => 'is_reim',
                'filter'    => OrderPayment::$complete,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$complete[$model['is_reim']] ?? '否';
                }
            ],

            'payment_price',
            [
                'attribute' => 'order_sn',
                'label'     => '订单编号',
                'format'    => 'raw',
//                'visible'   => !in_array($userId, array_merge($adminIds, $purchaseAdminIds)),
                'visible'   => in_array($userId, $adminIds),
                'filter'    => Html::activeTextInput($searchModel, 'order_sn', ['class'=>'form-control']),
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
                'visible'   => in_array($userId, $adminIds),
                'value'     => function ($model, $key, $index, $column) use ($userId, $purchaseAdminIds) {
                    if (in_array($userId, $purchaseAdminIds) && $model->is_complete) {
                        return $model->order_purchase_sn;
                    } else {
                        return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                    }
                }
            ],

            [
                'attribute'  => 'supplier_id',
                'filter'     => Supplier::getAllDropDown(),
                'filterType' => GridView::FILTER_SELECT2,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->supplier) {
                        return $model->supplier->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '采购员',
                'filter'    => in_array($userId, $purchaseAdminIds) ? [$userId => Yii::$app->user->identity->username] : Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员']),
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员'])[$model->admin_id])) {
                        return Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员'])[$model->admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'financial_admin_id',
                'label'     => '财务',
                'filter'    => Helper::getAdminList(['系统管理员', '订单管理员', '收款财务']),
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '订单管理员', '收款财务'])[$model->financial_admin_id])) {
                        return Helper::getAdminList(['系统管理员', '订单管理员', '收款财务'])[$model->financial_admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
//                'visible'        => !in_array($userId, $adminIds),
                'value'          => function ($model, $key, $index, $column) use($userId, $adminIds) {
                    $html = '';
                    if (in_array($userId, $adminIds) && $model->is_reim == 0) {
                        $html .= Html::a('<i class="fa fa-eye"></i> 报销', Url::to(['reim', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-success btn-xs btn-flat',
                        ]);
                    }
                    $html .= Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-xs btn-flat',
                    ]);
                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
