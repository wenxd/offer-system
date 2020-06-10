<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAgreement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-agreement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agreement_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_quote_id')->textInput() ?>

    <?= $form->field($model, 'order_quote_sn')->textInput() ?>

    <?= $form->field($model, 'goods_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'agreement_date')->textInput() ?>

    <?= $form->field($model, 'is_quote')->textInput() ?>

    <?= $form->field($model, 'admin_id')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
