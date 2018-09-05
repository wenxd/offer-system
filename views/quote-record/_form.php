<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\QuoteRecord;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteRecord */
/* @var $form yii\widgets\ActiveForm */
if ($model->type == 3) {
    $model->goods_number  = $model->goods->goods_number;
    $model->supplier_id   = $model->stock->supplier->id;
    $model->supplier_name = $model->stock->supplier->name;
    $model->price         = $model->stock->price;
    $model->tax_price     = $model->stock->tax_price;
} else {
    $model->goods_number  = $model->goods->goods_number;
    $model->supplier_id   = $model->inquiry->supplier->id;
    $model->supplier_name = $model->inquiry->supplier->name;
    $model->price         = $model->inquiry->price;
    $model->tax_price     = $model->inquiry->tax_price;
}
$order_inquiry_id = Yii::$app->session->get('order_inquiry_id');
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'goods_id')->textInput(['readonly' => true])->label('商品ID') ?>
    <?= $form->field($model, 'goods_number')->textInput(['readonly' => true])->label('商品编码') ?>
    <?= $form->field($model, 'supplier_id')->textInput(['readonly' => true])->label('供应商ID') ?>
    <?= $form->field($model, 'supplier_name')->textInput(['readonly' => true])->label('供应商名称') ?>

    <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'offer_date')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd hh:ii:00'
        ]
    ]);?>
    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['order-inquiry/detail', 'id' => $order_inquiry_id]), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    //实现税率自动转换
    var tax = $('#quoterecord-tax_rate').val();

    $('#quoterecord-price').blur(function () {
        var price = $('#quoterecord-price').val();
        var tax_price = price * (1 + tax/100);
        $("#quoterecord-tax_price").attr("value",tax_price.toFixed(2));
        $("#quoterecord-tax_price").val(tax_price.toFixed(2));
    });

    $('#quoterecord-tax_price').blur(function () {
        var tax_price = $('#quoterecord-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#quoterecord-price").attr("value",price.toFixed(2));
        $("#quoterecord-price").val(price.toFixed(2));
    });
</script>
