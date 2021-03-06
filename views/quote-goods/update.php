<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteGoods */

$this->title = 'Update Quote Goods: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
