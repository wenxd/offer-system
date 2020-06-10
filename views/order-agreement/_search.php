<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAgreementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-agreement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'agreement_sn') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_quote_id') ?>

    <?= $form->field($model, 'order_quote_sn') ?>

    <?php // echo $form->field($model, 'goods_info') ?>

    <?php // echo $form->field($model, 'agreement_date') ?>

    <?php // echo $form->field($model, 'is_quote') ?>

    <?php // echo $form->field($model, 'admin_id') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
