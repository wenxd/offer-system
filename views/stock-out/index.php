<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Admin;
use app\models\OrderAgreement;
use app\models\AuthAssignment;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAgreementStockOutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '项目出库管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

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
                'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds){
                    if (in_array($userId, $adminIds)) {
                        return $model->agreement_sn;
                    } else {
                        return Html::a($model->agreement_sn, Url::to(['order-agreement/view', 'id' => $model->id]));
                    }
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
//            [
//                'attribute' => 'order_quote_sn',
//                'format'    => 'raw',
//                'value'     => function ($model, $key, $index, $column) {
//                    return Html::a($model->order_quote_sn, Url::to(['order-quote/detail', 'id' => $model->order_quote_id]));
//                }
//            ],
            //'goods_info',
            //'agreement_date',
            //'is_quote',
            [
                'attribute' => 'agreement_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementStockOutSearch[agreement_date]',
                    'value' => Yii::$app->request->get('OrderAgreementStockOutSearch')['agreement_date'],
                ]),
                'value'     => function($model){
                    return substr($model->agreement_date, 0, 10);
                }
            ],
            [
                'attribute'     => 'stock_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'label'         => '收入合同实际交货日期',
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderAgreementStockOutSearch[stock_at]',
                    'value' => Yii::$app->request->get('OrderAgreementStockOutSearch')['stock_at'],
                ]),
                'value'     => function($model){
                    return substr($model->stock_at, 0, 10);
                }
            ],
            [
                'attribute'      => 'is_stock',
                'label'          => '是否出库',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'         => OrderAgreement::$stock,
                'value'          => function ($model, $key, $index, $column) {
                    return OrderAgreement::$stock[$model->is_stock];
                }
            ],
            [
                'attribute'      => 'is_enough',
                'label'          => '是否到齐',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'         => ['1' => '是', '2' => '否'],
                'value'          => function ($model, $key, $index, $column) {
                    $res = OrderAgreement::isEnoughStock($model->id);
                    return $res ? '是' : '否';
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
