<?php

use app\models\OrderPayment;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use app\models\Admin;
use app\models\OrderPurchase;
use app\models\OrderAgreement;
use app\models\AuthAssignment;
use app\models\OrderFinancialCollectSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderFinancialCollectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待收款订单';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '收款财务'])->all();
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
                    'label'     => '完成出库',
                    'format'    => 'raw',
                    'filter'    => OrderAgreement::$stock,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderAgreement::$stock[$model->is_stock];
                    }
                ],
                [
                    'attribute' => 'is_advancecharge',
                    'label'     => '预收款完成',
                    'format'    => 'raw',
                    'filter'    => OrderAgreement::$advanceCharge,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderAgreement::$advanceCharge[$model->is_advancecharge];
                    }
                ],
                [
                    'attribute' => 'is_payment',
                    'label'     => '全单收款完成',
                    'format'    => 'raw',
                    'filter'    => OrderAgreement::$payment,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderAgreement::$payment[$model->is_payment];
                    }
                ],
                [
                    'attribute' => 'is_bill',
                    'label'     => '开发票',
                    'format'    => 'raw',
                    'filter'    => OrderAgreement::$bill,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderAgreement::$bill[$model->is_bill];
                    }
                ],
                [
                    'attribute' => 'is_complete',
                    'label'     => '全流程',
                    'format'    => 'raw',
                    'filter'    => OrderAgreement::$complete,
                    'value'     => function ($model, $key, $index, $column) {
                        return OrderAgreement::$complete[$model->is_complete];
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
                    'attribute' => 'agreement_sn',
                    'label'     => '收款合同单号',
                    'format'    => 'raw',
                    'filter'    => Html::activeTextInput($searchModel, 'agreement_sn',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a($model->agreement_sn, Url::to(['order-agreement/view', 'id' => $model->id]));
                    }
                ],
                [
                    'attribute'      => '操作',
                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $column){
                        return Html::a('<i class="fa fa-eye"></i> 收款', Url::to(['detail', 'id' => $model['id']]), [
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
