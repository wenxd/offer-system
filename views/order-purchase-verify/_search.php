<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderPaymentVerifySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'payment_sn') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_purchase_id') ?>

    <?= $form->field($model, 'order_purchase_sn') ?>

    <?php // echo $form->field($model, 'goods_info') ?>

    <?php // echo $form->field($model, 'payment_at') ?>

    <?php // echo $form->field($model, 'is_payment') ?>

    <?php // echo $form->field($model, 'admin_id') ?>

    <?php // echo $form->field($model, 'purchase_status') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
