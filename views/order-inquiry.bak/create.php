<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = 'Create Order Inquiry';
$this->params['breadcrumbs'][] = ['label' => 'Order Inquiries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-inquiry-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
