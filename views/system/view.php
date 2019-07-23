<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SystemConfig;

/* @var $this yii\web\View */
/* @var $model app\models\SystemConfig */

$this->title = '配置详情';
$this->params['breadcrumbs'][] = ['label' => '配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'title',
                'filter'    => SystemConfig::$config,
                'value'     => function ($model) {
                    return SystemConfig::$config[$model->title];
                }
            ],
            'value',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
