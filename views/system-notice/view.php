<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SystemNotice;

/* @var $this yii\web\View */
/* @var $model app\models\SystemNotice */

$this->title = '系统消息';
$this->params['breadcrumbs'][] = ['label' => '消息列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-notice-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'content',
            [
                'attribute' => 'is_read',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return SystemNotice::$read[$model->is_read];
                }
            ],
            'notice_at',
        ],
    ]) ?>

</div>
