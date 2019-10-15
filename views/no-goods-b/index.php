<?php

use app\extend\widgets\Bar;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TempNotGoodsBSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库中没有的厂家号列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{download}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载库中没有的厂家号', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
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
            'columns' => [
                'id',
                'goods_id',
                'goods_number',
                'goods_number_b',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
