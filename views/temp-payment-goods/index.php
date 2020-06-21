<?php

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Helper;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '采购记录列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
?>
<div class="box">
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'inquiry_admin_id',
                'label'     => '采购员',
                'filter'    => Helper::getAdminList(['系统管理员', '采购员']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    }
                }
            ],
            'id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'label'     => '零件号',
                'visible'   => !in_array($userId, $adminIds),
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number . ' ' . $model->goods->material_code, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'goods_number_b',
                'format'    => 'raw',
                'label'     => '厂家号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number_b', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number_b, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format'    => 'raw',
                'label'     => '中文描述',
                'filter'    => Html::activeTextInput($searchModel, 'description',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description_en',
                'format'    => 'raw',
                'label'     => '英文描述',
                'filter'    => Html::activeTextInput($searchModel, 'description_en',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description_en;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company',
                'format'    => 'raw',
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
                'attribute' => 'supplier_id',
                'filter'     => \app\models\Supplier::getAllDropDown(),
                'filterType' => GridView::FILTER_SELECT2,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->supplier) {
                        return $model->supplier->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'delivery_time',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->delivery_time;
                }
            ],
            [
                'attribute' => 'tax_rate',
                'format'    => 'raw',
                'label'     => '税率',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->tax_rate;
                }
            ],
            [
                'attribute' => 'fixed_price',
                'format'    => 'raw',
                'label'     => '采购未税单价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_price;
                }
            ],
            [
                'attribute' => 'fixed_tax_price',
                'format'    => 'raw',
                'label'     => '采购含税单价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_tax_price;
                }
            ],
            [
                'attribute' => 'fixed_number',
                'format'    => 'raw',
                'label'     => '采购数量',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_number;
                }
            ],
            [
                'attribute' => 'unit',
                'format'    => 'raw',
                'label'     => '单位',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->goods->unit;
                }
            ],
            [
                'attribute' => 'fixed_all_price',
                'format'    => 'raw',
                'label'     => '采购未税总价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_price * $model->fixed_number;
                }
            ],
            [
                'attribute' => 'fixed_all_tax_price',
                'format'    => 'raw',
                'label'     => '采购含税总价',
                'value'     => function ($model, $key, $index, $column) {
                    return $model->fixed_tax_price * $model->fixed_number;
                }
            ],
            [
                'attribute' => 'order_sn',
                'format'    => 'raw',
                'label'     => '订单号',
                'visible'   => !in_array($userId, $adminIds),
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
                'visible'   => !in_array($userId, $adminIds),
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                }
            ],
            [
                'attribute' => 'order_payment_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_payment_sn, Url::to(['order-payment/detail', 'id' => $model->order_payment_id]));
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
