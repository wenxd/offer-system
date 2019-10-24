<?php

use yii\helpers\Html;
use yii\grid\GridView;
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
                'filter'    => Supplier::$grade,
                'value'     => function ($model, $key, $index, $column) {
                    return $model->grade ? Supplier::$grade[$model->grade] : '';
                }
            ],
            'grade_reason',
            'advantage_product',
            [
                'attribute' => 'admin_id',
                'label'     => '询价员',
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
                'class' => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'header' => '操作',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
