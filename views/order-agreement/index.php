<?php

use app\extend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\OrderPurchase;
use app\models\OrderPayment;
use app\models\OrderAgreementSearch;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收入合同订单管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['采购员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务']])->all();
$final_adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

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
                'visible'   => !in_array($userId, $adminIds),
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
                'attribute' => 'agreement_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->agreement_sn, Url::to(['view', 'id' => $model->id]));
                }
            ],
            [
                'attribute' => 'is_stock',
                'label'     => '完成出库',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$stock,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$stock[$model->is_stock];
                }
            ],
            [
                'attribute' => 'is_advancecharge',
                'label'     => '预收款完成',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$advanceCharge,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$advanceCharge[$model->is_advancecharge];
                }
            ],
            [
                'attribute' => 'is_payment',
                'label'     => '全单收款完成',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$payment,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$payment[$model->is_payment];
                }
            ],
            [
                'attribute' => 'is_bill',
                'label'     => '开发票',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$bill,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$bill[$model->is_bill];
                }
            ],
            [
                'attribute' => 'is_complete',
                'label'     => '全流程',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$complete,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$complete[$model->is_complete];
                }
            ],
            'payment_price',
            [
                'attribute' => 'is_purchase',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$purchase,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$purchase[$model->is_purchase];
                }
            ],
            [
                'attribute' => 'is_any_stock',
                'format'    => 'raw',
                'label'     => '是否走库存',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->agreementStock) {
                        return '是';
                    } else {
                        return '否';
                    }
                }
            ],
            [
                'attribute' => 'is_all_stock',
                'format'    => 'raw',
                'filter'    => OrderAgreementSearch::$allStock,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderAgreementSearch::$allStock[$model->is_all_stock];
                }
            ],
//            [
//                'attribute' => 'order_quote_sn',
//                'format'    => 'raw',
//                'value'     => function ($model, $key, $index, $column) {
//                    return Html::a($model->order_quote_sn, Url::to(['order-quote/view', 'id' => $model->order_quote_id]));
//                }
//            ],
            [
                'attribute' => 'sign_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementSearch[sign_date]',
                    'value' => Yii::$app->request->get('OrderAgreementSearch')['sign_date'],
                ]),
                'value'     => function($model){
                    return substr($model->sign_date, 0, 10);
                }
            ],
            [
                'attribute' => 'agreement_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementSearch[agreement_date]',
                    'value' => Yii::$app->request->get('OrderAgreementSearch')['agreement_date'],
                ]),
                'value'     => function($model){
                    return substr($model->agreement_date, 0, 10);
                }
            ],
            [
                'attribute' => 'stock_at',
                'label'     => '合同实际交货日期',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementSearch[stock_at]',
                    'value' => Yii::$app->request->get('OrderAgreementSearch')['stock_at'],
                ]),
                'value'     => function($model){
                    return substr($model->stock_at, 0, 10);
                }
            ],
            [
                'attribute' => 'expect_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementSearch[expect_at]',
                    'value' => Yii::$app->request->get('OrderAgreementSearch')['expect_at'],
                ]),
                'value'     => function($model){
                    return substr($model->expect_at, 0, 10);
                }
            ],
            [
                'attribute' => 'payment_max_date',
                'label'     => '支出合同最晚时间',
                'value'     => function ($model, $key, $index, $column) {
                    $orderPurchaseList = OrderPurchase::find()->where(['order_agreement_id' => $model->id])->all();
                    $orderPurchaseIds = ArrayHelper::getColumn($orderPurchaseList, 'id');
                    $orderPayment = OrderPayment::find()->where(['order_purchase_id' => $orderPurchaseIds])->orderBy('delivery_date Desc')->one();
                    if (!empty($orderPayment)) {
                        return substr($orderPayment->delivery_date, 0, 10);
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'customer_name',
                'label'     => '客户名称',
                'filter'    => Html::activeTextInput($searchModel, 'customer_name',['class' => 'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return $model->order->customer->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'visible'   => !in_array($userId, array_merge($adminIds, $final_adminIds)),
                'value'          => function ($model, $key, $index, $column){
                    if ($model->is_purchase) {
                        return '';
                    } else {
                        if (!$model->is_all_stock) {
                            $html = Html::a('<i class="fa fa-plus"></i> 采购策略', Url::to(['detail', 'id' => $model['id'], 'type' => 'strategy']), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-xs btn-flat',
                            ]);
                            return $html . Html::a('<i class="fa fa-plus"></i> 采购单', Url::to(['detail', 'id' => $model['id'], 'type' => 'order']), [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-primary btn-xs btn-flat',
                                ]);
                        } else {
                            return '';
                        }
                        if ($model->is_merge) {
                            if (!$model->is_all_stock) {
                                $html = Html::a('<i class="fa fa-plus"></i> 采购策略', Url::to(['detail', 'id' => $model['id'], 'type' => 'strategy']), [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-success btn-xs btn-flat',
                                ]);
                                return $html . Html::a('<i class="fa fa-plus"></i> 采购单', Url::to(['detail', 'id' => $model['id'], 'type' => 'order']), [
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-primary btn-xs btn-flat',
                                    ]);
                            } else {
                                return '';
                            }
                        } else {
                            return Html::a('<i class="fa fa-d"></i> 合并采购数据', Url::to(['merge', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-info btn-xs btn-flat',
                            ]);
                        }
                    }
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
