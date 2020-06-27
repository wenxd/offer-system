<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAgreement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">

        <?= $form->field($model, 'advancecharge_at')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter event time ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
        ])->label('预付款时间');?>

        <?= $form->field($model, 'payment_at')->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'Enter event time ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss'
            ]
        ])->label('付全款时间');?>

        <?= $form->field($model, 'bill_at')->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'Enter event time ...'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss'
            ]
        ])->label('收到发票时间');?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '添加' :  '更新', [
                'class' => 'btn btn-success',
                'name'  => 'submit-button']
        )?>
    </div>

    <?php ActiveForm::end(); ?>

</div>