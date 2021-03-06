<?php

use app\models\SystemConfig;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Supplier;
use app\models\Goods;
use app\models\Stock;
/* @var $this yii\web\View */
/* @var $model app\models\Stock */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {
    if (isset($_GET['goods_id']) && $_GET['goods_id']) {
        $model->good_id = $_GET['goods_id'];
    }
    $model->tax_rate = SystemConfig::find()->select('value')->where([
        'title'  => SystemConfig::TITLE_TAX,
        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
}
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'good_id')
        ->dropDownList($model->isNewRecord ? Goods::getCreateDropDown() : Goods::getAllDropDown())
        ->label('零件号') ?>

    <?= $form->field($model, 'tax_rate')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput() ?>
   
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
    var tax = $('#stock-tax_rate').val();

    $('#stock-price').blur(function () {
        var price = $('#stock-price').val();
        var tax_price = price * (1 + tax/100);
        $("#stock-tax_price").attr("value",tax_price.toFixed(2));
        $("#stock-tax_price").val(tax_price.toFixed(2));
    });

    $('#stock-tax_price').blur(function () {
        var tax_price = $('#stock-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#stock-price").attr("value",price.toFixed(2));
        $("#stock-price").val(price.toFixed(2));
    });
</script>
