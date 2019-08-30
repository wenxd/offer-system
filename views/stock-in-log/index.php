<?php

use app\models\Admin;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockInLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '入库记录';
$this->params['breadcrumbs'][] = $this->title;

$adminList = Admin::find()->all();
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
            [
                'attribute' => 'payment_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'payment_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return $model->payment_sn;
                }
            ],
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
                'label'     => '入库时间',
                'filter'    => DateRangePicker::widget([
                    'name' => 'StockInLogSearch[operate_time]',
                    'value' => Yii::$app->request->get('StockInLogSearch')['operate_time'],
                ])
            ],
            'remark',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
