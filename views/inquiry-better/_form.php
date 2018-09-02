<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use app\models\Supplier;
use app\models\Inquiry;
use app\models\Goods;
/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'good_id')
        ->dropDownList($model->isNewRecord ? Goods::getCreateDropDown() : Goods::getAllDropDown())
        ->label('零件号') ?>

    <?= $form->field($model, 'supplier_id')->dropDownList(Supplier::getCreateDropDown())
        ->label('供应商名称') ?>

    <?= $form->field($model, 'inquiry_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inquiry_datetime')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd hh:ii:00'
        ]
    ]);?>

    <?= $form->field($model, 'is_better')->dropDownList(Inquiry::$better) ?>

    <?= $form->field($model, 'is_priority')->dropDownList(Inquiry::$priority) ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
