<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderPayment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'payment_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_purchase_id')->textInput() ?>

    <?= $form->field($model, 'order_purchase_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_at')->textInput() ?>

    <?= $form->field($model, 'is_payment')->textInput() ?>

    <?= $form->field($model, 'admin_id')->textInput() ?>

    <?= $form->field($model, 'purchase_status')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
