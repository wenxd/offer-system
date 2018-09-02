<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Inquiry;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价管理列表';
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
            'good_id',
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
            'supplier_id',
            [
                'attribute' => 'supplier_name',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'supplier_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->supplier) {
                        return $model->supplier->name;
                    } else {
                        return '';
                    }
                }
            ],
            'inquiry_price',
            [
                'attribute' => 'inquiry_datetime',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[inquiry_datetime]',
                    'value' => Yii::$app->request->get('InquirySearch')['inquiry_datetime'],
                ])
            ],
            [
                'attribute' => 'updated_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[updated_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[created_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['created_at'],
                ])
            ],
            [
                'attribute' => 'is_better',
                'filter'    => Inquiry::$better,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$newest[$model->is_better];
                }
            ],
            [
                'attribute' => 'is_newest',
                'filter'    => Inquiry::$newest,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$newest[$model->is_newest];
                }
            ],
            [
                'class' => ActionColumn::className(),
                'headerOptions' => ['style' => 'width:12%'],
                'header' => '操作',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
