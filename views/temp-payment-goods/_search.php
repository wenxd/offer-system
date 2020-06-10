<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_payment_id') ?>

    <?= $form->field($model, 'order_payment_sn') ?>

    <?= $form->field($model, 'order_purchase_id') ?>

    <?php // echo $form->field($model, 'order_purchase_sn') ?>

    <?php // echo $form->field($model, 'purchase_goods_id') ?>

    <?php // echo $form->field($model, 'serial') ?>

    <?php // echo $form->field($model, 'goods_id') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'relevance_id') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'tax_rate') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'tax_price') ?>

    <?php // echo $form->field($model, 'all_price') ?>

    <?php // echo $form->field($model, 'all_tax_price') ?>

    <?php // echo $form->field($model, 'fixed_price') ?>

    <?php // echo $form->field($model, 'fixed_tax_price') ?>

    <?php // echo $form->field($model, 'fixed_all_price') ?>

    <?php // echo $form->field($model, 'fixed_all_tax_price') ?>

    <?php // echo $form->field($model, 'fixed_number') ?>

    <?php // echo $form->field($model, 'inquiry_admin_id') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'is_quality') ?>

    <?php // echo $form->field($model, 'supplier_id') ?>

    <?php // echo $form->field($model, 'delivery_time') ?>

    <?php // echo $form->field($model, 'before_supplier_id') ?>

    <?php // echo $form->field($model, 'before_delivery_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
