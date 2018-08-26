<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库存管理列表';
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
        'filterModel'  => $searchModel,
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
            'good_id',
            'supplier_id',
            'supplier_name',
            'price',
            'position',
            'number',
            [
                'attribute' => 'updated_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'StockSearch[updated_at]',
                    'value' => Yii::$app->request->get('StockSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'StockSearch[created_at]',
                    'value' => Yii::$app->request->get('StockSearch')['created_at'],
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
