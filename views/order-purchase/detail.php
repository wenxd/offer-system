<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use \app\models\Supplier;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '采购单详情';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->agreement_date) {
    $model->agreement_date = date('Y-m-d');
}

$model->payment_sn = 'Z' . date('ymd_') . '_' . $number;

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;

//显示按钮开关
$i = 0;

$model->delivery_date = date('Y-m-d');
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-header">
        <?= Html::a('导出', Url::to(['download', 'id' => $_GET['id']]), [
            'data-pjax' => '0',
            'class'     => 'btn btn-primary btn-flat',
        ])?>
    </div>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_purchase_id="<?=$_GET['id']?>">
                <tr>
                    <th><input type="checkbox" name="select_all" class="select_all"></th>
                    <th>序号</th>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <th>零件号</th>
                    <?php endif;?>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>原厂家</th>
                    <th>供应商</th>
                    <th>单位</th>
                    <th>采购数量</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                    <th>货期(周)</th>
                    <th width="80px;">是否入库</th>
                    <th>税率</th>
                    <th>合同需求数量</th>
                    <th>使用库存数</th>
                    <th>审核状态</th>
                    <th>驳回原因</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td><button type="button" class="btn btn-success inquiry_search">搜索</button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                    </td>
                    <td>
                        <select class="form-control" name="supplier_id">
                            <option value=""></option>
                            <?php foreach ($supplier as $value) :?>
                                <option value="<?=$value->id?>" <?=isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : ''?>><?=$value->name?></option>
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
                    <td>
                        <select class="form-control" name="is_stock">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($purchaseGoods as $item):?>
                <tr class="order_purchase_list">
                    <?php
                        $str = "<input type='checkbox' name='select_id' value={$item->goods_id} class='select_id'>";
                        //是否生成过支出单
                        $open = true;
                        $purchaseGoodsIds = ArrayHelper::getColumn($paymentGoods, 'purchase_goods_id');
                        if (in_array($item->id, $purchaseGoodsIds)) {
                            $open = false;
                            $i++;
                        }
                    ?>
                    <td>
                        <?=$open ? $str : ''?>
                    </td>
                    <td class="purchase_detail" data-purchase_goods_id="<?=$item->id?>" >
                        <?=$item->serial?>
                    </td>
                    <?php if(!in_array($userId, $adminIds)):?>
                        <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?><?=Html::a(' 采购记录', Url::to(['temp-payment-goods/temp', 'id' => $item->id]))?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td class="supplier_name"><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->goods->unit?></td>
                    <td class="afterNumber">
                        <input type="number" size="4" class="number" min="1" style="width: 50px;" value="<?=$item->fixed_number?>">
                    </td>
                    <td class="price"><input type="text" value="<?=$item->fixed_price?>" style="width: 100px;"></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td class="delivery_time"><input type="text" value="<?=$item->delivery_time?>" style="width: 100px;"></td>
                    <td><?=$item::$stock[$item->is_stock]?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td><?=$item->number?></td>
                    <td><?=($item->number - $item->fixed_number) >= 0 ? $item->number - $item->fixed_number : 0?></td>
                    <td><?php
                            if ($item->apply_status == 0) {
                                $status = '无';
                            } elseif ($item->apply_status == 1) {
                                $status = '审核中';
                            } elseif ($item->apply_status == 2) {
                                $status = '审核通过';
                            } elseif ($item->apply_status == 3) {
                                $status = '被驳回';
                            }
                            echo $status;
                        ?>
                    </td>
                    <td><?=$item->reason?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('采购员') ?>

        <?= $form->field($model, 'end_date')->textInput(['readonly' => 'true']); ?>

        <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::classname(), [
            'data' => Supplier::getCreateDropDown(),
            'options' => ['placeholder' => '选择供应商'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('供应商')?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->label('支出合同签订时间');?>

        <?= $form->field($model, 'delivery_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->label('支出合同交货时间');?>

        <?= $form->field($model, 'apply_reason')->textInput()->label('申请备注'); ?>

        <?php if (!$model->is_complete):?>
            <?= $form->field($model, 'payment_sn')->textInput(); ?>
        <?php endif;?>

    </div>
    <?php if (!$model->is_complete):?>
        <div class="box-footer">
            <?= Html::button('提交支出申请', [
                    'class' => 'btn btn-success payment_save',
                    'name'  => 'submit-button']
            )?>
        </div>
    <?php endif;?>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
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

        init();

        function init(){
            $('.order_purchase_list').each(function (i, e) {
                var price     = $(e).find('.price input').val();
                var tax_price = $(e).find('.tax_price').text();
                var number    = $(e).find('.afterNumber input').val();
                $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            });
        }

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
                    case 'supplier_id':
                        parameter += '&supplier_id=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order-purchase/detail&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });

        //输入未税单价
        $(".price input").bind('input propertychange', function (e) {
            var tax       = $(this).parent().parent().find('.tax').text();
            var price     = $(this).val();
            var tax_price = parseFloat(price * (1 + tax/100)).toFixed(2);
            var number    = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.tax_price').text(tax_price);
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
        });

        //输入数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time:2000});
                return false;
            }
            var a = number.replace(/[^\d]/g,'');
            $(this).val(a);

            var price     = $(this).parent().parent().find('.price input').val();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
        });

        $('#orderpurchase-supplier_id').change(function(e){
            var supplier_id = $(this).val();
            $.ajax({
                type:"get",
                url:'?r=supplier/detail',
                data:{id:supplier_id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        var payment_sn = $('#orderpurchase-payment_sn').val();
                        payment_sn = payment_sn.split('_');
                        var first = payment_sn[0];
                        var end   = payment_sn[2];
                        var after_payment_sn = first + '_' + res.data.short_name + '_' + end;
                        $('#orderpurchase-payment_sn').val(after_payment_sn);
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        //保存支出合同
        $(".payment_save").click(function () {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }
            var goods_info          = [];
            var supplier_flag       = false;
            var supplier_name       = '';
            var long_delivery_time  = 0;
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var s_name = $(element).parent().parent().find('.supplier_name').text();
                    if (!supplier_name) {
                        supplier_name = s_name;
                    } else {
                        if (supplier_name != s_name) {
                            supplier_flag = true;
                        }
                    }
                    var item = {};
                    item.purchase_goods_id = $(element).parent().parent().find('.purchase_detail').data('purchase_goods_id');
                    item.goods_id          = $(element).val();
                    item.fix_price         = $(element).parent().parent().find('.price input').val();
                    item.fix_number        = $(element).parent().parent().find('.afterNumber input').val();
                    var delivery_time = parseFloat($(element).parent().parent().find('.delivery_time input').val());
                    if (delivery_time > long_delivery_time) {
                        long_delivery_time = delivery_time;
                    }
                    item.delivery_time     = delivery_time;
                    goods_info.push(item);
                }
            });

            // if (supplier_flag) {
            //     layer.msg('一个支出合同不能有多个供应商', {time:2000});
            //     return false;
            // }

            var order_purchase_id = $('.data').data('order_purchase_id');
            var admin_id          = $('#orderpurchase-admin_id').val();
            var end_date          = $('#orderpurchase-end_date').val();
            var payment_sn        = $('#orderpurchase-payment_sn').val();
            var supplier_id       = $('#orderpurchase-supplier_id option:selected').val();
            var apply_reason      = $('#orderpurchase-apply_reason').val();
            var agreement_date    = $('#orderpurchase-agreement_date').val();
            //合同交货日期
            var delivery_date     = $('#orderpurchase-delivery_date').val();

            //创建支出合同
            $.ajax({
                type:"post",
                url:'?r=order-purchase-verify/save-order',
                data:{order_purchase_id:order_purchase_id, admin_id:admin_id, end_date:end_date, payment_sn:payment_sn,
                    goods_info:goods_info, long_delivery_time:long_delivery_time, supplier_id:supplier_id,
                    apply_reason:apply_reason, agreement_date:agreement_date, delivery_date:delivery_date},
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
    });
</script>
