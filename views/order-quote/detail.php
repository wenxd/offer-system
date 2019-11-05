<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成收入合同';
$this->params['breadcrumbs'][] = $this->title;

$model->agreement_date = date('Y-m-d');
$model->sign_date = date('Y-m-d');
$customer_name = $order->customer ? $order->customer->short_name : '';
$model->agreement_sn = 'S' . date('ymd_') . $customer_name . '_' . $number;

$use_admin = AuthAssignment::find()->where(['item_name' => '报价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;
$is_show = in_array($userId, $adminIds);
?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 2000px;">
            <thead class="data" data-order_quote_id="<?=$_GET['id']?>">
            <tr>
                <th>零件号</th>
                <?php if(!in_array($userId, $adminIds)):?>
                    <th>厂家号</th>
                <?php endif;?>
                <th style="width: 200px;">中文描述</th>
                <th style="width: 200px;">英文描述</th>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <?php endif;?>
                <th>单位</th>
                <th>收入合同货期</th>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>供应商</th>
                <?php endif;?>
                <th>税率</th>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>发行含税单价</th>
                <?php endif;?>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>含税总价</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quoteGoods as $item):?>
                <tr class="order_quote_list">
                    <td><?=$item->goods->goods_number?></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                        <td><?=$item->goods->goods_number_b?></td>
                    <?php endif;?>
                    <td class="goods_id" data-goods_id="<?=$item->goods_id?>" data-goods_type="<?=$item->type?>"
                        data-relevance_id="<?=$item->relevance_id?>" data-quote_goods_id="<?=$item->id?>">
                        <?=$item->goods->description?>
                    </td>
                    <td><?=$item->goods->description_en?></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <?php endif;?>
                    <td><?=$item->goods->unit?></td>
                    <td class="delivery_time"><input type="text" value="<?=$item->quote_delivery_time?>" style="width: 80px;"></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <?php endif;?>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=number_format($item->goods->publish_price * (1 + $item->tax_rate/100), 2, '.', '')?></td>
                    <?php endif;?>
                    <td class="price"><input type="text" class="change_price" value="<?=$item->quote_price?>"  style="width: 80px;"></td>
                    <td class="tax_price"><?=$item->quote_tax_price?></td>
                    <td class="all_tax_price"></td>
                    <td class="afterNumber">
                        <input type="number" class="number" min="1" value="<?=$item->number?>"  style="width: 80px;">
                    </td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= $is_show ? 4 : 7?>" rowspan="2">汇总统计</td>
                <td>最长合同货期</td>
                <td colspan="<?= $is_show ? 3 : 5?>" rowspan="2"></td>
                <td>合同总价</td>
                <td></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="mostLongTime"></td>
                <td class="sta_all_tax_price"></td>
                <td></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'quote_delivery_time')->textInput()->label('统一报价货期') ?>

        <?= $form->field($model, 'agreement_sn')->textInput() ?>

        <?= $form->field($model, 'sign_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ]);?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ]);?>

    </div>
    <?php if ($orderQuote->is_quote):?>
    <div class="box-footer">
        <?= Html::button('生成收入合同', [
                'class' => 'btn btn-success quote_complete',
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
        init();
        function init(){
            var sta_all_tax_price = 0;
            var mostLongTime      = 0;
            $('.order_quote_list').each(function (i, e) {
                var price           = parseFloat($(e).find('.change_price').val());
                var tax_price       = parseFloat($(e).find('.tax_price').text());
                var number          = parseFloat($(e).find('.afterNumber').find('input').val());
                var all_tax_price   = parseFloat(tax_price * number);
                $(e).find('.all_tax_price').text(all_tax_price.toFixed(2));
                if (all_tax_price) {
                    sta_all_tax_price += all_tax_price;
                }
                var delivery_time = parseFloat($(e).find('.delivery_time input').val());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }
            });
            var date = '<?=$model->agreement_date?>';
            $('#orderpurchase-agreement_date').val(date);
            $('.mostLongTime').text(mostLongTime);
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        }

        //改变数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            $(this).val(number);
            var price     = parseFloat($(this).parent().parent().find('.change_price').val());
            var tax_price = parseFloat($(this).parent().parent().find('.tax_price').text());
            var all_tax_price = parseFloat(tax_price * number);
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(all_tax_price.toFixed(2));

            //计算总价
            statPrice();
        });

        //修改单价
        $('.change_price').bind('input propertychange', function (e) {
            var price = $(this).val();
            var tax   = $(this).parent().parent().find('.tax').text();
            var tax_price = (price * (1 + tax / 100));
            var number    = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.tax_price').text(parseFloat(tax_price).toFixed(2));

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            statPrice();
        });

        //计算总价
        function statPrice() {
            var sta_all_tax_price = 0;
            $('.order_quote_list').each(function (i, e) {
                var all_tax_price = parseFloat($(e).find('.all_tax_price').text());
                if (all_tax_price) {
                    sta_all_tax_price += all_tax_price;
                }
            });
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        }

        //统一修改货期
        $('#orderagreement-quote_delivery_time').bind('input propertychange', function (e) {
            var quote_delivery_time = $(this).val();
            $('.order_quote_list').each(function (i, ele) {
                $(ele).find('.delivery_time input').val(quote_delivery_time);
            });
            $('.mostLongTime').text(quote_delivery_time);
        });

        //修改收入合同货期
        $('.delivery_time input').bind('input propertychange', function (e) {
            var quote_delivery_time = $(this).val();
            $(this).val(quote_delivery_time);
            var most_delivery_time = 0;
            $('.order_quote_list').each(function (i, ele) {
                var delivery_time = parseFloat($(ele).find('.delivery_time input').val());
                if (delivery_time > most_delivery_time) {
                    most_delivery_time = delivery_time;
                }
            });
            $('.mostLongTime').text(most_delivery_time);
        });

        $('.quote_complete').click(function (e) {
            var goods_info = [];

            $('.order_quote_list').each(function (i, ele) {
                var item = {};
                item.quote_goods_id  = $(ele).find('.goods_id').data('quote_goods_id');
                item.goods_id        = $(ele).find('.goods_id').data('goods_id');
                item.type            = $(ele).find('.goods_id').data('goods_type');
                item.relevance_id    = $(ele).find('.goods_id').data('relevance_id');
                item.number          = $(ele).find('.number').val();
                item.price           = $(ele).find('.change_price').val();
                item.tax_price       = $(ele).find('.tax_price').text();
                item.delivery_time   = $(ele).find('.delivery_time input').val();
                goods_info.push(item);

            });

            var agreement_sn = $('#orderagreement-agreement_sn').val();
            if (!agreement_sn) {
                layer.msg('请输入合同号', {time:2000});
                return false;
            }

            var sign_date = $('#orderagreement-sign_date').val();
            if (!sign_date) {
                layer.msg('请输入合同签订日期', {time:2000});
                return false;
            }

            var agreement_date = $('#orderagreement-agreement_date').val();
            if (!agreement_date) {
                layer.msg('请输入合同交货日期', {time:2000});
                return false;
            }

            // var admin_id = $('#orderagreement-admin_id').val();
            // if (!admin_id) {
            //     layer.msg('请选择询价员', {time:2000});
            //     return false;
            // }

            var id = $('.data').data('order_quote_id');
            $.ajax({
                type:"post",
                url:'?r=order-quote/create-agreement',
                data:{id:id, agreement_sn:agreement_sn, sign_date:sign_date, agreement_date:agreement_date, goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-quote/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
