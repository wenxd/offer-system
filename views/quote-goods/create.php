<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteGoods */

$this->title = 'Create Quote Goods';
$this->params['breadcrumbs'][] = ['label' => 'Quote Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
