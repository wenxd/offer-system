<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Competitor */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '竞争对手列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
?>
<div class="competitor-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
