<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\PurchaseGoods;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchaseGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '采购记录列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'order_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'order_purchase_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                }
            ],
            'goods_id',
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
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'PurchaseGoodsSearch[updated_at]',
                    'value' => Yii::$app->request->get('PurchaseGoodsSearch')['updated_at'] ?? '',
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'PurchaseGoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('PurchaseGoodsSearch')['created_at'] ?? '',
                ])
            ],
            [
                'attribute' => 'is_purchase',
                'filter'    => PurchaseGoods::$purchase,
                'value'     => function ($model, $key, $index, $column) {
                    return PurchaseGoods::$purchase[$model->is_purchase];
                }
            ],
            'number',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
