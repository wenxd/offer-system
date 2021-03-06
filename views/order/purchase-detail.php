<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Customer;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

$this->title = '采购单详情';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->id) {
    $model->provide_date = date('Y-m-d H:i:00');
}
?>
<style>
    .but button{
        float: right;
    }
    .but a{
        float: right;
        margin-right: 10px;
    }
    .offer {
        margin-top: 10px;
    }
</style>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>厂家号</th>
                    <th>最新</th>
                    <th>优选</th>
                    <th>商品类型</th>
                    <th style="width: 150px;">报价金额</th>
                    <th>库存数量</th>
                    <th style="width: 150px;">询价时间</th>
                    <th>供应商ID</th>
                    <th>供应商名称</th>
                    <th>购买数量</th>
                    <th>金额</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><?=$value->goods->goods_number?></td>
                        <td>
                            <?php
                            if ($value->type == 0 || $value->type == 1) {
                                echo Inquiry::$newest[$value->inquiry->is_newest];
                            } else {
                                echo '否';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($value->type == 0 || $value->type == 1) {
                                echo Inquiry::$newest[$value->inquiry->is_better];
                            } else {
                                echo '否';
                            }
                            ?>
                        </td>
                        <td><?=$value->type == 3 ? '库存商品' : '询价商品'?></td>
                        <td class="price" data-cart_id="<?=$value->id?>"><?=$value['quote_price']?></td>
                        <td><?=$value->type == 3 ? $value->stock->number : '无限多'?></td>
                        <td><?=$value->type == 3 ? '无' : $value->inquiry->inquiry_datetime?></td>
                        <td><?=$value->type == 3 ? $value->stock->supplier_id : $value->inquiry->supplier_id?></td>
                        <td><?=$value->type == 3 ? Supplier::getAllDropDown()[$value->stock->supplier_id] : Supplier::getAllDropDown()[$value->inquiry->supplier_id]?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['quote_price'] * $value['number'], 2, '.', '')?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="10" style="text-align: right;"><b>金额合计</b></td>
                    <td class="all_money"></td>
                    <td></td>
                </tr>
                </tbody>
            </table>

            <?= $form->field($model, 'customer_id')->dropDownList(Customer::getCreateDropDown())->textInput(['readonly' => true])?>

            <?= $form->field($model, 'order_sn')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'description')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'provide_date')->widget(DateTimePicker::className(), [
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd hh:ii:00',
                    'startView' =>2,  //其实范围（0：日  1：天 2：年）
                    'maxView'   =>2,  //最大选择范围（年）
                    'minView'   =>2,  //最小选择范围（年）
                ]
            ])->textInput(['readonly' => 'true']);?>

            <?= $form->field($model, 'order_price')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'remark')->textInput(['readonly' => true]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    function totalMoney() {
        var allMoney = 0;
        $('.money').each(function (index, element){
            allMoney += parseFloat(element.innerText)
        });
        $('.all_money').html(allMoney);
    }
    function editPrice(obj) {
        var price = $(obj).data('price');
        var html = '<input type="text" style="width:100px"><a style="margin-left: 10px;"><i class="fa fa-save" onclick="savePrice(this)" data-price="' + price + '"></i></a>';
        $(obj).parent().parent().html(html);
    }

    function savePrice(obj) {
        var number      = $(obj).parent().parent().parent().find('td').eq(9).html();
        var beforePrice = $(obj).data('price');
        var beforeHtml  = beforePrice + '<a style="margin-left: 10px;"><i class="fa fa-edit" onclick="editPrice(this)" data-price="' + beforePrice + '"></i></a>';
        var price       = $(obj).parent().parent().find('input').val();
        var cart_id     = $(obj).parent().parent().data('cart_id');
        if (!price) {
            $(obj).parent().parent().html(beforeHtml);
            return;
        }
        var reg = /^[0-9]*$/;
        if (!reg.test(price) || price <= 0) {
            layer.msg('数量请输入正整数', {time:2000});
            return false;
        }
        var saveHtml = price + '<a style="margin-left: 10px;"><i class="fa fa-edit" onclick="editPrice(this)" data-price="' + price + '"></i></a>';
        $.ajax({
            type:"post",
            url:"?r=cart/edit-price",
            data:{price:price, cart_id:cart_id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200) {
                    $(obj).parent().parent().html(saveHtml);
                    layer.msg(res.msg, {time:500}, function(){
                        window.location.reload();
                    });
                }
            }
        });
    }
    window.onload = function() {

        totalMoney();

        function submit(type) {
            $('form').submit(function(e){
                e.preventDefault();
                var form = $(this).serializeArray();

                var parameter = '';
                var is_go = false;
                console.log(form);
                $.each(form, function() {
                    if (!this.value) {
                        is_go = true;
                    }
                    parameter += this.name + '=' + this.value + '&';
                });
                parameter += 'type=' + type;
                if (is_go) {
                    return false;
                }
                $.ajax({
                    type:"get",
                    url:"?r=order-inquiry/submit&" + parameter,
                    data:{},
                    dataType:'JSON',
                    success:function(res){
                        if (res && res.code == 200) {
                            layer.msg(res.msg, {time:1500}, function(){
                                if (type == 1) {
                                    location.replace("?r=order-quote/index");
                                } else {
                                    location.replace("?r=order-inquiry/index");
                                }
                            });
                        }
                    }
                });
            });
        }
        //报价
        $('.quote_save').click(function () {
            submit(1);
        });
        //询价
        $('.inquiry_save').click(function () {
            submit(2);
        });
        $('.delete').click(function () {
            var cart_id = $(this).data('cart-id');
            $.ajax({
                type:"get",
                url:"?r=cart/delete",
                data:{id:cart_id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time:1500}, function(){
                            location.reload();
                        });
                    }

                }
            })
        });
    }
</script>