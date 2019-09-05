<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Admin;
use app\models\AuthAssignment;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收入合同订单管理';
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
                'attribute' => 'agreement_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->agreement_sn, Url::to(['view', 'id' => $model->id]));
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
                'attribute' => 'order_quote_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_quote_sn, Url::to(['order-quote/detail', 'id' => $model->order_quote_id]));
                }
            ],
            //'goods_info',
            //'agreement_date',
            //'is_quote',
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
                    return Html::a('<i class="fa fa-plus"></i> 派送采购员', Url::to(['detail', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-xs btn-flat',
                    ]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
