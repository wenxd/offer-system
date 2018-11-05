<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
use app\models\Goods;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '零件列表';
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
        'pager'        => [
            'firstPageLabel' => '首页',
            'prevPageLabel'  => '上一页',
            'nextPageLabel'  => '下一页',
            'lastPageLabel'  => '尾页',
        ],
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            'id',
            [
                'attribute' => 'goods_number',
                'format'         => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->goods_number, Url::to(['view', 'id' => $model->id]));
                }
            ],
            'goods_number_b',
            'description',
            'description_en',
            'original_company',
            'original_company_remark',
            [
                'attribute' => 'is_process',
                'filter'    => Goods::$process,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'is_special',
                'filter'    => Goods::$special,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$special[$model->is_special];
                }
            ],
            [
                'attribute' => 'is_nameplate',
                'filter'    => Goods::$nameplate,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'filter'    => Goods::$emerg,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'is_assembly',
                'filter'    => Goods::$assembly,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$assembly[$model->is_assembly];
                }
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[updated_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['created_at'],
                ])
            ],
            'technique_remark',
            [
                'class'         => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 200px;'],
                'header'        => '操作',
                'template'      => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
