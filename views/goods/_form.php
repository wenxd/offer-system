<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
/* @var $this yii\web\View */
/* @var $model app\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_company')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_company_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'technique_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img_id')->widget(FileInput::classname(), [
        'options' => [
            'accept' => 'image/*'
        ],
        'pluginOptions' => [
            'initialPreviewAsData' => true,
            'initialPreview'       => $model->img_url ? [$model->img_url] : [],
            'showUpload'           => false,
            'overwriteInitial'     => true,
            'dropZoneTitle'        => '请选择图片'
        ]
    ]); ?>

    <?= $form->field($model, 'competitor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'competitor_offer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'offer_date')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd hh:ii:00',
            'startView' =>2,  //其实范围（0：日  1：天 2：年）
            'maxView'   =>2,  //最大选择范围（年）
            'minView'   =>0,  //最小选择范围（年）
        ]
    ]);?>

    <?= $form->field($model, 'is_process')->radioList(Goods::$process, ['class' => 'radio']) ?>

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
