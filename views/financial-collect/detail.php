<?php

use app\models\SystemConfig;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;
use yii\widgets\DetailView;

$this->title = '待收款订单详情';
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
    $model->price         = $payment_ratio/100 * $orderAgreement->payment_price;
}

?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_agreement_id="<?=$_GET['id']?>">
            <tr>
                <th>零件号</th>
                <?php if (!$isShow):?>
                <th>厂家号</th>
                <?php endif;?>
                <th>中文描述</th>
                <th>英文描述</th>
                <?php if (!$isShow):?>
                <th>原厂家</th>
                <?php endif;?>
                <th>单位</th>
                <th>客户</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期(周)</th>
                <th>未税总价</th>
                <th>含税总价</th>
                <th>数量</th>
                <th>出库</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
                <tr class="order_final_list">
                    <td><?=$item->goods->goods_number?></td>
                    <?php if (!$isShow):?>
                        <td><?=$item->goods->goods_number_b?></td>
                    <?php endif;?>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <?php if (!$isShow):?>
                        <td><?=$item->goods->original_company?></td>
                    <?php endif;?>
                    <td><?=$item->goods->unit?></td>
                    <td><?=isset($item->orderAgreement) ? (isset($item->orderAgreement->customer) ? $item->orderAgreement->customer->name : '') : ''?></td>
                    <td><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->quote_price?></td>
                    <td class="tax_price"><?=$item->quote_tax_price?></td>
                    <td class="delivery_time"><?=$item->quote_delivery_time?></td>
                    <td class="all_price"><?=$item->quote_all_price?></td>
                    <td class="all_tax_price"><?=$item->quote_all_tax_price?></td>
                    <td class="number"><?=$item->number?></td>
                    <td><?=in_array($item->goods_id, $stock_goods_ids) ? '是' : '否'?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= $isShow ? 8 : 10?>" rowspan="2">汇总统计</td>
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
            <?= $form->field($model, 'price')->textInput(['maxlength' => true])->label('预收款金额') ?>
        <?php endif;?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存备注', [
                'class' => 'btn btn-primary save_remark',
                'name'  => 'submit-button']
        )?>
        <?php if(!$model->is_advancecharge):?>
        <?= Html::button('预收款完成', [
                'class' => 'btn btn-info rimary save_advance',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_payment):?>
        <?= Html::button('全单收款完成', [
                'class' => 'btn btn-info save_payment',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_bill):?>
        <?= Html::button('开发票', [
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
        $('#orderagreement-price').bind('input propertychange', function (e) {
            var money = $(this).val();
            var all_money = $('.sta_all_tax_price').text();
            var res = (money / all_money) * 100;
            $('.payment_ratio').text(res.toFixed(2));
        });

        var id = $('.data').data('order_agreement_id');
        $('.save_remark').click(function (e) {
            var remark = $('#orderagreement-financial_remark').val();
            $.ajax({
                type:"post",
                url:'?r=financial-collect/add-remark',
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
            var price = $('#orderagreement-price').val();
            $.ajax({
                type:"post",
                url:'?r=financial-collect/change-advance',
                data:{id:id, price:price},
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
                url:'?r=financial-collect/change-payment',
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
                url:'?r=financial-collect/change-bill',
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
