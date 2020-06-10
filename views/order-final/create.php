<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderFinal */

$this->title = 'Create Order Final';
$this->params['breadcrumbs'][] = ['label' => 'Order Finals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-final-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
