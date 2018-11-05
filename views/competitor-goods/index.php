<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Customer;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CompetitorGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '竞争对手价格记录';
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
            'goods_id',
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
            'competitor_id',
            [
                'attribute' => 'competitor_name',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'competitor_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->competitor) {
                        return $model->competitor->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'customer',
                'contentOptions'=>['style'=>'min-width: 100px;'],
                'label'     => '针对客户',
                'format'    => 'raw',
                'filter'    => Customer::getSelectDropDown(),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->customer && $model->customers) {
                        return $model->customers->name;
                    } else {
                        return '';
                    }
                }
            ],
            'tax_rate',
            'price',
            'tax_price',
            'number',
            'unit',
            [
                'attribute' => 'offer_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'CompetitorGoodsSearch[offer_date]',
                    'value' => Yii::$app->request->get('CompetitorGoodsSearch')['offer_date'] ?? '',
                ])
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'CompetitorGoodsSearch[updated_at]',
                    'value' => Yii::$app->request->get('CompetitorGoodsSearch')['updated_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'CompetitorGoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('CompetitorGoodsSearch')['created_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
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
