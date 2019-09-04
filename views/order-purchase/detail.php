<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '采购单详情';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->agreement_date) {
    $model->agreement_date = substr($model->orderAgreement->agreement_date, 0, 10);
}

$model->payment_sn = 'Z' . date('ymd_') . '_' . $number;

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;

//显示按钮开关
$i = 0;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
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
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>技术备注</th>
                    <th>加工</th>
                    <th>特制</th>
                    <th>铭牌</th>
                    <th>供应商</th>
                    <th>供应商缩写</th>
                    <th width="80px;">是否入库</th>
                    <th>货期(周)</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含率单价</th>
                    <th>未税总价</th>
                    <th>含率总价</th>
                    <th>数量</th>
                    <th>审核状态</th>
                    <th>驳回原因</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td><button type="button" class="btn btn-success inquiry_search">搜索</button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <select class="form-control" name="supplier_id">
                            <option value=""></option>
                            <?php foreach ($supplier as $value) :?>
                                <option value="<?=$value->id?>" <?=isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : ''?>><?=$value->name?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
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
                    <td></td>
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
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->goods->technique_remark?></td>
                    <td><?=Goods::$process[$item->goods->is_process]?></td>
                    <td><?=Goods::$special[$item->goods->is_special]?></td>
                    <td><?=Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->inquiry->supplier->short_name?></td>
                    <td><?=$item::$stock[$item->is_stock]?></td>
                    <td><?=$item->inquiry->delivery_time?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td class="price"><input type="text" value="<?=$item->fixed_price?>" style="width: 100px;"></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td class="afterNumber">
                        <input type="number" size="4" class="number" min="1" style="width: 50px;" value="<?=$item->fixed_number?>">
                    </td>
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

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->hiddenInput()->label(false);?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('采购员') ?>

        <?= $form->field($model, 'end_date')->textInput(['readonly' => 'true']); ?>

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

        $(".payment_save").click(function () {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }
            var goods_info = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var item = {};
                    item.purchase_goods_id = $(element).parent().parent().find('.purchase_detail').data('purchase_goods_id');
                    item.goods_id          = $(element).val();
                    item.fix_price         = $(element).parent().parent().find('.price input').val();
                    item.fix_number        = $(element).parent().parent().find('.afterNumber input').val();
                    goods_info.push(item);
                }
            });

            var order_purchase_id = $('.data').data('order_purchase_id');
            var admin_id = $('#orderpurchase-admin_id').val();
            var end_date = $('#orderpurchase-end_date').val();
            var payment_sn = $('#orderpurchase-payment_sn').val();

            //创建审核
            $.ajax({
                type:"post",
                url:'?r=order-purchase-verify/save-order',
                data:{order_purchase_id:order_purchase_id, admin_id:admin_id, end_date:end_date, payment_sn:payment_sn, goods_info:goods_info},
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
