<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SystemConfig;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */
/* @var $form yii\widgets\ActiveForm */

?>
<style>
    /*零件号*/
    .box-search-goods_number li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-ul-goods_number {
        margin-left: -40px;
    }
    .box-search-goods_number {
        width: 200px;
        margin-top: -10px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search-goods_number li:hover {
        background-color: #84b5bc;
    }
    .cancel-goods_number {
        display: none;
    }
</style>
<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

        <?= $form->field($model, 'goods_id')->textInput()->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'goods_number')->textInput()->label('零件号') ?>

        <div class="box-search-goods_number cancel-goods_number">
            <ul class="box-search-ul-goods_number">

            </ul>
        </div>

        <?= $form->field($model, 'number')->textInput() ?>

        <?= $form->field($model, 'type')->dropDownList(['2' => '出库'])->label('出库') ?>

        <?= $form->field($model, 'customer_id')->dropDownList(Customer::getSelectDropDown())->label('客户') ?>

        <?= $form->field($model, 'region')->dropDownList(SystemConfig::getRegionList())->label('区块') ?>

        <?= $form->field($model, 'plat_name')->textInput()->label('平台名称') ?>

        <?= $form->field($model, 'direction')->dropDownList(SystemConfig::getList())->label('去向') ?>

        <?= $form->field($model, 'remark')->textInput()->label('备注') ?>

    </div>

    <div class="box-footer">
        <?= Html::Button($model->isNewRecord ? '添加' :  '更新', [
                'class' => 'btn btn-success stock-created',
                'name'  => 'submit-button']
        )?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    //零件A搜索
    $("#stocklog-goods_number").bind('input propertychange', function (e) {
        var good_number = $('#stocklog-goods_number').val();
        if (good_number === '') {
            $('.box-search-goods_number').addClass('cancel-goods_number');
            return;
        }
        $('.box-search-ul-goods_number').html("");
        $('.box-search-goods_number').removeClass('cancel-goods_number');
        $.ajax({
            type:"GET",
            url:"?r=search/get-new-good-number",
            data:{good_number:good_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="selectGoodsA($(this), ' + res.data[i]['id'] + ')" data-goods_number_b="' + res.data[i]['goods_number_b'] + '">' + res.data[i]['goods_number'] + '</li>';
                    }
                    if (li) {
                        $('.box-search-ul-goods_number').append(li);
                    } else {
                        $('.box-search-goods_number').addClass('cancel-goods_number');
                    }
                }
            }
        })
    });
    //选择零件A
    function selectGoodsA(obj, goods_id){
        $("#stocklog-goods_id").val(goods_id);
        $("#stocklog-goods_number").val($.trim(obj.html()));
        $('.box-search-goods_number').addClass('cancel-goods_number');
    }

    $('.stock-created').click(function (e) {
        var goods_id = $('#stocklog-goods_id').val();
        if (!goods_id) {
            layer.msg('请输入厂家号', {time:2000});
            return false;
        }

        var number = $('#stocklog-number').val();
        var reg = /^\+?[1-9][0-9]*$/;
        if (!reg.test(number)) {
            layer.msg('请输入正整数', {time:2000});
            return false;
        }

        var customer_id = $('#stocklog-customer_id').val();

        var region    = $('#stocklog-region').find("option:selected").text();

        var plat_name = $('#stocklog-plat_name').val();

        var direction = $('#stocklog-direction').find("option:selected").text();

        var remark    = $('#stocklog-remark').val();

        $.ajax({
            type:"POST",
            url:"?r=stock-out-log/add",
            data:{goods_id:goods_id, number:number, customer_id:customer_id, region:region, plat_name:plat_name,
                direction:direction, remark:remark},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    location.replace("?r=stock-out-log");
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    });
</script>