<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */

$this->title = '询价详情';
$this->params['breadcrumbs'][] = ['label' => '询价列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'good_id',
            'supplier_id',
            'supplier_name',
            'inquiry_price',
            'inquiry_datetime',
            'sort',
            'is_better',
            'is_newest',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
