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

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    //实现商品编号唯一的验证跳转
    $('#goods-goods_number').blur(function () {
        var goods_number = $('#goods-goods_number').val();
        $.ajax({
            type:"GET",
            url:"?r=goods/get-number",
            data:{goods_number:goods_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    window.location.href = '?r=goods/update&id=' + res.data;
                }
            }
        })
    });
</script>
