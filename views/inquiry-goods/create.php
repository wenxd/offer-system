<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\InquiryGoods */

$this->title = 'Create Inquiry Goods';
$this->params['breadcrumbs'][] = ['label' => 'Inquiry Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
