<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempNotGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库中没有的零件号列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{download} {delete}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载库中没有的零件', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
                    ]);
                },
                'delete' => function () {
                    return Html::a('<i class="fa fa-delete"></i> 清空', Url::to(['delete-all']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-danger btn-flat',
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
            'id',
            'goods_number',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
