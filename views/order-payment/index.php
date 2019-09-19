<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '支出合同管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'payment_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'payment_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->id]));
                }
            ],
            'payment_price',
            [
                'attribute' => 'order_sn',
                'label'     => '订单编号',
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
                'attribute' => 'agreement_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderPaymentSearch[agreement_at]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['agreement_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->agreement_at, 0, 10);
                }
            ],
            [
                'attribute' => 'delivery_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderPaymentSearch[delivery_date]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['delivery_date'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->delivery_date, 0, 10);
                }
            ],
            [
                'attribute' => 'stock_at',
                'label'     => '合同实际交货日期',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderPaymentSearch[stock_at]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['stock_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->stock_at, 0, 10);
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'value'          => function ($model, $key, $index, $column){
                    $html = '';

                    $html .= Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-xs btn-flat',
                    ]);

                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
