<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\{Competitor, Customer, SystemConfig};

/* @var $this yii\web\View */
/* @var $model app\models\CompetitorGoods */
/* @var $form yii\widgets\ActiveForm */
$model->tax_rate = SystemConfig::find()->select('value')->where([
    'title'  => SystemConfig::TITLE_TAX,
    'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
if (!$model->isNewRecord) {
    $model->offer_date = substr($model->offer_date, 0, 10);
}
?>
<style>
    .box-search li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-ul {
        margin-left: -40px;
    }
    .box-search {
        width: 200px;
        margin-top: -15px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search li:hover {
        background-color: #84b5bc;
    }
    .cancel {
        display: none;
    }
</style>
<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true])->label('零件号A') ?>
    <div class="box-search cancel">
        <ul class="box-search-ul">

        </ul>
    </div>
    <?= $form->field($model, 'competitor_id')->dropDownList(Competitor::getAllDropDown())->label('竞争对手') ?>

    <?= $form->field($model, 'customer')->dropDownList(Customer::getAllDropDown())->label('针对客户') ?>

    <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'offer_date')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd',
            'startView' =>2,  //其实范围（0：日  1：天 2：年）
            'maxView'   =>2,  //最大选择范围（年）
            'minView'   =>2,  //最小选择范围（年）
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

    $("#competitorgoods-price").bind('input propertychange', function (e) {
        var price = $('#competitorgoods-price').val();
        var tax_price = price * (1 + tax/100);
        $("#competitorgoods-tax_price").attr("value",tax_price.toFixed(2));
        $("#competitorgoods-tax_price").val(tax_price.toFixed(2));
    });
    $("#competitorgoods-tax_price").bind('input propertychange', function (e) {
        var tax_price = $('#competitorgoods-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#competitorgoods-price").attr("value",price.toFixed(2));
        $("#competitorgoods-price").val(price.toFixed(2));
    });

    // $('#competitorgoods-price').blur(function () {
    //     var price = $('#competitorgoods-price').val();
    //     var tax_price = price * (1 + tax/100);
    //     $("#competitorgoods-tax_price").attr("value",tax_price.toFixed(2));
    //     $("#competitorgoods-tax_price").val(tax_price.toFixed(2));
    // });
    //
    // $('#competitorgoods-tax_price').blur(function () {
    //     var tax_price = $('#competitorgoods-tax_price').val();
    //     var price = tax_price / (1 + tax/100);
    //     $("#competitorgoods-price").attr("value",price.toFixed(2));
    //     $("#competitorgoods-price").val(price.toFixed(2));
    // });

    $("#competitorgoods-goods_number").bind('input propertychange', function (e) {
        var good_number = $('#competitorgoods-goods_number').val();
        if (good_number === '') {
            $('.box-search').addClass('cancel');
            return;
        }
        $('.box-search-ul').html("");
        $('.box-search').removeClass('cancel');
        $.ajax({
            type:"GET",
            url:"?r=search/get-good-number",
            data:{good_number:good_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="select($(this))">' + res.data[i] + '</li>';
                    }
                    if (li) {
                        $('.box-search-ul').append(li);
                    } else {
                        $('.box-search').addClass('cancel');
                    }
                }
            }
        })
    });

    function select(obj){
        $("#competitorgoods-goods_number").val(obj.html());
        $('.box-search').addClass('cancel');
    }
</script>
