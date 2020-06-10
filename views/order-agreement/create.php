<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderAgreement */

$this->title = 'Create Order Agreement';
$this->params['breadcrumbs'][] = ['label' => 'Order Agreements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-agreement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
