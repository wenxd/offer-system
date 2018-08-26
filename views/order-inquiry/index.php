<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderInquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价单列表';
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
            'order_id',
            'description',
            [
                'attribute' => 'provide_date',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderInquirySearch[provide_date]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['provide_date'],
                ])
            ],
            [
                'attribute' => 'updated_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderInquirySearch[updated_at]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderInquirySearch[created_at]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['created_at'],
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
