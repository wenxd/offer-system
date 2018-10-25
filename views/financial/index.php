<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\OrderPurchase;
use kartik\daterange\DateRangePicker;
use app\models\Admin;
use app\models\AuthAssignment;


/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderPurchaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待付款订单';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;
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
                'attribute' => 'is_stock',
                'label'     => '完成入库',
                'format'    => 'raw',
                'filter'    => OrderPurchase::$stock,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPurchase::$stock[$model->is_stock];
                }
            ],
            [
                'attribute' => 'is_advancecharge',
                'label'     => '预付款完成',
                'format'    => 'raw',
                'filter'    => OrderPurchase::$advanceCharge,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPurchase::$advanceCharge[$model->is_advancecharge];
                }
            ],
            [
                'attribute' => 'is_payment',
                'label'     => '全单付款完成',
                'format'    => 'raw',
                'filter'    => OrderPurchase::$payment,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPurchase::$payment[$model->is_payment];
                }
            ],
            [
                'attribute' => 'is_bill',
                'label'     => '收到发票',
                'format'    => 'raw',
                'filter'    => OrderPurchase::$bill,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPurchase::$bill[$model->is_bill];
                }
            ],
            'purchase_sn',
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
                'attribute' => 'order_final_sn',
                'visible'   => !in_array($userId, $adminIds),
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_final_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->orderFinal) {
                        return Html::a($model->orderFinal->final_sn, Url::to(['order-final/detail', 'id' => $model->order_final_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'end_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderPurchaseSearch[end_date]',
                    'value' => Yii::$app->request->get('OrderPurchaseSearch')['end_date'],
                ])
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderPurchaseSearch[updated_at]',
                    'value' => Yii::$app->request->get('OrderPurchaseSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderPurchaseSearch[created_at]',
                    'value' => Yii::$app->request->get('OrderPurchaseSearch')['created_at'],
                ])
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '采购员',
                'filter'    => $admins,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    }
                }
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
