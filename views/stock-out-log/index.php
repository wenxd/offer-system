<?php

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Order;
use app\models\StockLog;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockOutLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '出库记录';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '系统管理员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

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
                    'attribute' => 'order_sn',
                    'format'    => 'raw',
                    'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->order) {
                            return $model->order->order_sn;
                        } else {
                            return '';
                        }
                    }
                ],
                'agreement_sn',
                [
                    'attribute' => 'goods_number',
                    'format'    => 'raw',
                    'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->goods) {
                            return $model->goods->goods_number;
                        } else {
                            return '';
                        }
                    }
                ],
                'number',
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
                    'attribute' => 'operate_time',
                    'format'    => 'raw',
                    'label'     => '出库时间',
                    'filter'    => DateRangePicker::widget([
                        'name' => 'StockInLogSearch[operate_time]',
                        'value' => Yii::$app->request->get('StockInLogSearch')['operate_time'],
                    ])
                ],
                [
                    'attribute' => 'is_manual',
                    'filter'    => StockLog::$manual,
                    'value'     => function ($model, $key, $index, $column) {
                        return StockLog::$manual[$model->is_manual];
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
                            return '否';
                        }
                    }
                ],
                'remark',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
