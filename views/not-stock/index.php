<?php

use app\extend\grid\ActionColumn;
use app\extend\widgets\Bar;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NotStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '缺少库存列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{download} {delete}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载', Url::to(['download']), [
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
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'goods_number',
            [
                'class' => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 200px;'],
                'header' => '操作',
                'template' => '{delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
