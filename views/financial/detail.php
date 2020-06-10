<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\SystemConfig;
use yii\widgets\DetailView;

$this->title = '待付款订单详情';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '财务'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$stock_goods_ids = ArrayHelper::getColumn($stockLog, 'goods_id');
$userId   = Yii::$app->user->identity->id;

$isShow = in_array($userId, $adminIds);

$payment_ratio = SystemConfig::find()->select('value')->where([
        'title' => SystemConfig::TITLE_PAYMENT_RATIO
])->scalar();

if ($model->payment_ratio == '0.00') {
    $model->payment_ratio = $payment_ratio;
    $model->price         = $payment_ratio/100 * $orderPayment->payment_price;
}
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_payment_id="<?=$_GET['id']?>">
            <tr>
                <?php if (!$isShow):?>
                <th>零件号</th>
                <?php endif;?>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>单位</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期(周)</th>
                <th>未税总价</th>
                <th>含税总价</th>
                <th>数量</th>
                <th>入库</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_final_list">
                    <td><?=$item->goods->goods_number?></td>
                    <?php if (!$isShow):?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <?php endif;?>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->supplier->name?></td>
                    <td><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->fixed_price?></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td class="delivery_time"><?=$item->delivery_time?></td>
                    <td class="all_price"><?=$item->fixed_all_price?></td>
                    <td class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <td class="number"><?=$item->fixed_number?></td>
                    <td><?=in_array($item->goods_id, $stock_goods_ids) ? '是' : '否'?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= $isShow ? 9 : 10 ?>" rowspan="2">汇总统计</td>
                <td>最长货期</td>
                <td></td>
                <td>合计</td>
                <td colspan="2" rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="mostLongTime"></td>
                <td></td>
                <td class="sta_all_tax_price"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'financial_remark')->textInput(['maxlength' => true]) ?>

        <div class="customer-view">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'stock_at',
                    [
                        'attribute'      => 'payment_ratio',
                        'contentOptions' => ['class' => 'payment_ratio'],
                    ],
                    'advancecharge_at',
                    'payment_at',
                    'bill_at',
                ],
            ]) ?>
        </div>
        <?php if(!$model->is_advancecharge):?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true])->label('预付款金额') ?>
        <?php endif;?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存备注', [
                'class' => 'btn btn-primary save_remark',
                'name'  => 'submit-button']
        )?>
        <?php if(!$model->is_advancecharge):?>
        <?= Html::button('预付款完成', [
                'class' => 'btn btn-info rimary save_advance',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_payment):?>
        <?= Html::button('全单付款完成', [
                'class' => 'btn btn-info save_payment',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_bill):?>
        <?= Html::button('收到发票', [
                'class' => 'btn btn-info save_bill',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();

        function init() {

            var sta_price           = 0;
            var sta_tax_price       = 0;
            var mostLongTime        = 0;
            var sta_all_price       = 0;
            var sta_all_tax_price   = 0;

            $('.order_final_list').each(function (i, e) {
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                var price       = $(e).find('.price').text();
                if (price) {
                    sta_price += parseFloat(price);
                }

                var tax_price   = $(e).find('.tax_price').text();
                if (tax_price) {
                    sta_tax_price      += parseFloat(tax_price);
                }


                var all_price   = $(e).find('.all_price').text();
                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }

                var all_tax_price   = $(e).find('.all_tax_price').text();
                if (all_tax_price) {
                    sta_all_tax_price      += parseFloat(all_tax_price);
                }
            });

            $('.mostLongTime').text(mostLongTime);
            $('.sta_price').text(sta_price.toFixed(2));
            $('.sta_tax_price').text(sta_tax_price.toFixed(2));
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        }

        //动态修改预付款
        $('#orderpayment-price').bind('input propertychange', function (e) {
            var money = $(this).val();
            var all_money = $('.sta_all_tax_price').text();
            var res = (money / all_money) * 100;
            $('.payment_ratio').text(res.toFixed(2));
        });

        var id = $('.data').data('order_payment_id');
        $('.save_remark').click(function (e) {
            var remark = $('#orderpayment-financial_remark').val();
            $.ajax({
                type:"post",
                url:'?r=financial/add-remark',
                data:{id:id, remark:remark},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_advance').click(function (e) {
            var payment_ratio = $('#orderpayment-payment_ratio').val();
            $.ajax({
                type:"post",
                url:'?r=financial/change-advance',
                data:{id:id, payment_ratio:payment_ratio},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_payment').click(function (e) {
            $.ajax({
                type:"post",
                url:'?r=financial/change-payment',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_bill').click(function (e) {
            $.ajax({
                type:"post",
                url:'?r=financial/change-bill',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
