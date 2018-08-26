<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = '库存详情';
$this->params['breadcrumbs'][] = ['label' => '库存列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'good_id',
            'supplier_id',
            'supplier_name',
            'price',
            'position',
            'number',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
