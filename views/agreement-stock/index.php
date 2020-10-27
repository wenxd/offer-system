<?php

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Helper;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\AgreementStock;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AgreementStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '使用库存列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '库管员B']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$use_admin = AuthAssignment::find()->where(['item_name' => '收款财务'])->all();
$finance_adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box table-responsive">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'contentOptions' => ['style'=>'min-width: 100px;'],
                'label'     => '零件号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($userId, $adminIds){
                    if ($model->goods) {
                        if (in_array($userId, $adminIds)) {
                            return $model->goods->goods_number . ' ' . $model->goods->material_code;
                        } else {
                            return Html::a($model->goods->goods_number . ' ' . $model->goods->material_code, Url::to(['goods/view', 'id' => $model->goods->id]));
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format'    => 'raw',
                'label'     => '中文名称',
                'filter'    => Html::activeTextInput($searchModel, 'description', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => '库存位置',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->stock) {
                        return $model->stock->position;
                    } else {
                        return '';
                    }
                }
            ],
            'use_number',
            [
                'attribute' => '临时库存数量',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->is_confirm) {
                        return $model->temp_number;
                    }
                    if ($model->stock) {
                        return $model->stock->temp_number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => '库存数量',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->is_confirm) {
                        return $model->stock_number;
                    }
                    if ($model->stock) {
                        return $model->stock->number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'tax_price',
                'visible'   => !in_array($userId, $adminIds),
            ],
            [
                'attribute' => 'all_tax_price',
                'visible'   => !in_array($userId, $adminIds),
            ],
            [
                'attribute' => 'order_sn',
                'visible'   => !in_array($userId, array_merge($adminIds, $finance_adminIds)),
                'label'     => '订单编号',
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
                'attribute' => 'order_agreement_sn',
                'visible'   => !in_array($userId, $adminIds),
            ],
            'order_purchase_sn',
            'order_payment_sn',
            [
                'attribute' => 'is_stock',
                'format'    => 'raw',
                'filter'    => AgreementStock::$confirm,
                'value'     => function ($model, $key, $index, $column) {
                    return AgreementStock::$confirm[$model->is_stock];
                }
            ],
            [
                'attribute' => 'is_confirm',
                'format'    => 'raw',
                'filter'    => AgreementStock::$confirm,
                'value'     => function ($model, $key, $index, $column) {
                    return AgreementStock::$confirm[$model->is_confirm];
                }
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '库管员',
                'filter'    => in_array($userId, $adminIds) ? Helper::getAdminList(['库管员', '库管员B']) : Helper::getAdminList(['系统管理员', '库管员', '库管员B']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'source',
                'filter'    => AgreementStock::$source,
                'value'     => function ($model, $key, $index, $column) {
                    if (isset(AgreementStock::$source[$model->source])) {
                        return AgreementStock::$source[$model->source];
                    }
                    return '';
                }
            ],
            [
                'attribute'      => 'created_at',
                'label'          => '创建时间',
                'contentOptions' =>['style'=>'min-width: 150px;'],
                'filter'         => DateRangePicker::widget([
                    'name'       => 'AgreementStockSearch[created_at]',
                    'value'      => Yii::$app->request->get('AgreementStockSearch')['created_at'] ?? '',
                ]),
                'value' => function($model){
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute'      => 'confirm_at',
                'label'          => '确认时间',
                'contentOptions' =>['style'=>'min-width: 150px;'],
                'filter'         => DateRangePicker::widget([
                    'name'       => 'AgreementStockSearch[confirm_at]',
                    'value'      => Yii::$app->request->get('AgreementStockSearch')['confirm_at'] ?? '',
                ]),
                'value' => function($model){
                    return substr($model->confirm_at, 0, 10);
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'visible'        => !in_array($userId, $finance_adminIds),
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'value'          => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    $html = '';
                    if (!$model->is_confirm) {
                        if ($model->stock && $model->stock->temp_number >= $model->use_number) {
                            $html .= Html::a('<i class="fa fa-heart"></i> 确认', Url::to(['confirm', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-xs btn-flat',
                            ]);
                        }
                        if (in_array($model->source, ['strategy', 'purchase', 'payment'])) {
                            $html .= Html::a('<i class="fa fa-times"></i> 驳回', Url::to(['reject', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-danger btn-xs btn-flat',
                            ]);
                        }

                    }
                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
