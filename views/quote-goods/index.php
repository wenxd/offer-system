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
            'id',
            [
                'attribute' => 'order_sn',
                'label'     => '订单号',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_sn', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'order_final_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_final_sn, Url::to(['order-final/view', 'id' => $model->order_final_id]));
                }
            ],
            [
                'attribute' => 'order_quote_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_quote_sn, Url::to(['order-quote/view', 'id' => $model->order_quote_id]));
                }
            ],
            [
                'attribute'      => 'goods_id',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 60px;'],
                'value'          => function ($model, $key, $index, $column) {
                    return Html::a($model->goods_id, Url::to(['goods/view', 'id' => $model->goods_id]));
                }
            ],
            [
                'attribute'      => 'relevance_id',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 60px;'],
                'value'          => function ($model, $key, $index, $column) {
                    return Html::a($model->relevance_id, Url::to(['inquiry/view', 'id' => $model->relevance_id]));
                }
            ],
            'number',
            [
                'attribute' => 'is_quote',
                'format'    => 'raw',
                'filter'    => QuoteGoods::$quote,
                'value'     => function ($model, $key, $index, $column) {
                    return QuoteGoods::$quote[$model->is_quote];
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'QuoteGoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('QuoteGoodsSearch')['created_at'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->created_at, 0, 10);
                }
            ],
            'serial',
            'tax_rate',
            'price',
            'tax_price',
            'all_price',
            'all_tax_price',
            'quote_price',
            'quote_tax_price',
            'quote_all_price',
            'quote_all_tax_price',
            'delivery_time',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
