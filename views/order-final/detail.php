<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\CompetitorGoods;
use app\models\SystemConfig;
use app\models\AuthAssignment;

$this->title = '生成报价单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['报价员', '系统管理员', '订单管理员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$idStr = implode(',', $adminIds);
$adminList = Admin::find()->where(['id' => $adminIds])->orderBy([new \yii\db\Expression("FIELD(`id`, $idStr)")])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$customer_name = $order->customer ? $order->customer->short_name : '';
$model->quote_sn = 'Q' . date('ymd_') . $customer_name . '_' . $number;

$model->quote_ratio = SystemConfig::find()->select('value')->where([
            'title'      => SystemConfig::TITLE_QUOTE_PRICE_RATIO,
            'is_deleted' => SystemConfig::IS_DELETED_NO,
    ])->scalar();

$model->delivery_ratio = SystemConfig::find()->select('value')->where([
        'title'      => SystemConfig::TITLE_QUOTE_DELIVERY_RATIO,
        'is_deleted' => SystemConfig::IS_DELETED_NO,
])->scalar();

$model->publish_ratio = 1;

//竞争对手报价系数
$model->competitor_ratio = $competitor_ratio = SystemConfig::find()->select('value')->where([
        'is_deleted' => SystemConfig::IS_DELETED_NO,
        'title'      => SystemConfig::TITLE_COMPETITOR_RATIO
    ])->scalar();

//系数税率
$tax = SystemConfig::find()->select('value')->where([
    'is_deleted' => SystemConfig::IS_DELETED_NO,
    'title'      => SystemConfig::TITLE_TAX
])->scalar();
?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 3000px; table-layout: auto">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th style="max-width: 200px;">中文描述</th>
                <th>原厂家</th>
                <th>订单需求数量</th>
                <th>库存数量</th>
                <th>单位</th>
                <th>供应商</th>
                <th>税率</th>
                <th>发行价未税单价</th>
                <th>发行价含税单价</th>
                <th>发行价含税总价</th>
                <th>发货期</th>
                <th>竞争者名称</th>
                <th>竞低含单</th>
                <th>竞低含总</th>
                <th>竞预含单</th>
                <th>竞预含总</th>
                <th>成本未税单</th>
                <th>成本含税单</th>
                <th>成本未税总</th>
                <th>成本含税总</th>
                <th>成本货期</th>
                <th>报价未税单价</th>
                <th>报价含税单价</th>
                <th>报价未税总价</th>
                <th>报价含税总价</th>
                <th>报价货期(周)</th>
                <th>是否有报价单</th>
                <th>报价单号</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finalGoods as $item):?>
            <tr class="order_final_list">
                <td><?=isset($purchaseGoods[$item->goods_id]) ? '' : "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id}  value={$item->goods_id} class='select_id'>"?></td>
                <td class="serial"><?=$item->serial?></td>
                <td><?=Html::a($item->goods->goods_number . ' ' . $item->goods->material_code, Url::to(['goods/search-result', 'goods_id' => $item->goods->id]))?></td>
                <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'goods_id' => $item->goods->id]))?></td>
                <td><?=$item->goods->description?></td>
                <td><?=$item->goods->original_company?></td>
                <td class="afterNumber"><?=isset($purchaseGoods[$item->goods_id]) ? $purchaseGoods[$item->goods_id]->number :
                        '<input type="number" size="4" class="number" min="1" style="width: 100px;" value="' . $item->number . '" 
    onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'\')}else{this.value=this.value.replace(/\D/g,\'\')}"
    onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'0\')}else{this.value=this.value.replace(/\D/g,\'\')}">'?>
                </td>
                <td><?=$item->stockNumber ? $item->stockNumber->number : 0?></td>
                <td><?=$item->goods->unit?></td>
                <td><?=$item->inquiry->supplier->name?></td>
                <td class="ratio"><?=$tax?></td>
                <td style="background-color: #ffbeba" class="publish_price"><?=$item->goods->publish_price?></td>
                <?php
                    $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax/100), 2, '.', '');
                ?>
                <td class="publish_tax_price"><?=$publish_tax_price?></td>
                <td class="all_publish_tax_price"></td>
                <td class="publish_delivery_time"><?=$item->goods->publish_delivery_time?></td>
                <?php
                    $competitorGoods = CompetitorGoods::find()->where(['goods_id' => $item->goods_id])->orderBy('price asc')->one();
                    $competitorGoodsTaxPrice = $competitorGoods ? number_format($competitorGoods->price * (1 + $tax/100), 2, '.', '') : 0;
                ?>
                <td><?=$competitorGoods ? $competitorGoods->competitor->name : ''?></td>
                <td style="background-color: #d3d0ff" class="competitor_tax_price" data-competitor_goods_id="<?=$competitorGoods ? $competitorGoods->id : 0?>"><?=$competitorGoodsTaxPrice?></td>
                <td class="competitor_tax_price_all"><?=$competitorGoods ? $competitorGoodsTaxPrice * $item->number : 0?></td>
                <td class="competitor_public_tax_price"><input type="text"  style="width: 100px;" value="<?=$item->goods->publish_price * (1 + $tax/100) * $competitor_ratio?>"></td>
                <td class="competitor_public_tax_price_all"><?=$item->goods->publish_price * (1 + $tax/100) * $item->number * $competitor_ratio?></td>
                <td style="background-color: #fbffbc" class="price"><?=$item->price?></td>
                <td class="tax_price"><?=$item->tax_price?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
                <td class="delivery_time"><?=$item->inquiry->delivery_time?></td>
                <td class="quote_price"><input type="text" style="width: 100px;"></td>
                <td class="quote_tax_price"><input type="text" style="width: 100px;"></td>
                <td class="quote_all_price"></td>
                <td class="quote_all_tax_price"></td>
                <td class="quote_delivery_time"><input type="text" style="width: 100px;"></td>
                <td><?=isset($quoteGoods[$item->goods_id]) ? '是' : '否'?></td>
                <td><?=isset($quoteGoods[$item->goods_id]) ? $quoteGoods[$item->goods_id]->order_quote_sn : ''?></td>
            </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="12" rowspan="2">汇总统计</td>
                <td rowspan="2">发行价</td>
                <td>发行总价</td>
                <td>货期</td>
                <td colspan="2" rowspan="2"></td>
                <td>最低总价</td>
                <td rowspan="2"></td>
                <td>预估含税总价</td>
                <td colspan="2" rowspan="2"></td>
                <td rowspan="2">成本单</td>
                <td>成本总价</td>
                <td>货期</td>
                <td colspan="2" rowspan="2"></td>
                <td rowspan="2">报价单</td>
                <td>报价总价</td>
                <td>货期</td>
                <td colspan="3" rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="sta_all_publish_tax_price"></td>
                <td class="most_publish_delivery_time"></td>
                <td class="sta_competitor_low_tax_price_all"></td>
                <td class="sta_competitor_public_tax_price_all"></td>
                <td class="sta_all_tax_price"></td>
                <td class="mostLongTime"></td>
                <td class="sta_quote_all_tax_price"></td>
                <td class="most_quote_delivery_time"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择报价员') ?>

        <?= $form->field($model, 'quote_sn')->textInput() ?>

        <?= $form->field($model, 'profit_rate')->textInput(['readonly' => true])->label('报价单利润率%') ?>

        <?= $form->field($model, 'quote_ratio')->textInput() ?>

        <?= $form->field($model, 'delivery_ratio')->textInput() ?>

        <?= $form->field($model, 'publish_ratio')->textInput()->label('发行价系数') ?>

        <?= $form->field($model, 'competitor_ratio')->textInput()->label('竞报价系数') ?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存报价单', [
                'class' => 'btn btn-success quote_save',
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
                $('.quote_save').hide();
                $('.field-orderpurchase-admin_id').hide();
                $('.field-orderpurchase-end_date').hide();
            }
            var quote_ratio                 = $('#orderquote-quote_ratio').val();
            var quote_delivery_ratio        = $('#orderquote-delivery_ratio').val();
            var mostLongTime                = 0;
            var sta_all_price               = 0;
            var sta_all_tax_price           = 0;
            var sta_publish_tax_price       = 0;
            var most_publish_delivery_time  = 0;
            var sta_quote_all_price         = 0;
            var sta_quote_all_tax_price     = 0;
            var most_quote_delivery_time    = 0;
            var sta_competitor_low_tax_price_all    = 0;
            var sta_competitor_public_tax_price_all = 0;
            $('.order_final_list').each(function (i, e) {
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                var publish_delivery_time = parseFloat($(e).find('.publish_delivery_time').text());
                if (publish_delivery_time > most_publish_delivery_time) {
                    most_publish_delivery_time = publish_delivery_time;
                }

                var price               = $(e).find('.price').text();
                var tax_price           = $(e).find('.tax_price').text();
                var number              = $(e).find('.afterNumber input').val();
                var publish_tax_price   = $(e).find('.publish_tax_price').text();

                var all_price               = parseFloat(price * number).toFixed(2);
                var all_tax_price           = parseFloat(tax_price * number).toFixed(2);
                var all_publish_tax_price   = parseFloat(publish_tax_price * number).toFixed(2);

                $(e).find('.all_publish_tax_price').text(all_publish_tax_price);
                $(e).find('.all_price').text(all_price);
                $(e).find('.all_tax_price').text(all_tax_price);

                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }
                if (all_tax_price) {
                    sta_all_tax_price  += parseFloat(all_tax_price);
                }
                if (all_publish_tax_price) {
                    sta_publish_tax_price += parseFloat(all_publish_tax_price);
                }
                //报价
                var quote_price         = parseFloat((quote_ratio * price).toFixed(2));
                var quote_tax_price     = parseFloat((quote_ratio * tax_price).toFixed(2));
                var quote_all_price     = parseFloat((quote_price * number).toFixed(2));
                var quote_all_tax_price = parseFloat((quote_tax_price * number).toFixed(2));
                var quote_delivery_time = Math.round(parseFloat((quote_delivery_ratio * delivery_time).toFixed(2)));

                $(e).find('.quote_price input').val(quote_price);
                $(e).find('.quote_tax_price input').val(quote_tax_price);
                $(e).find('.quote_all_price').text(quote_all_price);
                $(e).find('.quote_all_tax_price').text(quote_all_tax_price);
                $(e).find('.quote_delivery_time input').val(quote_delivery_time);
                if (quote_all_price) {
                    sta_quote_all_price += quote_all_price;
                }
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += quote_all_tax_price;
                }
                if (quote_delivery_time > most_quote_delivery_time) {
                    most_quote_delivery_time = quote_delivery_time;
                }
                var competitor_tax_price_all = parseFloat($(e).find('.competitor_tax_price_all').text());
                if (competitor_tax_price_all) {
                    sta_competitor_low_tax_price_all += competitor_tax_price_all;
                }

                var competitor_public_tax_price_all = parseFloat($(e).find('.competitor_public_tax_price_all').text());
                if (competitor_public_tax_price_all) {
                    sta_competitor_public_tax_price_all += competitor_public_tax_price_all;
                }
            });
            $('.sta_all_publish_tax_price').text(sta_publish_tax_price.toFixed(2));
            $('.most_publish_delivery_time').text(most_publish_delivery_time);
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.mostLongTime').text(mostLongTime);
            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            $('.most_quote_delivery_time').text(most_quote_delivery_time);
            $('.sta_competitor_low_tax_price_all').text(sta_competitor_low_tax_price_all.toFixed(2));
            $('.sta_competitor_public_tax_price_all').text(sta_competitor_public_tax_price_all.toFixed(2));

            //报价单利润率（报价总价-成本总价）/报价总价
            var rate = (sta_quote_all_tax_price.toFixed(2) - sta_all_tax_price.toFixed(2)) / sta_quote_all_tax_price.toFixed(2);
            rate = (rate * 100).toFixed(2);
            $('#orderquote-profit_rate').val(rate);
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

        //输入报价未税单价
        $(".quote_price input").bind('input propertychange', function (e) {
            var quote_price = $(this).val();
            var number      = $(this).parent().parent().find('.number').val();
            var ratio       = 1 + $(this).parent().parent().find('.ratio').text() / 100;

            var quote_tax_price     = (quote_price * ratio).toFixed(2);
            var quote_all_price     = (quote_price * number).toFixed(2);
            var quote_all_tax_price = (quote_price * ratio * number).toFixed(2);

            $(this).parent().parent().find('.quote_tax_price input').val(quote_tax_price);
            $(this).parent().parent().find('.quote_all_price').text(quote_all_price);
            $(this).parent().parent().find('.quote_all_tax_price').text(quote_all_tax_price);

            //统计报价
            statPrice();
        });

        //输入报价含税单价
        $(".quote_tax_price input").bind('input propertychange', function (e) {
            var quote_tax_price = $(this).val();
            var number          = $(this).parent().parent().find('.number').val();
            var ratio           = 1 + $(this).parent().parent().find('.ratio').text() / 100;

            var quote_price         = (quote_tax_price / ratio).toFixed(2);
            var quote_all_price     = (quote_price * number).toFixed(2);
            var quote_all_tax_price = (quote_tax_price * number).toFixed(2);

            $(this).parent().parent().find('.quote_price input').val(quote_price);
            $(this).parent().parent().find('.quote_all_price').text(quote_all_price);
            $(this).parent().parent().find('.quote_all_tax_price').text(quote_all_tax_price);

            //统计报价
            statPrice();
        });


        //输入数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time:2000});
                return false;
            }
            var a = number.replace(/[\D]/g,'');
            $(this).val(a);

            var publish_price               = $(this).parent().parent().find('.publish_tax_price').text();
            var price                       = $(this).parent().parent().find('.price').text();
            var tax_price                   = $(this).parent().parent().find('.tax_price').text();
            var quote_price                 = $(this).parent().parent().find('.quote_price input').val();
            var quote_tax_price             = $(this).parent().parent().find('.quote_tax_price').text();
            var competitor_tax_price        = parseFloat($(this).parent().parent().find('.competitor_tax_price').text());
            var competitor_public_tax_price = parseFloat($(this).parent().parent().find('.competitor_public_tax_price input').val());

            $(this).parent().parent().find('.all_publish_tax_price').text(parseFloat(publish_price * number).toFixed(2));
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));

            $(this).parent().parent().find('.competitor_tax_price_all').text(parseFloat(competitor_tax_price * number).toFixed(2));
            $(this).parent().parent().find('.competitor_public_tax_price_all').text(parseFloat(competitor_public_tax_price * number).toFixed(2));

            $(this).parent().parent().find('.quote_all_price').text(parseFloat(quote_price * number).toFixed(2));
            $(this).parent().parent().find('.quote_all_tax_price').text(parseFloat(quote_tax_price * number).toFixed(2));

            statPrice();
        });

        //输入竞争对手预估含税报价
        $(".competitor_public_tax_price input").bind('input propertychange', function (e) {
            var price    = parseFloat($(this).val());
            var number   = parseFloat($(this).parent().parent().find('.afterNumber input').val());
            var competitor_public_tax_price_all = number * price;
            $(this).parent().parent().find('.competitor_public_tax_price_all').text(competitor_public_tax_price_all.toFixed(2));
            var sta_competitor_public_tax_price_all = 0;
            $('.order_final_list').each(function (i, e) {
                var competitor_public_tax_price_all = parseFloat($(e).find('.competitor_public_tax_price_all').text());
                if (competitor_public_tax_price_all) {
                    sta_competitor_public_tax_price_all += competitor_public_tax_price_all;
                }
            });
            $('.sta_competitor_public_tax_price_all').text(sta_competitor_public_tax_price_all.toFixed(2));
        });

        //输入报价系数
        $('#orderquote-quote_ratio').bind('input propertychange', function (e) {
            var quote_ratio = $(this).val();
            var sta_quote_all_price     = 0;
            var sta_quote_all_tax_price = 0;
            $('.order_final_list').each(function (i, e) {
                //成本价
                var price           = $(e).find('.price').text();
                var tax_price       = $(e).find('.tax_price').text();
                var number          = $(e).find('.afterNumber input').val();
                var all_price       = parseFloat(price * number).toFixed(2);
                var all_tax_price   = parseFloat(tax_price * number).toFixed(2);
                //报价
                var quote_price         = (price * quote_ratio).toFixed(2);
                var quote_tax_price     = (tax_price * quote_ratio).toFixed(2);
                var quote_all_price     = (all_price * quote_ratio).toFixed(2);
                var quote_all_tax_price = (all_tax_price * quote_ratio).toFixed(2);

                $(e).find('.quote_price input').val(quote_price);
                $(e).find('.quote_tax_price input').val(quote_tax_price);
                $(e).find('.quote_all_price').text(quote_all_price);
                $(e).find('.quote_all_tax_price').text(quote_all_tax_price);

                if (quote_all_price) {
                    sta_quote_all_price += parseFloat(quote_all_price);
                }
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(quote_all_tax_price);
                }
            });
            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            statPrice();
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

        //输入发行价系数
        $('#orderquote-publish_ratio').bind('input propertychange', function (e) {
            var quote_publish_price_ratio = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
            var quote_publish_price_all = 0;
            $('.order_final_list').each(function (i, e) {
                var tax               = parseFloat($(e).find('.ratio').text());
                var number            = parseFloat($(e).find('.afterNumber input').val());
                var publish_price     = parseFloat($(e).find('.publish_price').text());
                var publish_tax_price = (publish_price * (1 + tax/100) * quote_publish_price_ratio).toFixed(2);
                $(e).find('.publish_tax_price').text(publish_tax_price);
                var new_all_publish_tax_price = publish_price * (1 + tax/100) * quote_publish_price_ratio * number;
                $(e).find('.all_publish_tax_price').text(new_all_publish_tax_price.toFixed(2));
                if (new_all_publish_tax_price) {
                    quote_publish_price_all += new_all_publish_tax_price;
                }
            });
            $('.sta_all_publish_tax_price').text(quote_publish_price_all.toFixed(2));
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
            $(".quote_save").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            // 计算相同零件号未税单价是否一致
            var status = false;
            $keys = [];
            $delivery_keys = [];
            $('.select_id').each(function (index, element) {
                $(element).parent().parent().css('backgroundColor', 'white');
                var temp_goods_id = $(element).val();
                var temp_quote_price = $(element).parent().parent().find('.quote_price input').val();
                if ($keys.hasOwnProperty(temp_goods_id)) {
                    if ($keys[temp_goods_id] != temp_quote_price) {
                        // 不同的做强提示
                        $('.select_id').each(function (index, element) {
                            var temp_parent = $(element).parent().parent();
                            if (temp_goods_id == $(element).val()) {
                                temp_parent.css('backgroundColor', '#ffbeba');
                            } else {
                                temp_parent.css('backgroundColor', 'white');
                            }
                        });
                        layer.msg('多零件号未税单价不一致', {time:3000, icon:2});
                        status = true;
                        return false;
                    }
                } else {
                    $keys[temp_goods_id] = temp_quote_price;
                }
                // 检测货期
                var temp_quote_delivery_time = $(element).parent().parent().find('.quote_delivery_time input').val();
                if ($delivery_keys.hasOwnProperty(temp_goods_id)) {
                    if ($delivery_keys[temp_goods_id] != temp_quote_delivery_time) {
                        // 不同的做强提示
                        $('.select_id').each(function (index, element) {
                            var temp_parent = $(element).parent().parent();
                            if (temp_goods_id == $(element).val()) {
                                temp_parent.css('backgroundColor', '#ffbeba');
                            } else {
                                temp_parent.css('backgroundColor', 'white');
                            }
                        });
                        layer.msg('多零件号货期不一致', {time:3000, icon:2});
                        status = true;
                        return false;
                    }
                } else {
                    $delivery_keys[temp_goods_id] = temp_quote_delivery_time;
                }
            });
            if (status) {
                $(".quote_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var goods_info = [];
            var number_flag = false;
            $('.select_id').each(function (index, element) {
                var item = {};
                if ($(element).prop("checked")) {
                    item.goods_id    = $(element).val();
                    if (!$(element).parent().parent().find('.number').val()){
                        number_flag  = true;
                    }
                    item.number              = $(element).parent().parent().find('.number').val();
                    item.type                = $(element).data('type');
                    item.relevance_id        = $(element).data('relevance_id');

                    item.serial              = $(element).parent().parent().find('.serial').text();
                    item.tax_rate            = $(element).parent().parent().find('.ratio').text();
                    item.delivery_time       = $(element).parent().parent().find('.delivery_time').text();
                    item.price               = $(element).parent().parent().find('.price').text();
                    item.tax_price           = $(element).parent().parent().find('.tax_price').text();
                    item.all_price           = $(element).parent().parent().find('.all_price').text();
                    item.all_tax_price       = $(element).parent().parent().find('.all_tax_price').text();
                    item.quote_price         = $(element).parent().parent().find('.quote_price input').val();
                    item.quote_tax_price     = $(element).parent().parent().find('.quote_tax_price input').val();
                    item.quote_all_price     = $(element).parent().parent().find('.quote_all_price').text();
                    item.quote_all_tax_price = $(element).parent().parent().find('.quote_all_tax_price').text();
                    item.quote_delivery_time = $(element).parent().parent().find('.quote_delivery_time input').val();

                    item.competitor_goods_id                    = $(element).parent().parent().find('.competitor_tax_price').data('competitor_goods_id');
                    item.competitor_goods_tax_price             = parseFloat($(element).parent().parent().find('.competitor_tax_price').text());
                    item.competitor_goods_tax_price_all         = parseFloat($(element).parent().parent().find('.competitor_tax_price_all').text());
                    item.competitor_goods_quote_tax_price       = parseFloat($(element).parent().parent().find('.competitor_public_tax_price input').val());
                    item.competitor_goods_quote_tax_price_all   = parseFloat($(element).parent().parent().find('.competitor_public_tax_price_all').text());
                    item.publish_tax_price       = parseFloat($(element).parent().parent().find('.publish_tax_price').text());
                    item.all_publish_tax_price   = parseFloat($(element).parent().parent().find('.all_publish_tax_price').text());

                    goods_info.push(item);
                }
            });

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

            $.ajax({
                type:"post",
                url:'?r=order-quote/save-order',
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
                        location.replace("?r=order-quote/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        //计算总价
        function statPrice() {
            var sta_all_price               = 0;
            var sta_all_tax_price           = 0;
            var sta_quote_all_price         = 0;
            var sta_quote_all_tax_price     = 0;
            var sta_all_publish_tax_price   = 0;
            var sta_competitor_low_tax_price_all    = 0;
            var sta_competitor_public_tax_price_all = 0;
            $('.order_final_list').each(function (i, e) {
                var all_price               = $(e).find('.all_price').text();
                var all_tax_price           = $(e).find('.all_tax_price').text();
                var quote_all_price         = $(e).find('.quote_all_price').text();
                var quote_all_tax_price     = $(e).find('.quote_all_tax_price').text();
                var all_publish_tax_price   = $(e).find('.all_publish_tax_price').text();
                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }
                if (all_tax_price) {
                    sta_all_tax_price  += parseFloat(all_tax_price);
                }
                if (quote_all_price) {
                    sta_quote_all_price += parseFloat(quote_all_price);
                }
                if (quote_all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(quote_all_tax_price);
                }
                if (all_publish_tax_price) {
                    sta_all_publish_tax_price += parseFloat(all_publish_tax_price);
                }
                var competitor_tax_price_all = parseFloat($(e).find('.competitor_tax_price_all').text());
                if (competitor_tax_price_all) {
                    sta_competitor_low_tax_price_all += competitor_tax_price_all;
                }
                var competitor_public_tax_price_all = parseFloat($(e).find('.competitor_public_tax_price_all').text());
                if (competitor_public_tax_price_all) {
                    sta_competitor_public_tax_price_all += competitor_public_tax_price_all;
                }
            });
            $('.sta_all_publish_tax_price').text(sta_all_publish_tax_price.toFixed(2));
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
            $('.sta_competitor_low_tax_price_all').text(sta_competitor_low_tax_price_all.toFixed(2));
            $('.sta_competitor_public_tax_price_all').text(sta_competitor_public_tax_price_all.toFixed(2));

            //报价单利润率（报价总价-成本总价）/报价总价
            var rate = (sta_quote_all_tax_price.toFixed(2) - sta_all_tax_price.toFixed(2)) / sta_quote_all_tax_price.toFixed(2);
            rate = (rate * 100).toFixed(2);
            $('#orderquote-profit_rate').val(rate);
        }
        
        //输入竞争者报价系数
        $('#orderquote-competitor_ratio').bind('input propertychange', function (e) {
            var competitor_ratio = parseFloat($(this).val());
            $('.order_final_list').each(function (i, e) {
                var tax          = parseFloat($(e).find('.ratio').text());
                var number       = parseFloat($(e).find('.afterNumber input').val());
                var public_price = parseFloat($(e).find('.publish_price').text());
                var competitor_public_tax_price = public_price * (1 + tax/100) * competitor_ratio;
                $(e).find('.competitor_public_tax_price input').val(competitor_public_tax_price.toFixed(2));
                $(e).find('.competitor_public_tax_price_all').text((competitor_public_tax_price * number).toFixed(2));
            });
            statPrice();
        });
    });
</script>
