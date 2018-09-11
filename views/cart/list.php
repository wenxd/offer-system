<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Customer;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

$this->title = '预生成报价单、询价单列表';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->id) {
    $model->provide_date = date('Y-m-d H:i:00');
    $model->order_id     = date('YmdHis');
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
                        <th>选择</th>
                        <th>零件号</th>
                        <th>是否最新</th>
                        <th>是否优选</th>
                        <th>商品类型</th>
                        <th style="width: 150px;">报价金额</th>
                        <th>库存数量</th>
                        <th style="width: 150px;">询价时间</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>购买数量</th>
                        <th>金额</th>
                        <th style="width: 230px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cartList as $key => $value):?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><?=$value->goods->goods_number?></td>
                        <td>
                            <?php
                                if ($value->type == 1 || $value->type == 2) {
                                    echo Inquiry::$newest[$value->inquiry->is_newest];
                                } else {
                                    echo '否';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if ($value->type == 1 || $value->type == 2) {
                                    echo Inquiry::$newest[$value->inquiry->is_better];
                                } else {
                                    echo '否';
                                }
                            ?>
                        </td>
                        <td><?=$value->type == 3 ? '库存商品' : '询价商品'?></td>
                        <td class="price" data-cart_id="<?=$value->id?>"><?=$value['quotation_price']?><a style="margin-left: 10px;"><i class="fa fa-edit" onclick="editPrice(this)" data-price="<?=$value['quotation_price']?>"></i></a></td>
                        <td><?=$value->type == 3 ? $value->stock->number : '无限多'?></td>
                        <td><?=$value->type == 3 ? '无' : $value->inquiry->inquiry_datetime?></td>
                        <td><?=$value->type == 3 ? $value->stock->supplier_id : $value->inquiry->supplier_id?></td>
                        <td><?=$value->type == 3 ? Supplier::getAllDropDown()[$value->stock->supplier_id] : Supplier::getAllDropDown()[$value->inquiry->supplier_id]?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['quotation_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td>
                            <a class="btn btn-danger btn-xs btn-flat delete" href="javascript:void(0);" data-cart-id="<?=$value['id']?>" ><i class="fa fa-trash"></i> 删除</a>
                            <a class="btn btn-success btn-xs btn-flat" href="<?=Url::to(['goods/view', 'id' => $value->goods->id])?>"><i class="fa fa-product-hunt"></i> 零件详情</a>
                            <a class="btn btn-info btn-xs btn-flat" href="<?=Url::to(['inquiry/index', 'InquirySearch[good_id]' => $value->goods->id])?>"><i class="fa fa-list"></i> 询价记录</a>
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

            <?= $form->field($model, 'customer_id')->dropDownList(Customer::getCreateDropDown())?>

            <?= $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'provide_date')->widget(DateTimePicker::className(), [
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd hh:ii:00',
                    'startView' =>2,  //其实范围（0：日  1：天 2：年）
                    'maxView'   =>2,  //最大选择范围（年）
                    'minView'   =>2,  //最小选择范围（年）
                ]
            ]);?>

            <?= $form->field($model, 'quote_price')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('保存报价单', [
                    'class' => 'btn btn-info quote_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::submitButton('保存询价单', [
                    'class' => 'btn btn-success inquiry_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::a('<i class="fa fa-reply"></i> 继续添加', Url::to(['search/index']), [
                'class' => 'btn btn-default btn-flat',
            ])?>
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
