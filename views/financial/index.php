<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\OrderPayment;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderFinancialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待付款订单';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '财务'])->all();
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
            [
                'attribute' => 'order_sn',
                'label'     => '订单号',
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
                'attribute' => 'order_purchase_sn',
                'label'     => '采购单号',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_purchase_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                }
            ],
            [
                'attribute' => 'payment_sn',
                'label'     => '支出合同单号',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'payment_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->id]));
                }
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
