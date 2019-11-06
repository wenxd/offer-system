<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
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
            'tax_price',
            'all_tax_price',
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
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
