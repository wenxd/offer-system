<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use app\models\Inquiry;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价管理列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget()?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            'id',
            'good_id',
            'supplier_id',
            'supplier_name',
            'inquiry_price',
            [
                'attribute' => 'inquiry_datetime',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[inquiry_datetime]',
                    'value' => Yii::$app->request->get('InquirySearch')['inquiry_datetime'],
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
                'class' => ActionColumn::className(),
                'headerOptions' => ['style' => 'width:17%'],
                'header' => '操作',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
