<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agreement-goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_agreement_id')->textInput() ?>

    <?= $form->field($model, 'order_agreement_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_quote_id')->textInput() ?>

    <?= $form->field($model, 'order_quote_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'serial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'relevance_id')->textInput() ?>

    <?= $form->field($model, 'tax_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quote_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quote_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quote_all_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quote_all_tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'is_agreement')->textInput() ?>

    <?= $form->field($model, 'agreement_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'purchase_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'agreement_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
