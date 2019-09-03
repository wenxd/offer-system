<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\QuoteGoods;

/* @var $this yii\web\View */
/* @var $searchModel app\models\QuoteGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报价记录列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'      => 'goods_number',
                'label'          => '零件号',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 60px;'],
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods_id]));
                    } else {
                        return '';
                    }
                }
            ],
            'quote_tax_price',
            'number',
            'tax_rate',
            'quote_all_tax_price',
            'delivery_time',
            [
                'attribute'                       => 'customer_id',
                'label'                           => '客户名称',
                'format'                          => 'raw',
                'filter'                          => \app\models\Customer::getAllDropDown(),
//                'vAlign'                          => 'middle',
//                'width'                           => '120px',
//                'filterType'                      => GridView::FILTER_POS_BODY,
//                'filterInputOptions'              => ['placeholder' => '全部'],
//                'filterWidgetOptions'             => [
//                    'pluginOptions'               => [
//                        'allowClear'              => true,
//                        'minimumResultsForSearch' => -1
//                    ],
//                ],
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        if($model->order->customer) {
                            return $model->order->customer->name;
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'created_at',
                'label'     => '报价时间',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'QuoteGoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('QuoteGoodsSearch')['created_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute' => 'order_quote_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_quote_sn, Url::to(['order-quote/view', 'id' => $model->order_quote_id]));
                }
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
