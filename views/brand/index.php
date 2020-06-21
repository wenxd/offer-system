<?php

use app\extend\grid\ActionColumn;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use app\extend\widgets\Bar;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '品牌管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {delete}',
        ])?>
    </div>
    <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            'id',
            'name',
            'name_all',
            'intro',
            'remark',
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[updated_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['updated_at'],
                ]),
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['created_at'],
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'class'         => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 10px;'],
                'header'        => '操作',
                'template'      => '{update}',
            ],
        ],
    ]); ?>
    </div>
</div>
