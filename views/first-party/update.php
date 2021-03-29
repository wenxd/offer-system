<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FirstParty */

$this->title = '修改甲方采办人: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'First Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="first-party-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
