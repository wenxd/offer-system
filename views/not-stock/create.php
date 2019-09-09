<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempNotStock */

$this->title = 'Create Temp Not Stock';
$this->params['breadcrumbs'][] = ['label' => 'Temp Not Stocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-not-stock-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
