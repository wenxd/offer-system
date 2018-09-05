<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\QuoteRecord */

$this->title = 'Create Quote Record';
$this->params['breadcrumbs'][] = ['label' => 'Quote Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
