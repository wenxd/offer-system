<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
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
                'format'         => 'raw',
                'label'          => '零件号',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
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
                'attribute'  => 'customer_id',
                'label'      => '客户名称',
                'filter'     => \app\models\Customer::getAllDropDown(),
                'filterType' => GridView::FILTER_SELECT2,
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
