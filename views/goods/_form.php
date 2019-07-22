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

$deviceList = [];
if ($model->isNewRecord) {
    $model->unit = '个';
} else {
    $deviceList = json_decode($model->device_info, true);
}
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_number_b')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        
    <?= $form->field($model, 'description_en')->textInput(['maxlength' => true]) ?>
        
    <?= $form->field($model, 'original_company')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_company_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_process')->radioList(Goods::$process, ['class' => 'radio']) ?>

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

    <?= $form->field($model, 'is_special')->radioList(Goods::$special, ['class' => 'radio']) ?>

    <?= $form->field($model, 'is_emerg')->radioList(Goods::$emerg, ['class' => 'radio']) ?>
        
    <?= $form->field($model, 'is_assembly')->radioList(Goods::$emerg, ['class' => 'radio']) ?>
        
    <?= $form->field($model, 'is_nameplate')->radioList(Goods::$nameplate, ['class' => 'radio']) ?>
        
    <?= $form->field($model, 'nameplate_img_id')->widget(FileInput::classname(), [
        'options' => [
            'accept' => 'image/*'
        ],
        'pluginOptions' => [
            'initialPreviewAsData' => true,
            'initialPreview'       => $model->nameplate_img_url ? [$model->nameplate_img_url] : [],
            'showUpload'           => false,
            'overwriteInitial'     => true,
            'dropZoneTitle'        => '请选择图片'
        ]
    ]); ?>

    <?= $form->field($model, 'technique_remark')->textInput(['maxlength' => true]) ?>

    <button type="button" class="glyphicon glyphicon-plus btn btn-primary btn-sm add-device" name="button">添加设备信息</button>

    <div class="form-group field-goods-device_info">
        <div class="device_list">
            <?php if ($deviceList):?>
            <?php foreach ($deviceList as $device => $number):?>
                <div class="input-group">
                    <span class="input-group-addon device-name">设备名称</span>
                    <span class="input-group-addon">
                        <input type="text" class="form-control" name="Goods[device_info][name][]" value="<?=$device?>" placeholder="输入设备数值">
                    </span>
                    <span class="input-group-addon device-number">设备数值</span>
                    <span class="input-group-addon">
                        <input type="text" class="form-control" name="Goods[device_info][number][]" value="<?=$number?>" placeholder="输入设备数值">
                    </span>
                    <span class="input-group-addon">
                        <a class="btn-danger btn-sm btn-flat" href="javascript:void(0)" onclick="del(this)"><i class="fa fa-trash"></i> 删除</a>
                    </span>
                </div>
            <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>

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

    $('.add-device').click(function () {
        var html = '<div class="input-group">\n' +
            '            <span class="input-group-addon device-name">设备名称</span>\n' +
            '            <span class="input-group-addon">\n' +
            '                <input type="text" class="form-control" name="Goods[device_info][name][]" placeholder="输入设备数值">\n' +
            '            </span>\n' +
            '            <span class="input-group-addon device-number">设备数值</span>\n' +
            '            <span class="input-group-addon">\n' +
            '                <input type="text" class="form-control" name="Goods[device_info][number][]" placeholder="输入设备数值">\n' +
            '            </span>\n' +
            '            <span class="input-group-addon">\n' +
            '                <a class="btn-danger btn-sm btn-flat" href="javascript:void(0)" onclick="del(this)"><i class="fa fa-trash"></i> 删除</a>\n' +
            '            </span>\n' +
            '        </div>';

        $('.device_list').append(html);
    });

    function del(obj) {
        $(obj).parent().parent().remove();
    }



</script>
