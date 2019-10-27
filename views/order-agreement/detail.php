<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成采购单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');
//采购商品IDs
$purchaseGoods_ids = ArrayHelper::getColumn($purchaseGoods, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '询价员', '采购员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$model->purchase_sn    = 'B' . date('ymd_') . $number;
$model->agreement_date = substr($orderAgreement->agreement_date, 0, 10);
?>
<style>
    #example2 {
        position: relative;
        clear: both;
        zoom: 1;
        overflow-x: auto;
    }

</style>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_agreement_id="<?=$_GET['id']?>">
                <tr>
                    <th><input type="checkbox" name="select_all" class="select_all"></th>
                    <th>序号</th>
                    <th>零件号</th>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>供应商</th>
                    <th>询价员</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>未税总价</th>
                    <th>含税单价</th>
                    <th>含税总价</th>
                    <th>货期</th>
                    <th>采购单号</th>
                    <th>合同货期</th>
                    <th>合同需求数量</th>
                    <th>采购数量</th>
                    <th>单位</th>
                    <th>使用库存数量</th>
                    <th>库存数量</th>
                    <th>建议库存</th>
                    <th>高储</th>
                    <th>低储</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td><button type="button" class="btn btn-success inquiry_search">搜索</button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="width:100px">
                        <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <select class="form-control" name="admin_id">
                            <option value=""></option>
                            <?php foreach ($admins as $key => $value) :?>
                                <option value="<?=$key?>" <?=isset($_GET['admin_id']) ? ($_GET['admin_id'] === (string)$key ? 'selected' : '') : ''?>><?=$value?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
            <tr class="order_agreement_list">
                <?php
                    $checkbox = true;
                    $order_purchase_sn = '';
                    $purchase_number = 0;
                    if (isset($purchaseGoods[$item->goods_id])) {
                        $purchaseGoodsList = $purchaseGoods[$item->goods_id];
                        foreach ($purchaseGoodsList as $k => $v) {
                            if ($v['serial'] == $item->serial && $v['goods_id'] == $item->goods_id) {
                                $checkbox           = false;
                                $order_purchase_sn  = $v['order_purchase_sn'];
                                $purchase_number    = $v['fixed_number'];
                            }
                        }
                    }
                ?>
                <td>
                    <?=$checkbox ? "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-agreement_goods_id={$item->id} value={$item->goods_id} class='select_id'>" : ""?>
                </td>
                <td><?=$item->serial?></td>
                <td><?=Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                <td><?=$item->goods->description?></td>
                <td><?=$item->goods->description_en?></td>
                <td><?=$item->goods->original_company?></td>
                <td><?=$item->goods->original_company_remark?></td>
                <td class="supplier_name"><?=$item->inquiry->supplier->name?></td>
                <td><?=Admin::findOne($item->inquiry_admin_id)->username?></td>
                <td><?=$item->tax_rate?></td>
                <td class="price"><?=$item->price?></td>
                <td class="all_price"><?=$item->all_price?></td>
                <td class="tax_price"><?=$item->tax_price?></td>
                <td class="all_tax_price"><?=$item->all_tax_price?></td>
                <td class="delivery_time"><?=$item->delivery_time?></td>
                <td><?=$order_purchase_sn?></td>
                <td class="quote_delivery_time"><?=$item->quote_delivery_time?></td>
                <td class="oldNumber"><?=$item->number?></td>
                <td class="afterNumber">
                    <input type="number" size="4" class="number" min="1" style="width: 50px;" value="<?=$purchase_number ? $purchase_number : $item->number?>">
                </td>
                <td><?=$item->goods->unit?></td>
                <td class="use_stock"></td>
                <td class="stock_number"><?=$item->stock ? $item->stock->number : 0?></td>
                <td><?=$item->stock ? $item->stock->suggest_number : 0?></td>
                <td><?=$item->stock ? $item->stock->high_number : 0?></td>
                <td><?=$item->stock ? $item->stock->low_number : 0?></td>
            </tr>
            <?php endforeach;?>
                <tr style="background-color: #acccb9">
                    <td colspan="14" rowspan="2">汇总统计</td>
                    <td>采购含税总价</td>
                    <td>最长货期</td>
                    <td></td>
                    <td>合同最长货期</td>
                    <td colspan="8"></td>
                </tr>
                <tr style="background-color: #acccb9">
                    <td class="purchase_all_price"></td>
                    <td class="mostLongTime"></td>
                    <td></td>
                    <td class="quote_mostLongTime"></td>
                    <td colspan="8"></td>
                </tr>
            </tbody>
        </table>
        <?= $form->field($model, 'purchase_sn')->textInput()->label('采购订单号') ?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择采购员') ?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->label('收入合同交货时间');?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存采购单', [
                'class' => 'btn btn-success purchase_save',
                'name'  => 'submit-button']
        )?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();

        function init(){
            if (!$('.select_id').length) {
                $('.select_all').hide();
                $('.purchase_save').hide();
                $('.field-orderpurchase-purchase_sn').hide();
                $('.field-orderpurchase-admin_id').hide();
                $('.field-orderpurchase-agreement_date').hide();
            }
            var sta_all_price       = 0;
            var sta_all_tax_price   = 0;
            var mostLongTime        = 0;
            var purchase_price      = 0;
            var purchase_all_price  = 0;
            var quote_mostLongTime  = 0;
            $('.order_agreement_list').each(function (i, e) {
                var price           = $(e).find('.price').text();
                var tax_price       = $(e).find('.tax_price').text();
                var number          = $(e).find('.oldNumber').text();
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                var purchase_number = $(e).find('.number').val();

                sta_all_price += parseFloat(price * number);
                sta_all_tax_price += parseFloat(tax_price * number);
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                $(e).find('.all_price').text(parseFloat(price * purchase_number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * purchase_number).toFixed(2));

                var all_price     = parseFloat(price * purchase_number);
                var all_tax_price = parseFloat(tax_price* purchase_number);

                purchase_price     += parseFloat(all_price);
                purchase_all_price += parseFloat(all_tax_price);

                //默认使用库存数量
                var use_number = number - purchase_number;
                if (use_number < 0) {
                    use_number = 0;
                }
                $(e).find('.use_stock').text(use_number);

                //合同货期
                var quote_delivery_time = parseFloat($(e).find('.quote_delivery_time').text());
                if (quote_delivery_time > quote_mostLongTime) {
                    quote_mostLongTime = quote_delivery_time;
                }
            });
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.mostLongTime').text(mostLongTime);
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));
            $('.quote_mostLongTime').text(quote_mostLongTime.toFixed(2));
        }

        //全选
        $('.select_all').click(function (e) {
            $('.select_id').prop("checked",$(this).prop("checked"));
        });

        //子选择
        $('.select_id').on('click',function (e) {
            if ($('.select_id').length == $('.select_id:checked').length) {
                $('.select_all').prop("checked",true);
            } else {
                $('.select_all').prop("checked",false);
            }
        });

        //输入数量
        $(".number").bind('input propertychange', function (e) {

            var number = $(this).val();
            var a = number.replace(/[^\d]/g,'');
            $(this).val(a);

            var price = $(this).parent().parent().find('.price').text();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));

            //默认使用库存数量
            var agreement_number = parseFloat($(this).parent().parent().find('.oldNumber').text());
            var use_number = agreement_number - number;
            if (use_number < 0) {
                use_number = 0;
            }
            var stock_number = parseFloat($(this).parent().parent().find('.stock_number').text());
            // if (use_number > stock_number) {
            //     layer.msg('使用库存数量不能比库存大', {time:2000});
            //     $(this).val(agreement_number);
            //     return false;
            // }

            $(this).parent().parent().find('.use_stock').text(use_number);

            var purchase_price     = 0;
            var purchase_all_price = 0;
            $('.order_agreement_list').each(function (i, e) {
                var all_price       = $(e).find('.all_price').text();
                var all_tax_price   = $(e).find('.all_tax_price').text();
                purchase_price      += parseFloat(all_price);
                purchase_all_price  += parseFloat(all_tax_price);
            });
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));
        });

        //保存
        $('.purchase_save').click(function (e) {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }

            var goods_info              = [];
            var number_flag             = false;
            var supplier_flag           = false;
            var flag_stock              = false;
            var purchase_number_flag    = false;
            var supplier_name           = '';
            $('.select_id').each(function (index, element) {
                var item = {};
                if ($(element).prop("checked")) {
                    var s_name = $(element).parent().parent().find('.supplier_name').text();
                    if (!supplier_name) {
                        supplier_name = s_name;
                    } else {
                        if (supplier_name != s_name) {
                            supplier_flag = true;
                        }
                    }
                    if (!$(element).parent().parent().find('.number').val()){
                        number_flag  = true;
                    }

                    var purchase_number     = parseFloat($(element).parent().parent().find('.number').val());
                    var stock_number        = parseFloat($(element).parent().parent().find('.stock_number').text());
                    var old_number          = parseFloat($(element).parent().parent().find('.oldNumber').text());
                    var use_stock           = parseFloat($(element).parent().parent().find('.use_stock').text());

                    if (purchase_number == 0 && old_number > stock_number) {
                        flag_stock = true;
                    }
                    if (use_stock > stock_number) {
                        purchase_number_flag = true;
                    }

                    item.agreement_goods_id = $(element).data('agreement_goods_id');
                    item.goods_id           = $(element).val();
                    item.number             = $(element).parent().parent().find('.number').val();
                    item.type               = $(element).data('type');
                    item.relevance_id       = $(element).data('relevance_id');
                    item.delivery_time      = $(element).parent().parent().find('.delivery_time').text();
                    goods_info.push(item);
                }
            });

            // if (supplier_flag) {
            //     layer.msg('一个支出合同不能有多个供应商', {time:2000});
            //     return false;
            // }

            if (purchase_number_flag) {
                layer.msg('使用库存数量不能比库存大', {time:2000});
                return false;
            }

            if (flag_stock) {
                layer.msg('需求数量大于库存数量时，采购数量不能为0', {time:2000});
                return false;
            }

            if (number_flag) {
                layer.msg('请给选中的行输入数量', {time:2000});
                return false;
            }
            var purchase_sn = $('#orderagreement-purchase_sn').val();
            if (!purchase_sn) {
                layer.msg('请输入采购单号', {time:2000});
                return false;
            }

            var admin_id = $('#orderagreement-admin_id').val();
            if (!admin_id) {
                layer.msg('请选择采购员', {time:2000});
                return false;
            }

            var agreement_date = $('#orderagreement-agreement_date').val();
            if (!agreement_date) {
                layer.msg('请输入收入合同交货日期', {time:2000});
                return false;
            }

            var order_agreement_id = $('.data').data('order_agreement_id');

            $.ajax({
                type:"post",
                url:'?r=order-purchase/save-order',
                data:{order_agreement_id:order_agreement_id, purchase_sn:purchase_sn, agreement_date:agreement_date, admin_id:admin_id, goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        //搜索功能
        $('.inquiry_search').click(function (e) {
            var search = $('#w3-filters').find('td input');
            var parameter = '';
            search.each(function (i, e) {
                switch ($(e).attr('name')) {
                    case 'goods_number':
                        parameter += '&goods_number=' + $(e).val();
                        break;
                    case 'goods_number_b':
                        parameter += '&goods_number_b=' + $(e).val();
                        break;
                    case 'original_company':
                        parameter += '&original_company=' + $(e).val();
                        break;
                    default:
                        break;
                }
            });
            var searchOption = $('#w3-filters').find('td select');
            searchOption.each(function (i, e) {
                switch ($(e).attr('name')) {
                    case 'admin_id':
                        parameter += '&admin_id=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order-agreement/detail&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
    });
</script>
