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

$this->title = '采购单列表';
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
                'attribute' => 'purchase_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'purchase_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->id]));
                }
            ],
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
                'attribute' => 'order_agreement_sn',
                'visible'   => !in_array($userId, $adminIds),
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_agreement_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->orderAgreement) {
                        return Html::a($model->orderAgreement->agreement_sn, Url::to(['order-agreement/view', 'id' => $model->order_agreement_id]));
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
                'attribute' => 'is_agreement',
                'label'     => '是否全部生成了支出合同',
                'filter'    => OrderPurchase::$agreement,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPurchase::$agreement[$model->is_agreement];
                }
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
