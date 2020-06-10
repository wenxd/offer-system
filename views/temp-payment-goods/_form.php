<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_payment_id')->textInput() ?>

    <?= $form->field($model, 'order_payment_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_purchase_id')->textInput() ?>

    <?= $form->field($model, 'order_purchase_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'purchase_goods_id')->textInput() ?>

    <?= $form->field($model, 'serial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'relevance_id')->textInput() ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'tax_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_all_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_all_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fixed_number')->textInput() ?>

    <?= $form->field($model, 'inquiry_admin_id')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'is_quality')->textInput() ?>

    <?= $form->field($model, 'supplier_id')->textInput() ?>

    <?= $form->field($model, 'delivery_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'before_supplier_id')->textInput() ?>

    <?= $form->field($model, 'before_delivery_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
