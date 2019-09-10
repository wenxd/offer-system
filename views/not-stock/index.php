<?php

use app\extend\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NotStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '缺少库存列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
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
