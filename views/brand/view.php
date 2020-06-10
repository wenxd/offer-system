<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Brand */

$this->title = '品牌详情';
$this->params['breadcrumbs'][] = ['label' => '品牌列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="brand-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
