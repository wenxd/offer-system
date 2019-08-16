<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;
use app\models\OrderPurchase;
use kartik\daterange\DateRangePicker;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\OrderFinancialCollectSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderFinancialCollectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待收款订单';
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
                    'attribute' => 'is_stock',
                    'label'     => '完成出库',
                    'format'    => 'raw',
                    'filter'    => OrderPurchase::$stock,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderPurchase::$stock[$model->is_stock];
                    }
                ],
                [
                    'attribute' => 'is_advancecharge',
                    'label'     => '预收款完成',
                    'format'    => 'raw',
                    'filter'    => OrderPurchase::$advanceCharge,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderPurchase::$advanceCharge[$model->is_advancecharge];
                    }
                ],
                [
                    'attribute' => 'is_payment',
                    'label'     => '全单收款完成',
                    'format'    => 'raw',
                    'filter'    => OrderPurchase::$payment,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderPurchase::$payment[$model->is_payment];
                    }
                ],
                [
                    'attribute' => 'is_bill',
                    'label'     => '开发票',
                    'format'    => 'raw',
                    'filter'    => OrderPurchase::$bill,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderPurchase::$bill[$model->is_bill];
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
                    'label'     => '采购单号',
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
