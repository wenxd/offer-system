<?php

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
            'goods_number',
            'description',
            'original_company',
            'original_company_remark',
            'unit',

            [
                'attribute' => 'is_process',
                'filter'    => Goods::$process,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'offer_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[offer_date]',
                    'value' => Yii::$app->request->get('GoodsSearch')['offer_date'],
                ])
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
            'competitor',
            'competitor_offer',
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
