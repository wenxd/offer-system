<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementStock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agreement-stock-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_agreement_id')->textInput() ?>

    <?= $form->field($model, 'order_agreement_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_purchase_id')->textInput() ?>

    <?= $form->field($model, 'order_purchase_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_payment_id')->textInput() ?>

    <?= $form->field($model, 'order_payment_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_id')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'use_number')->textInput() ?>

    <?= $form->field($model, 'all_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_tax_price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
