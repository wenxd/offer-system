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
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId = Yii::$app->user->identity->id;

//报价员权限
$is_show = in_array($userId, $adminIds);
?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover"
               style="<?= $is_show ? 'width: 1500px;' : 'width: 3000px;' ?>">
            <thead class="data" data-order_final_id="<?= $_GET['id'] ?>">
            <tr>
                <th>序号</th>
                <th>品牌</th>
                <th>零件号</th>
                <?php if (!$is_show) : ?>
                    <th>厂家号</th>
                <?php endif; ?>
                <th style="width: 200px;">中文描述</th>
                <th style="width: 200px;">英文描述</th>
                <?php if (!$is_show) : ?>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                <?php endif; ?>
                <th>订单需求数量</th>
                <th>库存数量</th>
                <th>单位</th>
                <?php if (!$is_show) : ?>
                    <th>供应商</th>
                <?php endif; ?>
                <th>税率</th>
                <?php if (!$is_show) : ?>
                    <th>发行未税单价</th>
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
                <?php endif; ?>
                <th>报价未税单价</th>
                <th>报价含税单价</th>
                <th>报价未税总价</th>
                <th>报价含税总价</th>
                <th>报价货期(周)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quoteGoods as $item): ?>
                <tr class="order_final_list">
                    <td class="quote_id" style="display: none"><?= $item->id ?></td>
                    <td class="serial"><?= $item->serial ?></td>
                    <td><?= $item->goods->material_code ?></td>
                    <td><?= $is_show ? $item->goods->goods_number : Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number])) ?></td>
                    <?php if (!$is_show) : ?>
                        <td><?= Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number])) ?></td>
                    <?php endif; ?>
                    <td><?= $item->goods->description ?></td>
                    <td><?= $item->goods->description_en ?></td>
                    <?php if (!$is_show) : ?>
                        <td><?= $item->goods->original_company ?></td>
                        <td><?= $item->goods->original_company_remark ?></td>
                    <?php endif; ?>
                    <!--订单需求数量-->
                    <td class="afterNumber"><input type="text" class="number" value="<?= $item->number ?>"
                                                   style="width: 120px;"></td>
                    <td><?= $item->stockNumber ? $item->stockNumber->number : 0 ?></td>
                    <td><?= $item->goods->unit ?></td>
                    <?php if (!$is_show) : ?>
                        <td><?= $item->inquiry->supplier->name ?></td>
                    <?php endif; ?>
                    <td class="ratio"><?= $item->tax_rate ?></td>
                    <?php if (!$is_show) : ?>
                        <td class="publish_price"><?= $item->goods->publish_price ?></td>
                        <?php
                        $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax_rate / 100), 2, '.', '');
                        ?>
                        <td class="publish_tax_price"><?= $item->publish_tax_price ?></td>
                        <td class="all_publish_tax_price"><?= $item->publish_tax_price_all ?></td>
                        <td class="publish_delivery_time"><?= $item->goods->publish_delivery_time ?></td>
                        <?php
                        $competitorGoods = CompetitorGoods::find()->where(['goods_id' => $item->goods_id])->orderBy('price asc')->one();
                        $competitorGoodsTaxPrice = $competitorGoods ? number_format($competitorGoods->price * (1 + $item->tax_rate / 100), 2, '.', '') : 0;
                        ?>
                        <td><?= $competitorGoods ? $competitorGoods->competitor->name : '' ?></td>
                        <td class="competitor_tax_price"
                            data-competitor_goods_id="<?= $competitorGoods ? $competitorGoods->id : 0 ?>"><?= $item->competitor_goods_tax_price ?></td>
                        <td class="competitor_tax_price_all"><?= $item->competitor_goods_tax_price_all ?></td>
                        <td class="competitor_public_tax_price"><?= $item->competitor_goods_quote_tax_price ?></td>
                        <td class="competitor_public_tax_price_all"><?= $item->competitor_goods_quote_tax_price_all ?></td>
                        <td class="price"><?= $item->price ?></td>
                        <td class="tax_price"><?= $item->tax_price ?></td>
                        <td class="all_price"><?= $item->all_price ?></td>
                        <td class="all_tax_price"><?= $item->all_tax_price ?></td>
                        <td class="delivery_time"><?= $item->delivery_time ?></td>
                    <?php endif; ?>
                    <!--报价未税单价	报价含税单价-->
                    <td class="quote_price"><input type="text" value="<?= $item->quote_price ?>" style="width: 120px;">
                    </td>
                    <td class="quote_tax_price"><input type="text" value="<?= $item->quote_tax_price ?>"
                                                       style="width: 120px;"></td>
                    <td class="quote_all_price"><?= $item->quote_all_price ?></td>
                    <td class="quote_all_tax_price"><?= $item->quote_all_tax_price ?></td>
                    <td class="quote_delivery_time"><input type="text" value="<?= $item->quote_delivery_time ?>"
                                                           style="width: 60px;"></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= $is_show ? 10 : 14 ?>" rowspan="2">汇总统计</td>
                <?php if (!$is_show) : ?>
                    <td rowspan="2">发行</td>
                    <td>发行含税总价合计</td>
                    <td>发行最长货期</td>
                    <td rowspan="2"></td>
                    <td rowspan="2">竞争对手</td>
                    <td>竞争对手总价</td>
                    <td rowspan="2"></td>
                    <td>预估含税总价</td>
                    <td rowspan="2"></td>
                    <td rowspan="2">成本单</td>
                    <td>成本未税总价合计</td>
                    <td>成本含税总价合计</td>
                    <td>成本最长货期</td>
                    <td rowspan="2"></td>
                <?php endif; ?>
                <td rowspan="2">报价单</td>
                <td>报价未税总价合计</td>
                <td>报价含税总价合计</td>
                <td>报价最长货期</td>
            </tr>
            <tr style="background-color: #acccb9">
                <?php if (!$is_show) : ?>
                    <td class="sta_all_publish_tax_price"></td>
                    <td class="most_publish_delivery_time"></td>
                    <td class="sta_competitor_tax_price_all"></td>
                    <td class="sta_competitor_public_tax_price_all"></td>
                    <td class="sta_all_price"></td>
                    <td class="sta_all_tax_price"></td>
                    <td class="mostLongTime"></td>
                <?php endif; ?>
                <td class="sta_quote_all_price"></td>
                <td class="sta_quote_all_tax_price"></td>
                <td class="most_quote_delivery_time"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('选择报价员') ?>

        <?= $form->field($model, 'quote_sn')->textInput(['readonly' => true]) ?>

        <?php if (!$is_show) : ?>
            <?= $form->field($model, 'profit_rate')->textInput(['readonly' => true])->label('报价单利润率') ?>

            <?= $form->field($model, 'quote_ratio')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'delivery_ratio')->textInput() ?>

            <?= $form->field($model, 'publish_ratio')->textInput(['readonly' => true]) ?>

            <?= $form->field($model, 'competitor_ratio')->textInput(['readonly' => true]) ?>
        <?php endif; ?>
        <div class="box-footer">
            <?= Html::button('保存报价单', [
                    'class' => 'btn btn-success quote_save',
                    'name'  => 'submit-button']
            )?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();

        function init() {
            var sta_publish_tax_price = 0;
            var most_publish_delivery_time = 0;

            var sta_all_price = 0;
            var sta_all_tax_price = 0;
            var mostLongTime = 0;

            var sta_quote_all_price = 0;
            var sta_quote_all_tax_price = 0;
            var most_quote_delivery_time = 0;
            var sta_competitor_tax_price_all = 0;
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

                var all_price = $(e).find('.all_price').text();
                if (all_price) {
                    sta_all_price += parseFloat(all_price);
                }
                var all_tax_price = $(e).find('.all_tax_price').text();
                if (all_tax_price) {
                    sta_all_tax_price += parseFloat(all_tax_price);
                }
                var delivery_time = parseFloat($(e).find('.delivery_time').text());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                var quote_all_price = $(e).find('.quote_all_price').text();
                if (quote_all_price) {
                    sta_quote_all_price += parseFloat(quote_all_price);
                }
                var quote_all_tax_price = $(e).find('.quote_all_tax_price').text();
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(quote_all_tax_price);
                }
                var quote_delivery_time = parseFloat($(e).find('.quote_delivery_time input').val());
                if (quote_delivery_time > most_quote_delivery_time) {
                    most_quote_delivery_time = quote_delivery_time;
                }
                //竞争者预估
                var competitor_tax_price_all = parseFloat($(e).find('.competitor_tax_price_all').text());
                if (competitor_tax_price_all) {
                    sta_competitor_tax_price_all += competitor_tax_price_all;
                }
                //竞争者最低
                var competitor_public_tax_price_all = parseFloat($(e).find('.competitor_public_tax_price_all').text());
                if (competitor_public_tax_price_all) {
                    sta_competitor_public_tax_price_all += competitor_public_tax_price_all;
                }
            });
            $('.sta_all_publish_tax_price').text(sta_publish_tax_price.toFixed(2));
            $('.most_publish_delivery_time').text(most_publish_delivery_time.toFixed(2));

            $('.sta_competitor_tax_price_all').text(sta_competitor_tax_price_all.toFixed(2));
            $('.sta_competitor_public_tax_price_all').text(sta_competitor_public_tax_price_all.toFixed(2));

            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.mostLongTime').text(mostLongTime);

            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            $('.most_quote_delivery_time').text(most_quote_delivery_time);
        }

        //计算各种率
        function compute() {
            //报价单利润率
            var sta_all_tax_price = $('.sta_all_tax_price').text();
            var sta_quote_all_tax_price = $('.sta_quote_all_tax_price').text();
            var profit_rate = (sta_quote_all_tax_price - sta_all_tax_price) / sta_quote_all_tax_price;
            $('#orderquote-profit_rate').val(profit_rate.toFixed(2) * 100);
            //
        }

        //报价未税单价修改
        $('.quote_price input').bind('input propertychange', function (e) {
            var quote_price = $(this).val();
            var ratio = $(this).parent().parent().find('.ratio').text();
            var number = $(this).parent().parent().find('.afterNumber input').val();
            var quote_tax_price = ((ratio / 100 + 1) * quote_price).toFixed(2);
            $(this).parent().parent().find('.quote_tax_price input').val(quote_tax_price)
            var quote_all_price = (quote_price * number).toFixed(2);
            $(this).parent().parent().find('.quote_all_price').text(quote_all_price);
            var quote_all_tax_price = (quote_tax_price * number).toFixed(2);
            $(this).parent().parent().find('.quote_all_tax_price').text(quote_all_tax_price);
            init();
            compute();
        });

        //输入报价含税单价
        $('.quote_tax_price input').bind('input propertychange', function (e) {
            var quote_tax_price = $(this).val();
            console.log(quote_tax_price);
            var number = $(this).parent().parent().find('.afterNumber input').val();
            var ratio = 1 + $(this).parent().parent().find('.ratio').text() / 100;
            var quote_price = (quote_tax_price / ratio).toFixed(2);
            console.log(quote_price);
            $(this).parent().parent().find('.quote_price input').val(quote_price)
            var quote_all_price = (quote_price * number).toFixed(2);
            $(this).parent().parent().find('.quote_all_price').text(quote_all_price);
            var quote_all_tax_price = (quote_tax_price * number).toFixed(2);
            $(this).parent().parent().find('.quote_all_tax_price').text(quote_all_tax_price);
            init();
            compute();
        });

        //输入数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time: 2000});
                return false;
            }
            var a = number.replace(/[\D]/g, '');
            $(this).val(a);

            var publish_price = $(this).parent().parent().find('.publish_tax_price').text();
            var price = $(this).parent().parent().find('.price').text();
            var tax_price = $(this).parent().parent().find('.tax_price').text();
            var quote_price = $(this).parent().parent().find('.quote_price input').val();
            var quote_tax_price = $(this).parent().parent().find('.quote_tax_price input').val();
            var competitor_tax_price = parseFloat($(this).parent().parent().find('.competitor_tax_price').text());
            var competitor_public_tax_price = parseFloat($(this).parent().parent().find('.competitor_public_tax_price').text());

            $(this).parent().parent().find('.all_publish_tax_price').text(parseFloat(publish_price * number).toFixed(2));
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));

            $(this).parent().parent().find('.competitor_tax_price_all').text(parseFloat(competitor_tax_price * number).toFixed(2));
            $(this).parent().parent().find('.competitor_public_tax_price_all').text(parseFloat(competitor_public_tax_price * number).toFixed(2));

            $(this).parent().parent().find('.quote_all_price').text(parseFloat(quote_price * number).toFixed(2));
            $(this).parent().parent().find('.quote_all_tax_price').text(parseFloat(quote_tax_price * number).toFixed(2));
            init();
            compute();
        });

        //输入货期系数
        $('#orderquote-delivery_ratio').bind('input propertychange', function (e) {
            var delivery_ratio = $(this).val();
            var most_quote_delivery_time    = 0;
            $('.order_final_list').each(function (i, e) {
                var delivery_time = $(e).find('.delivery_time').text();
                var quote_delivery_time = Math.round(parseFloat((delivery_time * delivery_ratio).toFixed(2)));
                $(e).find('.quote_delivery_time input').val(quote_delivery_time);
                if (quote_delivery_time > most_quote_delivery_time) {
                    most_quote_delivery_time = quote_delivery_time;
                }
            });
            $('.most_quote_delivery_time').text(most_quote_delivery_time);
        });

        //单独输入报价货期
        $('.quote_delivery_time input').bind('input propertychange', function (e) {
            var quote_delivery_time = parseFloat($(this).val());
            var most_quote_delivery_time = 0;
            $('.order_final_list').each(function (i, e) {
                var delivery_time = $(e).find('.quote_delivery_time input').val();
                if (delivery_time > most_quote_delivery_time) {
                    most_quote_delivery_time = delivery_time;
                }
            });
            if (quote_delivery_time > most_quote_delivery_time) {
                most_quote_delivery_time = quote_delivery_time;
            }
            $('.most_quote_delivery_time').text(most_quote_delivery_time);
        });
        //保存
        $('.quote_save').click(function (e) {
            //防止双击
            // $(".quote_save").attr("disabled", true).addClass("disabled");
            var goods_info = [];
            var number_flag = false;
            $('.quote_id').each(function (index, element) {
                var item = {};
                var elements = $(element).parent();
                item.quote_id = $(this).text();
                item.goods_id    = $(element).val();
                if (!elements.find('.number').val()){
                    number_flag  = true;
                }
                item.number              = elements.find('.number').val();
                item.type                = $(element).data('type');
                item.relevance_id        = $(element).data('relevance_id');

                item.serial              = elements.find('.serial').text();
                item.tax_rate            = elements.find('.ratio').text();
                item.delivery_time       = elements.find('.delivery_time').text();
                item.price               = elements.find('.price').text();
                item.tax_price           = elements.find('.tax_price').text();
                item.all_price           = elements.find('.all_price').text();
                item.all_tax_price       = elements.find('.all_tax_price').text();
                item.quote_price         = elements.find('.quote_price input').val();
                item.quote_tax_price     = elements.find('.quote_tax_price input').val();
                item.quote_all_price     = elements.find('.quote_all_price').text();
                item.quote_all_tax_price = elements.find('.quote_all_tax_price').text();
                item.quote_delivery_time = elements.find('.quote_delivery_time input').val();

                item.competitor_goods_id                    = elements.find('.competitor_tax_price').data('competitor_goods_id');
                item.competitor_goods_tax_price             = parseFloat(elements.find('.competitor_tax_price').text());
                item.competitor_goods_tax_price_all         = parseFloat(elements.find('.competitor_tax_price_all').text());
                item.competitor_goods_quote_tax_price       = parseFloat(elements.find('.competitor_public_tax_price').text());
                item.competitor_goods_quote_tax_price_all   = parseFloat(elements.find('.competitor_public_tax_price_all').text());
                item.publish_tax_price       = parseFloat(elements.find('.publish_tax_price').text());
                item.all_publish_tax_price   = parseFloat(elements.find('.all_publish_tax_price').text());

                goods_info.push(item);
            });
            console.log(goods_info);
            if (number_flag) {
                layer.msg('请给选中的行输入数量', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var admin_id = $('#orderquote-admin_id').val();
            if (!admin_id) {
                layer.msg('请选择采购员', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var quote_sn = $('#orderquote-quote_sn').val();
            if (!quote_sn) {
                layer.msg('请填写报价单号', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var quote_ratio = $('#orderquote-quote_ratio').val();
            if (!quote_ratio) {
                layer.msg('请填写报价系数', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var delivery_ratio = $('#orderquote-delivery_ratio').val();
            if (!delivery_ratio) {
                layer.msg('请填写货期系数', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var competitor_ratio = $('#orderquote-competitor_ratio').val();
            if (!competitor_ratio) {
                layer.msg('请填写竞报价系数', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var order_final_id = $('.data').data('order_final_id');

            var sta_quote_all_tax_price  = parseFloat($('.sta_quote_all_tax_price').text());
            var sta_all_tax_price        = parseFloat($('.sta_all_tax_price').text());
            var most_quote_delivery_time = parseFloat($('.most_quote_delivery_time').text());
            var mostLongTime             = parseFloat($('.mostLongTime').text());
            var publish_ratio            = parseFloat($('#orderquote-publish_ratio').val());

            //两个总价
            var sta_all_publish_tax_price           = parseFloat($('.sta_all_publish_tax_price').text());
            var sta_competitor_public_tax_price_all = parseFloat($('.sta_competitor_public_tax_price_all').text());

            //报价利润率
            var profit_rate = $('#orderquote-profit_rate').val();
            console.log({order_final_id:order_final_id, admin_id:admin_id, quote_sn:quote_sn, quote_ratio:quote_ratio,
                delivery_ratio:delivery_ratio, goods_info:goods_info, competitor_ratio:competitor_ratio,
                sta_quote_all_tax_price:sta_quote_all_tax_price, sta_all_tax_price:sta_all_tax_price,
                most_quote_delivery_time:most_quote_delivery_time, mostLongTime:mostLongTime,
                publish_ratio:publish_ratio, sta_all_publish_tax_price:sta_all_publish_tax_price,
                sta_competitor_public_tax_price_all:sta_competitor_public_tax_price_all, profit_rate:profit_rate});
            $.ajax({
                type:"post",
                url:'?r=order-quote/save-order1',
                data:{order_final_id:order_final_id, admin_id:admin_id, quote_sn:quote_sn, quote_ratio:quote_ratio,
                    delivery_ratio:delivery_ratio, goods_info:goods_info, competitor_ratio:competitor_ratio,
                    sta_quote_all_tax_price:sta_quote_all_tax_price, sta_all_tax_price:sta_all_tax_price,
                    most_quote_delivery_time:most_quote_delivery_time, mostLongTime:mostLongTime,
                    publish_ratio:publish_ratio, sta_all_publish_tax_price:sta_all_publish_tax_price,
                    sta_competitor_public_tax_price_all:sta_competitor_public_tax_price_all, profit_rate:profit_rate},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        // location.replace("?r=order-quote/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

    });
</script>
