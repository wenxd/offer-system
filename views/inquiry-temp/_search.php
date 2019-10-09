<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InquiryTempSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inquiry-temp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'good_id') ?>

    <?= $form->field($model, 'supplier_id') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'tax_price') ?>

    <?php // echo $form->field($model, 'tax_rate') ?>

    <?php // echo $form->field($model, 'all_tax_price') ?>

    <?php // echo $form->field($model, 'all_price') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'inquiry_datetime') ?>

    <?php // echo $form->field($model, 'sort') ?>

    <?php // echo $form->field($model, 'is_better') ?>

    <?php // echo $form->field($model, 'is_newest') ?>

    <?php // echo $form->field($model, 'is_priority') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'offer_date') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'better_reason') ?>

    <?php // echo $form->field($model, 'delivery_time') ?>

    <?php // echo $form->field($model, 'admin_id') ?>

    <?php // echo $form->field($model, 'order_id') ?>

    <?php // echo $form->field($model, 'order_inquiry_id') ?>

    <?php // echo $form->field($model, 'inquiry_goods_id') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'is_upload') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
