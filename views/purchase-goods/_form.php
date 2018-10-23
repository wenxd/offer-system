<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PurchaseGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'order_final_id')->textInput() ?>

    <?= $form->field($model, 'order_purchase_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_purchase_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'relevance_id')->textInput() ?>

    <?= $form->field($model, 'is_purchase')->textInput() ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
