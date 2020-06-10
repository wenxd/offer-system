<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempNotGoods */

$this->title = 'Create Temp Not Goods';
$this->params['breadcrumbs'][] = ['label' => 'Temp Not Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-not-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
