<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use app\models\Inquiry;
use app\models\Goods;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价记录列表';
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
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company',
                'label'     => '原厂家',
                'filter'    => Html::activeTextInput($searchModel, 'original_company',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->original_company;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company_remark',
                'label'     => '原厂家备注',
                'filter'    => Html::activeTextInput($searchModel, 'original_company_remark',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->original_company_remark;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'unit',
                'label'     => '单位',
                'contentOptions'=>['style'=>'min-width: 70px;'],
                'filter'    => Html::activeTextInput($searchModel, 'unit',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->unit;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'technique_remark',
                'label'     => '技术备注',
                'filter'    => Html::activeTextInput($searchModel, 'technique_remark',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->technique_remark;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'is_process',
                'label'     => '加工',
                'filter'    => Goods::$process,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Goods::$process[$model->goods->is_process];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'img_id',
                'label'     => '图纸',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return HTML::img($model->goods->img_url, ['width' => '100px']);
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
            'price',
            'tax_price',
            'tax_rate',
            [
                'attribute' => 'delivery_time',
                'contentOptions'=>['style'=>'min-width: 80px;']
            ],
            [
                'attribute' => 'inquiry_datetime',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[inquiry_datetime]',
                    'value' => Yii::$app->request->get('InquirySearch')['inquiry_datetime'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->inquiry_datetime, 0, 10);
                }
            ],
            'remark',
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[updated_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['updated_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[created_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['created_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
                }
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
                'contentOptions'=>['style'=>'min-width: 200px;'],
                'header' => '操作',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
