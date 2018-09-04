<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\models\Customer;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报价单列表';
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
                'attribute' => 'customer_name',
                'filter'    => Html::activeTextInput($searchModel, 'customer_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->customer) {
                        return $model->customer->name;
                    }
                }
            ],
            'order_id',
            'description',
            [
                'attribute' => 'provide_date',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderQuoteSearch[provide_date]',
                    'value' => Yii::$app->request->get('OrderQuoteSearch')['provide_date'],
                ])
            ],
            [
                'attribute' => 'updated_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderQuoteSearch[updated_at]',
                    'value' => Yii::$app->request->get('OrderQuoteSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderQuoteSearch[created_at]',
                    'value' => Yii::$app->request->get('OrderQuoteSearch')['created_at'],
                ])
            ],
            'quote_price',
            'remark',
            [
                'attribute' => '操作',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column){
                    return Html::a('<i class="fa fa-eye"></i> 查看',Url::to(['detail', 'id'=> $model['id']]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-xs btn-flat',
                    ]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
