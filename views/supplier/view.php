<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Supplier */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '运营商列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'mobile',
            'telephone',
            'email',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
