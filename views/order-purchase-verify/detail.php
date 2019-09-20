<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '采购单审核详情';
$this->params['breadcrumbs'][] = $this->title;



$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_payment_id="<?=$_GET['id']?>">
                <tr>
                    <th>序号</th>
                    <th>零件号</th>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>供应商</th>
                    <th>货期(周)</th>
                    <th>税率</th>
                    <th>含税单价</th>
                    <th>含税总价</th>
                    <th>数量</th>
                    <th style="background-color: darkgrey">修改后供应商</th>
                    <th style="background-color: darkgrey">修改后货期(周)</th>
                    <th style="background-color: darkgrey">修改后未税单价</th>
                    <th style="background-color: darkgrey">修改后含税单价</th>
                    <th style="background-color: darkgrey">修改后未税总价</th>
                    <th style="background-color: darkgrey">修改后含税总价</th>
                    <th style="background-color: darkgrey">修改后数量</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_payment_list" data-payment_goods_id="<?=$item->id?>">
                    <td><?=$item->serial?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td class="before_supplier"><?=isset($item->beforeSupplier) ? $item->beforeSupplier->name : ''?></td>
                    <td class="before_delivery_time"><?=$item->before_delivery_time?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td><?=$item->tax_price?></td>
                    <td class="before_tax_price"><?=$item->all_tax_price?></td>
                    <td><?=$item->number?></td>
                    <td class="supplier"><?=$item->supplier->name?></td>
                    <td class="delivery_time"><?=$item->delivery_time?></td>
                    <td class="price"><?=$item->fixed_price?></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td class="all_price"><?=$item->fixed_all_price?></td>
                    <td class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <td class="afterNumber"><?=$item->fixed_number?></td>
                </tr>
            <?php endforeach;?>

            <tr style="background-color: #acccb9">
                <td colspan="15" rowspan="2">汇总统计</td>
                <td>修改前含税总价</td>
                <td>支出未税总价</td>
                <td>支出含税总价</td>
                <td rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="sta_all_tax_price"></td>
                <td class="sta_quote_all_price"></td>
                <td class="sta_quote_all_tax_price"></td>
            </tr>

            </tbody>
        </table>

        <?= $form->field($model, 'apply_reason')->textInput(['readonly' => true])->label('采购申请备注'); ?>

        <?php if (!$model->is_verify):?>
            <?= $form->field($model, 'reason')->textInput(); ?>
        <?php endif;?>
    </div>
    <?php if (!$model->is_verify):?>
        <div class="box-footer">
            <?= Html::button('审核通过', [
                    'class' => 'btn btn-success verify_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::button('驳回', [
                'class' => 'btn btn-warning btn-flat verify_reject',
            ])?>
        </div>
    <?php endif;?>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    init();
    function init() {
        var sta_all_tax_price   = 0;
        var sta_quote_all_price = 0;
        var sta_quote_all_tax_price = 0;
        $('.order_payment_list').each(function (i, e) {
            var all_price = $(e).find('.all_price').text();
            var all_tax_price = $(e).find('.all_tax_price').text();
            if (all_price) {
                sta_quote_all_price += parseFloat(all_price);
            }
            if (all_tax_price) {
                sta_quote_all_tax_price += parseFloat(all_tax_price);
            }
            var before_tax_price = parseFloat($(e).find('.before_tax_price').text());
            if (before_tax_price) {
                sta_all_tax_price += before_tax_price;
            }
            var supplier = $(e).find('.supplier').text();
            var before_supplier = $(e).find('.before_supplier').text();
            if (supplier !== before_supplier) {
                $(e).find('.supplier').css({"background": "#58a95d"});
            }

            var delivery_time = parseFloat($(e).find('.delivery_time').text());
            var before_delivery_time = parseFloat($(e).find('.before_delivery_time').text());
            if (delivery_time !== before_delivery_time) {
                $(e).find('.delivery_time').css({"background": "#58a95d"});
            }
        });
        $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
        $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
    }

    $('.verify_save').click(function (e) {
        verify(true);
    });

    $('.verify_reject').click(function (e) {
        verify(false);
    });

    function verify(action) {
        var order_payment_id = $('.data').data('order_payment_id');
        var goods_info = [];
        $('.order_payment_list').each(function (i, e) {
            var payment_goods_id = $(e).data('payment_goods_id');
            goods_info.push(payment_goods_id);
        });

        if (action) {
            urls = '?r=order-purchase-verify/verify-pass';
        } else {
            var reason = $('#orderpayment-reason').val();
            if (!reason) {
                layer.msg('请填写驳回原因', {time:2000});
                return false;
            }
            urls = '?r=order-purchase-verify/verify-reject';
        }

        //ajax审核
        $.ajax({
            type:"post",
            url:urls,
            data:{order_payment_id:order_payment_id, goods_info:goods_info, reason:reason},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    window.location.href = '?r=order-purchase-verify';
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>
