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
            'short_name',
            'full_name',
            'contacts',
            'mobile',
            'telephone',
            'email',
            [
                'attribute' => 'grade',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return $model->grade ? Supplier::$grade[$model->grade] : '';
                }
            ],
            'grade_reason',
            'advantage_product',
            [
                'attribute' => 'is_confirm',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Supplier::$confirm[$model->is_confirm];
                }
            ],
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
