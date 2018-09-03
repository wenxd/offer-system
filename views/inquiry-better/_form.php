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

$model->tax_rate='10';

?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

        <?= $form->field($model, 'good_id')
            ->dropDownList($model->isNewRecord ? Goods::getCreateDropDown() : Goods::getAllDropDown())
            ->label('零件号') ?>

        <?= $form->field($model, 'supplier_id')->dropDownList(Supplier::getCreateDropDown())
            ->label('供应商名称') ?>

        <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true])
        //        ->hint('只输入数字，例如10%，只输入10')
        ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inquiry_datetime')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd hh:ii:00'
            ]
        ]);?>

        <?= $form->field($model, 'offer_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd hh:ii:00'
            ]
        ]);?>

        <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

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

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    //实现税率自动转换
    var tax = $('#inquiry-tax_rate').val();

    $('#inquiry-price').blur(function () {
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        $("#inquiry-tax_price").attr("value",tax_price.toFixed(2));
        $("#inquiry-tax_price").val(tax_price.toFixed(2));
    });

    $('#inquiry-tax_price').blur(function () {
        var tax_price = $('#inquiry-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#inquiry-price").attr("value",price.toFixed(2));
        $("#inquiry-price").val(price.toFixed(2));
    });
</script>
