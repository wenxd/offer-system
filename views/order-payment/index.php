<?php

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

$this->title = '支出合同管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['财务']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['采购员']])->all();
$purchaseAdminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box">
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
                'attribute' => 'is_stock',
                'label'     => '完成入库',
                'format'    => 'raw',
                'filter'    => OrderPayment::$stock,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$stock[$model->is_stock];
                }
            ],
            [
                'attribute' => 'is_advancecharge',
                'label'     => '预付款完成',
                'format'    => 'raw',
                'filter'    => OrderPayment::$advanceCharge,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$advanceCharge[$model->is_advancecharge];
                }
            ],
            [
                'attribute' => 'is_payment',
                'label'     => '全单付款完成',
                'format'    => 'raw',
                'filter'    => OrderPayment::$payment,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$payment[$model->is_payment];
                }
            ],
            [
                'attribute' => 'is_bill',
                'label'     => '收到发票',
                'format'    => 'raw',
                'filter'    => OrderPayment::$bill,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$bill[$model->is_bill];
                }
            ],
            [
                'attribute' => 'is_complete',
                'label'     => '全流程',
                'format'    => 'raw',
                'filter'    => OrderPayment::$complete,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$complete[$model->is_complete];
                }
            ],
            'payment_price',
            [
                'attribute' => 'order_sn',
                'label'     => '订单编号',
                'format'    => 'raw',
                'visible'   => !in_array($userId, array_merge($adminIds, $purchaseAdminIds)),
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
                'visible'   => !in_array($userId, $adminIds),
                'value'     => function ($model, $key, $index, $column) use ($userId, $purchaseAdminIds) {
                    if (in_array($userId, $purchaseAdminIds) && $model->is_complete) {
                        return $model->order_purchase_sn;
                    } else {
                        return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                    }
                }
            ],
            [
                'attribute' => 'order_agreement_date',
                'format'    => 'raw',
                'label'     => '收入合同交货日期',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->purchase && $model->purchase->agreement) {
                        return substr($model->purchase->agreement->agreement_date, 0, 10);
                    } else {
                        return false;
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
                'attribute'     => 'delivery_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'        => DateRangePicker::widget([
                    'name'  => 'OrderPaymentSearch[delivery_date]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['delivery_date'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->delivery_date, 0, 10);
                }
            ],
            [
                'attribute'     => 'stock_at',
                'label'         => '合同实际交货日期',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderPaymentSearch[stock_at]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['stock_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->stock_at, 0, 10);
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
                'filter'    => Helper::getAdminList(['系统管理员', '采购员', '询价员']),
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '采购员', '询价员'])[$model->admin_id])) {
                        return Helper::getAdminList(['系统管理员', '采购员', '询价员'])[$model->admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'stock_admin_id',
                'label'     => '库管员',
                'filter'    => Helper::getAdminList(['系统管理员', '库管员']),
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '库管员'])[$model->stock_admin_id])) {
                        return Helper::getAdminList(['系统管理员', '库管员'])[$model->stock_admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'financial_admin_id',
                'label'     => '财务',
                'filter'    => Helper::getAdminList(['系统管理员', '财务']),
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '财务'])[$model->financial_admin_id])) {
                        return Helper::getAdminList(['系统管理员', '财务'])[$model->financial_admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'visible'        => !in_array($userId, $adminIds),
                'value'          => function ($model, $key, $index, $column){
                    $html = '';
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
