<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\Supplier;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '供应商列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['询价员', '采购员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
if (in_array($userId, $adminIds)) {
    $control = '{create} {index}';
} else {
    $control = '{create} {delete} {index}';
}
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => $control,
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'        => [
            'firstPageLabel' => '首页',
            'prevPageLabel'  => '上一页',
            'nextPageLabel'  => '下一页',
            'lastPageLabel'  => '尾页',
        ],
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            'id',
            'name',
            'short_name',
            'full_name',
            'contacts',
            'mobile',
            'telephone',
            'email',
            [
                'attribute' => 'grade',
                'format'    => 'raw',
                'contentOptions'=>['style'=>'min-width: 80px;'],
                'filter'    => Supplier::$grade,
                'value'     => function ($model, $key, $index, $column) {
                    return $model->grade ? Supplier::$grade[$model->grade] : '';
                }
            ],
            'grade_reason',
            'advantage_product',
            [
                'attribute' => 'admin_id',
                'label'     => '申请人',
                'filter'    => \app\models\Helper::getAdminList(['系统管理员', '询价员']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'is_confirm',
                'format'    => 'raw',
                'filter'    => Supplier::$confirm,
                'value'     => function ($model, $key, $index, $column) {
                    return Supplier::$confirm[$model->is_confirm];
                }
            ],
            [
                'attribute' => 'created_at',
                'format'    => 'raw',
                'label'     => '申请时间',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'SupplierSearch[created_at]',
                    'value' => Yii::$app->request->get('SupplierSearch')['created_at'],
                ]),
                'value'     => function($model) {
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute' => 'agree_at',
                'format'    => 'raw',
                'label'     => '入库时间',
                'filter'    => DateRangePicker::widget([
                    'name'  => 'SupplierSearch[agree_at]',
                    'value' => Yii::$app->request->get('SupplierSearch')['agree_at'],
                ]),
                'value'     => function($model) {
                    return substr($model->agree_at, 0, 10);
                }
            ],
            [
                'class'         => ActionColumn::className(),
                'visible'       => !in_array($userId, $adminIds),
                'contentOptions'=>['style'=>'min-width: 200px;'],
                'header'        => '操作',
                'template'      => '{confirm} {view} {update}',
                'buttons' => [
                    'confirm' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-reload"></i> 确认', Url::to(['confirm', 'id' => $model->id]), [
                            'data-pjax' => '0',
                            'class'     => 'btn btn-success btn-flat btn-xs',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
