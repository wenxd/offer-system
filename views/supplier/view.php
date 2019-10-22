<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Supplier;

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
            [
                'attribute' => 'admin_id',
                'label'     => '询价员',
                'value'     => function ($model) {
                    if ($model->admin) {
                        return $model->admin->username;
                    } else {
                        return '';
                    }
                }
            ],
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
