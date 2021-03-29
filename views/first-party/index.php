<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '甲方采办人';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="first-party-index">

    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'created_at',
            'updated_at',
            [
                'class' => \yii\grid\ActionColumn::className(),
                'header' => '操作',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
