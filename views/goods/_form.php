<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Brand;
use app\models\SystemConfig;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
/* @var $form yii\widgets\ActiveForm */

//汇率
$rate = SystemConfig::find()->select('value')->where([
    'title'      => SystemConfig::TITLE_RATE,
    'is_deleted' => SystemConfig::IS_DELETED_NO,
])->scalar();

//到货系数
$arrivalRatio = SystemConfig::find()->select('value')->where([
    'title'      => SystemConfig::TITLE_ARRIVAL_RATIO,
    'is_deleted' => SystemConfig::IS_DELETED_NO,
])->scalar();

$deviceList = [];
if ($model->isNewRecord) {
    $model->unit = '件';
    $tax = SystemConfig::find()->select('value')->where([
        'title'      => SystemConfig::TITLE_TAX,
        'is_deleted' => SystemConfig::IS_DELETED_NO,
    ])->scalar();
    $model->publish_tax = $tax ? $tax : 0;
} else {
    $deviceList = json_decode($model->device_info, true);
    $stock = \app\models\Stock::find()->where(['good_id' => $model->id])->one();
    if ($stock) {
        $model->suggest_number = $stock->suggest_number;
    } else {
        $model->suggest_number = 0;
    }
}
$brandList = Brand::getList();
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>
    <div class="box-body">

        <?= $form->field($model, 'brand_id')->dropDownList($brandList)->label('品牌') ?>

        <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'goods_number_b')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description_en')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'factory_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'publish_tax')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'publish_tax_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'estimate_publish_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'publish_delivery_time')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'material')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'original_company')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'original_company_remark')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'import_mark')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'is_tz')->radioList(Goods::$tz, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_process')->radioList(Goods::$process, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_standard')->radioList(Goods::$standard, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_import')->radioList(Goods::$import, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_emerg')->radioList(Goods::$emerg, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_repair')->radioList(Goods::$repair, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_assembly')->radioList(Goods::$emerg, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_special')->radioList(Goods::$special, ['class' => 'radio']) ?>
        <?= $form->field($model, 'is_nameplate')->radioList(Goods::$nameplate, ['class' => 'radio']) ?>
        <?= $form->field($model, 'part')->textInput(['maxlength' => true]) ?>

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
        <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'suggest_number')->textInput(['maxlength' => true]) ?>

        <button type="button" class="glyphicon glyphicon-plus btn btn-primary btn-sm add-device" name="button">添加设备信息</button>

    <div class="form-group field-goods-device_info">
        <div class="device_list">
            <div class="input-group" style="display: none">
                <span class="input-group-addon device-name">设备名称</span>
                <span class="input-group-addon">
                    <input type="text" class="form-control" name="Goods[device_info][name][]" value="" placeholder="输入设备数值">
                </span>
                <span class="input-group-addon device-number">设备数值</span>
                <span class="input-group-addon">
                    <input type="text" class="form-control" name="Goods[device_info][number][]" value="" placeholder="输入设备数值">
                </span>
            </div>
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

    $('.file-preview').click(function () {
       console.log(111);
    });

    //美金出厂价变动
    $("#goods-factory_price").bind('input propertychange', function (e) {
        var factory_price  = parseFloat($(this).val());
        if (factory_price) {
            var rate = '<?=$rate?>';
            var arrival_ratio = '<?=$arrivalRatio?>';
            var publish_tax = $('#goods-publish_tax').val();
            var publish_tax_price = factory_price * rate * arrival_ratio * (1+publish_tax/100);
            $('#goods-publish_tax_price').val(publish_tax_price.toFixed(2));
        }
    });
    //发行税率
    $("#goods-publish_tax").bind('input propertychange', function (e) {
        var publish_tax  = parseFloat($(this).val());
        var factory_price  = $('#goods-factory_price').val();
        if (factory_price) {
            var rate = '<?=$rate?>';
            var arrival_ratio = '<?=$arrivalRatio?>';
            var publish_tax_price = factory_price * rate * arrival_ratio * (1+publish_tax/100);
            $('#goods-publish_tax_price').val(publish_tax_price.toFixed(2));
        }
    });

</script>
