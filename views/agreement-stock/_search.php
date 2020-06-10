<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementStockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agreement-stock-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_agreement_id') ?>

    <?= $form->field($model, 'order_agreement_sn') ?>

    <?= $form->field($model, 'order_purchase_id') ?>

    <?php // echo $form->field($model, 'order_purchase_sn') ?>

    <?php // echo $form->field($model, 'order_payment_id') ?>

    <?php // echo $form->field($model, 'order_payment_sn') ?>

    <?php // echo $form->field($model, 'goods_id') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'tax_price') ?>

    <?php // echo $form->field($model, 'use_number') ?>

    <?php // echo $form->field($model, 'all_price') ?>

    <?php // echo $form->field($model, 'all_tax_price') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
