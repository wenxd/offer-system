<?php

use app\models\CompetitorGoods;
use app\models\SystemConfig;
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
$userId   = Yii::$app->user->identity->id;

//报价员权限
$is_show = in_array($userId, $adminIds);
?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
            <tr>
                <th>序号</th>
                <th>零件号</th>
                <?php if (!$is_show) :?>
                <th>厂家号</th>
                <?php endif;?>
                <th>中文描述</th>
                <th>英文描述</th>
                <?php if (!$is_show) :?>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <?php endif;?>
                <th>订单需求数量</th>
                <th>库存数量</th>
                <th>单位</th>
                <?php if (!$is_show) :?>
                <th>供应商</th>
                <?php endif;?>
                <th>税率</th>
                <?php if (!$is_show) :?>
                <th>发行含税单价</th>
                <th>发行含税总价</th>
                <th>发行货期</th>
                <th>竞争对手名称</th>
                <th>竞争对手最低含税单价</th>
                <th>竞争对手最低含税总价</th>
                <th>竞争对手预估含税报价</th>
                <th>竞争对手预估含税总价</th>
                <th>成本未税单价</th>
                <th>成本含税单价</th>
                <th>成本未税总价</th>
                <th>成本含税总价</th>
                <th>成本货期(周)</th>
                <?php endif;?>
                <th>报价未税单价</th>
                <th>报价含税单价</th>
                <th>报价未税总价</th>
                <th>报价含税总价</th>
                <th>报价货期(周)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quoteGoods as $item):?>
                <tr class="order_final_list">
                    <td class="serial"><?=$item->serial?></td>
                    <td><?=$is_show ? $item->goods->goods_number : Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <?php if (!$is_show) :?>
                    <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <?php endif;?>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <?php if (!$is_show) :?>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <?php endif;?>
                    <td class="afterNumber"><?=$item->number?></td>
                    <td><?=$item->stockNumber ? $item->stockNumber->number : 0?></td>
                    <td><?=$item->goods->unit?></td>
                    <?php if (!$is_show) :?>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <?php endif;?>
                    <td class="ratio"><?=$item->tax_rate?></td>
                    <?php if (!$is_show) :?>
                    <?php
                        $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax_rate/100), 2, '.', '');
                    ?>
                    <td class="publish_tax_price"><?=$publish_tax_price?></td>
                    <td class="all_publish_tax_price"><?=$publish_tax_price * $item->number?></td>
                    <td class="publish_delivery_time"><?=$item->goods->publish_delivery_time?></td>
                    <?php
                        $competitorGoods = CompetitorGoods::find()->where(['goods_id' => $item->goods_id])->orderBy('price asc')->one();
                        $competitorGoodsTaxPrice = $competitorGoods ? number_format($competitorGoods->price * (1 + $item->tax_rate/100), 2, '.', '') : 0;
                    ?>
                    <td><?=$competitorGoods ? $competitorGoods->competitor->name : ''?></td>
                    <td class="competitor_tax_price" data-competitor_goods_id="<?=$competitorGoods ? $competitorGoods->id : 0?>"><?=$competitorGoodsTaxPrice?></td>
                    <td class="competitor_tax_price_all"><?=$competitorGoods ? $competitorGoodsTaxPrice * $item->number : 0?></td>
                    <td class="competitor_public_tax_price"><?=$publish_tax_price * $orderQuote->competitor_ratio?></td>
                    <td class="competitor_public_tax_price_all"><?=$publish_tax_price * $orderQuote->competitor_ratio * $item->number?></td>
                    <td class="price"><?=$item->price?></td>
                    <td class="tax_price"><?=$item->tax_price?></td>
                    <td class="all_price"><?=$item->all_price?></td>
                    <td class="all_tax_price"><?=$item->all_tax_price?></td>
                    <td class="delivery_time"><?=$item->delivery_time?></td>
                    <?php endif;?>
                    <td class="quote_price"><?=$item->quote_price?></td>
                    <td class="quote_tax_price"><?=$item->quote_tax_price?></td>
                    <td class="quote_all_price"><?=$item->quote_all_price?></td>
                    <td class="quote_all_tax_price"><?=$item->quote_all_tax_price?></td>
                    <td class="quote_delivery_time"><?=$item->quote_delivery_time?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= $is_show ? 9 : 12?>" rowspan="2">汇总统计</td>
                <?php if (!$is_show) :?>
                <td rowspan="2">发行</td>
                <td>发行含税总价合计</td>
                <td>发行最长货期</td>
                <td colspan="4" rowspan="2"></td>
                <td>预估含税总价</td>
                <td rowspan="2"></td>
                <td rowspan="2">成本单</td>
                <td>成本未税总价合计</td>
                <td>成本含税总价合计</td>
                <td>成本最长货期</td>
                <td rowspan="2"></td>
                <?php endif;?>
                <td rowspan="2">报价单</td>
                <td>报价未税总价合计</td>
                <td>报价含税总价合计</td>
                <td>报价最长货期</td>
            </tr>
            <tr style="background-color: #acccb9">
                <?php if (!$is_show) :?>
                <td class="sta_all_publish_tax_price"></td>
                <td class="most_publish_delivery_time"></td>
                <td class="sta_competitor_public_tax_price_all"></td>
                <td class="sta_all_price"></td>
                <td class="sta_all_tax_price"></td>
                <td class="mostLongTime"></td>
                <?php endif;?>
                <td class="sta_quote_all_price"></td>
                <td class="sta_quote_all_tax_price"></td>
                <td class="most_quote_delivery_time"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('选择报价员') ?>

        <?= $form->field($model, 'quote_sn')->textInput(['readonly' => true]) ?>

        <?php if (!$is_show) :?>
        <?= $form->field($model, 'quote_ratio')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'delivery_ratio')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'competitor_ratio')->textInput(['readonly' => true]) ?>
        <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        function init(){
            var sta_publish_tax_price       = 0;
            var most_publish_delivery_time  = 0;

            var sta_all_price               = 0;
            var sta_all_tax_price           = 0;
            var mostLongTime                = 0;

            var sta_quote_all_price         = 0;
            var sta_quote_all_tax_price     = 0;
            var most_quote_delivery_time    = 0;
            var sta_competitor_public_tax_price_all = 0;
            $('.order_final_list').each(function (i, e) {
                var publish_tax_price = parseFloat($(e).find('.all_publish_tax_price').text());
                if (publish_tax_price) {
                    sta_publish_tax_price += publish_tax_price;
                }
                var publish_delivery_time = parseFloat($(e).find('.publish_delivery_time').text());
                if (publish_delivery_time > most_publish_delivery_time) {
                    most_publish_delivery_time = publish_delivery_time;
                }

                var all_price       = $(e).find('.all_price').text();
                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }
                var all_tax_price   = $(e).find('.all_tax_price').text();
                if (all_tax_price) {
                    sta_all_tax_price  += parseFloat(all_tax_price);
                }
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                var quote_all_price     = $(e).find('.quote_all_price').text();
                if (quote_all_price) {
                    sta_quote_all_price += parseFloat(quote_all_price);
                }
                var quote_all_tax_price = $(e).find('.quote_all_tax_price').text();
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(quote_all_tax_price);
                }
                var quote_delivery_time   = parseFloat($(e).find('.quote_delivery_time').text());
                if (quote_delivery_time > most_quote_delivery_time) {
                    most_quote_delivery_time = quote_delivery_time;
                }
                var competitor_public_tax_price_all = parseFloat($(e).find('.competitor_public_tax_price_all').text());
                if (competitor_public_tax_price_all) {
                    sta_competitor_public_tax_price_all += competitor_public_tax_price_all;
                }
            });
            $('.sta_all_publish_tax_price').text(sta_publish_tax_price.toFixed(2));
            $('.most_publish_delivery_time').text(most_publish_delivery_time.toFixed(2));

            $('.sta_competitor_public_tax_price_all').text(sta_competitor_public_tax_price_all.toFixed(2));

            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.mostLongTime').text(mostLongTime);

            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            $('.most_quote_delivery_time').text(most_quote_delivery_time);
        }
    });
</script>
