<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '报价单详情';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '报价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
            <tr>
                <th>序号</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>订单需求数量</th>
                <th>单位</th>
                <th>供应商</th>
                <th>税率</th>
                <th>货期(周)</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>未税总价</th>
                <th>含税总价</th>
                <th>报价货期(周)</th>
                <th>报价未税单价</th>
                <th>报价含税单价</th>
                <th>报价未税总价</th>
                <th>报价含税总价</th>
                <th>库存数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quoteGoods as $item):?>
                <tr class="order_final_list">
                    <td class="serial"><?=$item->serial?></td>
                    <td><?=Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td class="afterNumber"><?=$item->number?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->type ? $item->stock->supplier->name : $item->inquiry->supplier->name?></td>
                    <td class="ratio"><?=$item->tax_rate?></td>
                    <td class="delivery_time"><?=$item->delivery_time?></td>
                    <td class="price"><?=$item->price?></td>
                    <td class="tax_price"><?=$item->tax_price?></td>
                    <td class="all_price"><?=$item->all_price?></td>
                    <td class="all_tax_price"><?=$item->all_tax_price?></td>
                    <td class="quote_delivery_time"><?=$item->quote_delivery_time?></td>
                    <td class="quote_price"><?=$item->quote_price?></td>
                    <td class="quote_tax_price"><?=$item->quote_tax_price?></td>
                    <td class="quote_all_price"><?=$item->quote_all_price?></td>
                    <td class="quote_all_tax_price"><?=$item->quote_all_tax_price?></td>
                    <?php
                        $is_inquiry = false;
                        foreach ($inquiryGoods as $inquiry) {
                            if ($inquiry['goods_id'] == $item->goods_id && $inquiry['serial'] == $item->serial) {
                                if ($inquiry['is_inquiry']) {
                                    $is_inquiry = true;
                                }
                                break;
                            }
                        }
                    ?>
                    <td><?=$item->stockNumber ? $item->stockNumber->number : 0?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="12" rowspan="2">汇总统计</td>
                <td rowspan="2">成本单</td>
                <td>最长货期</td>
                <td>未税总价</td>
                <td>含税总价</td>
                <td rowspan="2"></td>
                <td rowspan="2">报价单</td>
                <td>报价最长货期</td>
                <td>报价未税总价</td>
                <td>报价含税总价</td>
                <td colspan="2" rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="mostLongTime"></td>
                <td class="sta_all_price"></td>
                <td class="sta_all_tax_price"></td>
                <td class="quote_mostLongTime"></td>
                <td class="sta_quote_all_price"></td>
                <td class="sta_quote_all_tax_price"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('选择报价员') ?>

        <?= $form->field($model, 'quote_sn')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'quote_ratio')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'delivery_ratio')->textInput(['readonly' => true]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        function init(){
            var mostLongTime            = 0;
            var sta_all_price           = 0;
            var sta_all_tax_price       = 0;
            var sta_quote_all_price     = 0;
            var sta_quote_all_tax_price = 0;
            var quote_mostLongTime      = 0;
            $('.order_final_list').each(function (i, e) {
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }
                var all_price       = $(e).find('.all_price').text();
                var all_tax_price   = $(e).find('.all_tax_price').text();
                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }
                if (all_tax_price) {
                    sta_all_tax_price  += parseFloat(all_tax_price);
                }
                var quote_all_price     = $(e).find('.quote_all_price').text();
                var quote_all_tax_price = $(e).find('.quote_all_tax_price').text();
                if (quote_all_price) {
                    sta_quote_all_price += parseFloat(quote_all_price);
                }
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(quote_all_tax_price);
                }
                var quote_delivery_time   = parseFloat($(e).find('.quote_delivery_time').text());
                if (quote_delivery_time > mostLongTime) {
                    quote_mostLongTime = quote_delivery_time;
                }

            });
            $('.mostLongTime').text(mostLongTime);
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            $('.quote_mostLongTime').text(quote_mostLongTime);
        }
    });
</script>
