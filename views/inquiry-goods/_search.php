<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InquiryGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inquiry-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'inquiry_sn') ?>

    <?= $form->field($model, 'goods_id') ?>

    <?= $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'serial') ?>

    <?php // echo $form->field($model, 'is_inquiry') ?>

    <?php // echo $form->field($model, 'is_result') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'not_result_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
