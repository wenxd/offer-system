<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderQuote */

$this->title = 'Create Order Quote';
$this->params['breadcrumbs'][] = ['label' => 'Order Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
