<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Competitor;
use app\models\Customer;
/* @var $this yii\web\View */
/* @var $model app\models\CompetitorGoods */
/* @var $form yii\widgets\ActiveForm */
$model->tax_rate='16';
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'goods_id')->dropDownList(Goods::getAllDropDown())->label('零件号') ?>

    <?= $form->field($model, 'competitor_id')->dropDownList(Competitor::getAllDropDown())->label('竞争对手') ?>

    <?= $form->field($model, 'customer')->dropDownList(Customer::getAllDropDown())->label('针对客户') ?>

    <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

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

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

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
    var tax = $('#competitorgoods-tax_rate').val();

    $('#competitorgoods-price').blur(function () {
        var price = $('#competitorgoods-price').val();
        var tax_price = price * (1 + tax/100);
        $("#competitorgoods-tax_price").attr("value",tax_price.toFixed(2));
        $("#competitorgoods-tax_price").val(tax_price.toFixed(2));
    });

    $('#competitorgoods-tax_price').blur(function () {
        var tax_price = $('#competitorgoods-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#competitorgoods-price").attr("value",price.toFixed(2));
        $("#competitorgoods-price").val(price.toFixed(2));
    });
</script>
