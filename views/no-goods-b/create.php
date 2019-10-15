<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempNotGoodsB */

$this->title = 'Create Temp Not Goods B';
$this->params['breadcrumbs'][] = ['label' => 'Temp Not Goods Bs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-not-goods-b-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
