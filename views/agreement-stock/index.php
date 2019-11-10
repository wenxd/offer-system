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

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
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
                'label'     => '零件号',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
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
            'use_number',
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
                'visible'   => !in_array($userId, $adminIds),
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
            'order_agreement_sn',
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
                'label'     => '采购员',
                'filter'    => in_array($userId, $adminIds) ? Helper::getAdminList(['库管员']) : Helper::getAdminList(['系统管理员', '库管员']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    } else {
                        return '';
                    }
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
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'value'          => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    $html = '';
                    if (!$model->is_confirm) {
                        $html .= Html::a('<i class="fa fa-heart"></i> 确认', Url::to(['confirm', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-success btn-xs btn-flat',
                        ]);
                    }
                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
