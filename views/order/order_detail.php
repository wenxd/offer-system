<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Customer;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

$this->title = '订单详情';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->id) {
    $model->provide_date = date('Y-m-d H:i:00');
    $model->order_sn = date('Ymd') . rand(100, 999);
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
                        <th><input type="checkbox" name="select_all" class="select_all"></th>
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
                        <th style="width: 230px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><input type="checkbox" name="select_id" value="<?=$value->id?>" class="select_id"></td>
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
                        <td><?=$value['quote_price']?></td>
                        <td><?=$value->type == 3 ? $value->stock->number : '无限多'?></td>
                        <td><?=$value->type == 3 ? '无' : $value->inquiry->inquiry_datetime?></td>
                        <td><?=$value->type == 3 ? $value->stock->supplier_id : $value->inquiry->supplier_id?></td>
                        <td><?=$value->type == 3 ? ($value->stock->supplier_id ? Supplier::getAllDropDown()[$value->stock->supplier_id] : '') : ($value->inquiry->supplier_id ? Supplier::getAllDropDown()[$value->inquiry->supplier_id] : '') ?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['quote_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td>
                            <a class="btn btn-success btn-xs btn-flat" href="<?=Url::to(['goods/view', 'id' => $value->goods->id])?>"><i class="fa fa-product-hunt"></i> 零件详情</a>
                            <a class="btn btn-info btn-xs btn-flat" href="<?=Url::to(['inquiry/index', 'InquirySearch[good_id]' => $value->goods->id])?>"><i class="fa fa-list"></i> 询价记录</a>
                        </td>
                    </tr>

                <?php endforeach;?>
                <tr>
                    <td colspan="11" style="text-align: right;"><b>金额合计</b></td>
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
            ])->textInput(['readonly' => true]);?>

            <?= $form->field($model, 'order_price')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'remark')->textInput(['readonly' => true]) ?>
        </div>
        <div class="box-footer">
            <?= Html::button('保存最终报价单', [
                    'class' => 'btn btn-info quote_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['order/index']), [
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
            var number = getSelectNumber();
            if (!number) {
                layer.msg('请选择几个选项', {time:1000}, function(){});
                return false;
            }
            var ids = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    ids.push($(element).val());
                }
            });
            var customer_id  = $('#order-customer_id').val();
            if (!customer_id) {
                layer.msg('请选择客户信息', {time:1000}, function(){});
                return false;
            }
            var order_sn     = $('#order-order_sn').val();
            if (!order_sn) {
                layer.msg('请填写订单编号', {time:1000}, function(){});
                return false;
            }
            var description  = $('#order-description').val();
            if (!description) {
                layer.msg('请填写订单描述', {time:1000}, function(){});
                return false;
            }
            var provide_date = $('#order-provide_date').val();
            if (!provide_date) {
                layer.msg('请填写供货日期', {time:1000}, function(){});
                return false;
            }
            var order_price  = $('#order-order_price').val();
            if (!order_price || order_price <= 0) {
                layer.msg('报价金额不能为零', {time:1000}, function(){});
                return false;
            }
            var remark       = $('#order-remark').val();
            if (!remark) {
                layer.msg('请填写备注', {time:1000}, function(){});
                return false;
            }

            $.ajax({
                type:"post",
                url:"?r=order/final-quote",
                data:{ids:ids},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time:1500}, function(){
                            location.replace("?r=order/quote-list");
                        });
                    }
                }
            });

            // $.ajax({
            //     type:"get",
            //     url:"?r=order/submit",
            //     data:{ids:ids, type:type, customer_id:customer_id, order_sn:order_sn, description:description,
            //         provide_date:provide_date, order_price:order_price, remark:remark},
            //     dataType:'JSON',
            //     success:function(res){
            //         if (res && res.code == 200) {
            //             layer.msg(res.msg, {time:1500}, function(){
            //                 location.replace("?r=cart/list&is_order=true");
            //             });
            //         }
            //     }
            // });
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

        //全选
        $('.select_all').click(function (e) {
            if ($(this).prop("checked")) {
                $('.select_id').prop("checked",true);
            } else {
                $('.select_id').prop("checked",false);
            }
        });

        //子选择
        var select_num = $('.select_id').length;
        $('.select_id').click(function (e) {
            if ($(this).prop("checked")) {
                var n = 0;
                $('.select_id').each(function (index, element) {
                    if ($(element).prop("checked")) {
                        n++;
                    }
                });
                if (n == select_num) {
                    $('.select_all').prop("checked",true);
                }
            } else {
                $('.select_all').prop("checked",false);
            }
        });

        function getSelectNumber() {
            var n = 0;
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    n++;
                }
            });
            return n;
        }
    }
</script>
