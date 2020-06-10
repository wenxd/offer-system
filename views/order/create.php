<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $number  */

$this->title = '创建订单';
?>
<div class="order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'number' => $number
    ]) ?>

</div>
