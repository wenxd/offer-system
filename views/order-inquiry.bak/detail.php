<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\QuoteRecord;
use yii\widgets\ActiveForm;
$this->title = '询价单详情';
$this->params['breadcrumbs'][] = $this->title;

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
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="select_all"></th>
                        <th>零件ID</th>
                        <th>厂家号</th>
                        <th>最新</th>
                        <th>优选</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>商品类型</th>
                        <th>询价时间</th>
                        <th>税率</th>
                        <th>未税单价</th>
                        <th>含税单价</th>
                        <th>交货期</th>
                        <th>询价备注</th>
                        <th>金额</th>
                        <th>询价</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key => $value):?>
                    <tr>
                        <td><input type="checkbox" class="select_id" value="<?=$value->id?>"></td>
                        <td><?=$value->goods_id?></td>
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
                        <td><?=$value->type == 3 ? $value->stock->supplier_id : $value->inquiry->supplier_id?></td>
                        <td><?=$value->type == 3 ? Supplier::getAllDropDown()[$value->stock->supplier_id] : Supplier::getAllDropDown()[$value->inquiry->supplier_id]?></td>
                        <td><?=$value->type == 3 ? '库存商品' : '询价商品'?></td>
                        <td><?=$value->type == 3 ? '无' : $value->inquiry->inquiry_datetime?></td>
                        <td><?=$value->type == 3 ? $value->stock->tax_rate : $value->inquiry->tax_rate?></td>
                        <td class="price"><?=$value->type == 3 ? $value->stock->price : $value->inquiry->price?></td>
                        <td><?=$value->type == 3 ? $value->stock->tax_price : $value->inquiry->tax_price?></td>
                        <td><?=$value->offer_date?></td>
                        <td><?=$value->remark?></td>
                        <td class="money">
                            <?=number_format($value['quote_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td><?=QuoteRecord::$status[$value->status]?></td>
                        <td>
                            <?php if (!$value->status):?>
                            <a class="btn btn-primary btn-xs btn-flat" href="<?=Url::to(['quote-record/update', 'id' => $value->id])?>"><i class="fa fa-inbox"></i> 询价</a>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="14" style="text-align: right;"><b>金额合计</b></td>
                    <td class="all_money"></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
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
    </div>
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    window.onload = function() {
        var allMoney = 0;
        $('.money').each(function (index, element){
            allMoney += parseFloat(element.innerText)
        });
        $('.all_money').html(allMoney);

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

        //生成最终报价单
        $('.quote_save').click(function () {
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
        });


    }
</script>
