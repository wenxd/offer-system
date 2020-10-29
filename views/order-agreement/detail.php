<?php

use app\extend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\Helper;
use app\models\Inquiry;
use app\models\SystemConfig;
use app\models\AuthAssignment;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = '生成采购单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');
//采购商品IDs
$purchaseGoods_ids = ArrayHelper::getColumn($purchaseGoods, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '询价员', '采购员']])->all();
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$model->purchase_sn = 'B' . date('ymd_') . $number;
$model->agreement_date = substr($orderAgreement->agreement_date, 0, 10);

$system_tax = SystemConfig::find()->select('value')->where([
    'is_deleted' => 0,
    'title' => SystemConfig::TITLE_TAX,
])->scalar();
?>
<style>
    #example2 {
        position: relative;
        clear: both;
        zoom: 1;
        overflow-x: auto;
    }

</style>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-header">

        <div class="col-md-12">
            <div class="col-md-6">
                <?= Bar::widget([
                    'template' => '{low} {short} {stock} {better} {new} {recover}',
                    'buttons' => [
                        'low' => function () {
                            return Html::a('<i class="fa fa-reload"></i> 一键最低', Url::to(['low', 'id' => $_GET['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-flat',
                            ]);
                        },
                        'short' => function () {
                            return Html::a('<i class="fa fa-reload"></i> 一键最短', Url::to(['short', 'id' => $_GET['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-info btn-flat',
                            ]);
                        },
                        'stock' => function () {
//                            return Html::a('<i class="fa fa-reload"></i> 一键走库存', Url::to(['stock', 'id' => $_GET['id']]), [
//                                'data-pjax' => '0',
//                                'class' => 'btn btn-primary btn-flat',
//                            ]);
                            return Html::button('一键走库存', ['class' => 'btn btn-primary btn-flat', 'onclick' => 'exit_stock()']);
                        },
                        'better' => function () {
                            return Html::a('<i class="fa fa-reload"></i> 一键优选', Url::to(['better', 'id' => $_GET['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-success btn-flat',
                            ]);
                        },
                        'new' => function () {
                            return Html::a('<i class="fa fa-reload"></i> 一键最新', Url::to(['new', 'id' => $_GET['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-info btn-flat',
                            ]);
                        },
                        'recover' => function () {
                            return Html::a('<i class="fa fa-reload"></i> 一键恢复', Url::to(['recover', 'id' => $_GET['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-danger btn-flat',
                            ]);
                        }
                    ]
                ]) ?>
                <script>
                    function exit_stock() {
                        var goods_info = [];
                        $('.oldNumber').each(function (index, element) {
                            // 合同需求数量
                            var oldNumber = parseInt($(element).text());
                            // 库存数量
                            var stock_number = parseInt($(this).parent().find('.stock_number').text());
                            // 库存数量 < 合同需求数量
                            if (stock_number < oldNumber) {
                                $(this).parent().find('.afterNumber').find('.number').val(oldNumber - stock_number);
                                $(this).parent().find('.use_stock').text(stock_number);
                            } else {
                                $(this).parent().find('.afterNumber').find('.number').val(0);
                                $(this).parent().find('.use_stock').text(oldNumber);
                            }
                        });

                    }
                </script>
            </div>
        </div>

    </div>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 3000px; table-layout: auto">
            <thead class="data" data-order_agreement_id="<?= $_GET['id'] ?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <th>操作</th>
                <th style="width: 100px;">零件号</th>
                <th style="width: 100px;">厂家号</th>
                <th style="width: 100px;">总成</th>
                <th style="width: 100px;">中文描述</th>
                <th style="max-width: 150px;">英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th style="width: 100px;">供应商</th>
                <th>询价员</th>
                <th>税率</th>
                <th>最低未税单价</th>
                <th>最低含税总价</th>
                <th>最低货期</th>
                <th>货期最短未税单价</th>
                <th>货期最短含税总价</th>
                <th>货期最短货期</th>
                <th>采购未税单价</th>
                <th>采购未税总价</th>
                <th>采购含税单价</th>
                <th>采购含税总价</th>
                <th>采购货期</th>
                <th>采购单号</th>
                <th>合同货期</th>
                <th>策略需求数量</th>
                <th>策略采购数量</th>
                <th>单位</th>
                <th>使用库存数量</th>
                <th>临时库存数量</th>
                <th>库存数量</th>
                <th>建议库存</th>
                <th>高储</th>
                <th>低储</th>
            </tr>
            <tr id="w3-filters" class="filters">
                <td>
                    <button type="button" class="btn btn-success btn-xs inquiry_search">搜索</button>
                </td>
                <td>
                    <?= Html::a('复位', '?r=order-agreement/detail&id=' . $_GET['id'], ['class' => 'btn btn-info btn-xs']) ?>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="width:100px">
                    <input type="text" class="form-control" name="original_company"
                           value="<?= $_GET['original_company'] ?? '' ?>">
                </td>
                <td></td>
                <td></td>
                <td>
                    <select class="form-control" name="admin_id">
                        <option value=""></option>
                        <?php foreach ($admins as $key => $value) : ?>
                            <option value="<?= $key ?>"
                                    <?= isset($_GET['admin_id']) ? ($_GET['admin_id'] === (string)$key ? 'selected' : '') : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
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
            <?php foreach ($agreementGoods as $item): ?>
                <tr class="order_agreement_list">
                    <?php
                    $checkbox = true;
                    $order_purchase_sn = '';
                    $purchase_number = 0;
                    if (isset($purchaseGoods[$item->goods_id])) {
                        $purchaseGoodsList = $purchaseGoods[$item->goods_id];
                        foreach ($purchaseGoodsList as $k => $v) {
                            if ($v['serial'] == $item->serial && $v['goods_id'] == $item->goods_id) {
                                $checkbox = false;
                                $order_purchase_sn = $v['order_purchase_sn'];
                                $purchase_number = $v['fixed_number'];
                            }
                        }
                    }
                    ?>
                    <td>
                        <?= $checkbox ? "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-agreement_goods_id={$item->id} value={$item->goods_id} class='select_id'>" : "" ?>
                    </td>
                    <td><?= $item->serial ?></td>
                    <td><?= Html::a('关联询价记录', Url::to(['inquiry/search', 'goods_id' => $item->goods_id, 'agreement_goods_id' => $item->id, 'order_agreement_id' => $_GET['id']], ['class' => 'btn btn-primary btn-flat'])) ?></td>
                    <td><?= Html::a($item->goods->goods_number . ' ' . $item->goods->material_code, Url::to(['goods/search-result', 'goods_id' => $item->goods->id])) ?></td>
                    <td><?= Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'goods_id' => $item->goods->id])) ?></td>
                    <td>
                        <?php
                        $text = '';
                        if (!empty($item->belong_to)) {
                            foreach (json_decode($item->belong_to, true) as $key => $device) {
                                $text .= $key . ':' . $device . '<br/>';
                            }
                        }
                        echo $text;
                        ?>
                    </td>
                    <td><?= $item->goods->description ?></td>
                    <td><?= $item->goods->description_en ?></td>
                    <td><?= $item->goods->original_company ?></td>
                    <td><?= $item->goods->original_company_remark ?></td>
                    <td class="supplier_name"><?= $item->inquiry->supplier->name ?></td>
                    <td><?php
                        $user = Admin::findOne($item->inquiry_admin_id);
                        if (isset($user->username)) {
                            echo $user->username;
                        } else {
                            echo '';
                        }
                        ?></td>
                    <td><?= $item->tax_rate ?></td>
                    <?php
                    $lowPriceInquiry = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('price asc')->one();
                    $deliverInquiry = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('delivery_time asc')->one();
                    ?>
                    <td class="low_price"
                        style="background-color:#00FF33"><?= $lowPriceInquiry ? $lowPriceInquiry->price : 0 ?></td>
                    <td class="low_tax_price"><?= $lowPriceInquiry ? ($lowPriceInquiry->price * (1 + $system_tax / 100)) * $item->number : 0 ?></td>
                    <td class="low_delivery"><?= $lowPriceInquiry ? $lowPriceInquiry->delivery_time : 0 ?></td>
                    <td class="short_price"><?= $deliverInquiry ? $deliverInquiry->price : 0 ?></td>
                    <td class="short_tax_price"><?= $deliverInquiry ? ($deliverInquiry->price * (1 + $system_tax / 100)) * $item->number : 0 ?></td>
                    <td class="short_delivery"
                        style="background-color:#0099FF"><?= $deliverInquiry ? $deliverInquiry->delivery_time : 0 ?></td>
                    <td class="price" style="background-color:#00FF33"><?= $item->price ?></td>
                    <td class="all_price"><?= $item->all_price ?></td>
                    <td class="tax_price"><?= $item->tax_price ?></td>
                    <td class="all_tax_price"><?= number_format($item->price * (1 + $system_tax / 100) * $item->purchase_number, 2, '.', '') ?></td>
                    <td class="delivery_time" style="background-color:#0099FF"><?= $item->delivery_time ?></td>
                    <td><?= $order_purchase_sn ?></td>
                    <td class="quote_delivery_time"><?= $item->quote_delivery_time ?></td>
                    <td class="oldNumber"><?= $item->order_number ?></td>
                    <td class="afterNumber">
                        <input type="number" size="4" goods_id="<?=$item->goods_id?>" class="number" min="1" style="width: 50px;"
                               value="<?= $item->purchase_number ?>">
                    </td>
                    <td><?= $item->goods->unit ?></td>
                    <td class="use_stock"></td>
                    <td class="stock_number">
                        <?php
//                        $stock = $item->stock ? $item->stock->number : 0;
//                        $use_number = $item->orderstock->use_number ?? 0;
//                        echo $stock - $use_number;
                        echo $item->stock ? $item->stock->temp_number : 0;
                        ?>
                    </td>
                    <td><?= $item->stock ? $item->stock->number : 0;?></td>
                    <td><?= $item->stock ? $item->stock->suggest_number : 0 ?></td>
                    <td><?= $item->stock ? $item->stock->high_number : 0 ?></td>
                    <td><?= $item->stock ? $item->stock->low_number : 0 ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: #acccb9">
                <td colspan="13" rowspan="2">汇总统计</td>
                <td>最低含税总价</td>
                <td>最低最长货期</td>
                <td rowspan="2"></td>
                <td>货期最短含税总价</td>
                <td>货期最短最长货期</td>
                <td colspan="3" rowspan="2"></td>
                <td>采购含税总价</td>
                <td>采购最长货期</td>
                <td></td>
                <td>合同最长货期</td>
                <td colspan="8"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="stat_low_tax_price_all"></td>
                <td class="most_low_deliver"></td>
                <td class="stat_short_tax_price_all"></td>
                <td class="most_short_deliver"></td>
                <td class="purchase_all_price"></td>
                <td class="mostLongTime"></td>
                <td></td>
                <td class="quote_mostLongTime"></td>
                <td colspan="8"></td>
            </tr>
            </tbody>
        </table>
        <?= $form->field($model, 'purchase_sn')->textInput()->label('采购订单号') ?>

        <?= $form->field($model, 'admin_id')->dropDownList(Helper::getAdminList(['系统管理员', '采购员']))->label('选择采购员') ?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView' => 2,  //最大选择范围（年）
                'minView' => 2,  //最小选择范围（年）
            ]
        ])->label('收入合同交货时间'); ?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存采购数量/使用库存', ['class' => 'btn btn-primary purchase_number_save', 'name' => 'submit-button']) ?>
        <?php
        if ($orderAgreement->is_strategy == 1 && $orderAgreement->is_purchase_number == 1) {
            // 查询是否有未确认使用库存列表
            $count = \app\models\AgreementStock::find()
                ->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $orderAgreement->id, 'is_confirm' => \app\models\AgreementStock::IS_CONFIRM_NO])
                ->count();
            if (!$count) {
                // 没有保存采购策略不允许保存采购订单
                if ($orderAgreement->is_purchase_number) {
                    echo Html::button('保存采购单', [
                            'class' => 'btn btn-success purchase_save',
                            'name' => 'submit-button']
                    );
                } else {
                    echo "<p class='text-danger'>没有保存采购单采购数量/使用库存</p>";
                }

            } else {
                echo "<p class='text-danger'>使用库存未确认 * {$count}</p>";
            }
        }
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<style>
    #example2 {
        position: relative;
        clear: both;
        zoom: 1;
        overflow-x: auto;
    }

</style>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //保存采购数量/使用库存
        $('.purchase_number_save').click(function (e) {
            //防止双击
            // $(".purchase_save").attr("disabled", true).addClass("disabled");
            var goods_info = [];
            $('.number').each(function (index, element) {
                var goods_id = $(element).attr('goods_id');
                var strategy_number = $(element).val();
                goods_info.push({goods_id:goods_id,strategy_number:strategy_number});
            });
            console.log(goods_info);
            $.ajax({
                type:"post",
                url:'<?=$_SERVER['REQUEST_URI']?>',
                data:{goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    console.log(res);
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                    }
                }
            });
        });
        init();
        var order_id = <?=$order->id?>;
        var temporary_purchase_sn = '<?=$model->purchase_sn?>';
        //选择采购员时判断同一个订单是否已经有过同一个人的采购单号
        $('#orderagreement-admin_id').change(function (e) {
            var admin_id = $('#orderagreement-admin_id').val();
            $.ajax({
                type: "get",
                url: '?r=search/get-purchase-sn',
                data: {order_id: order_id, admin_id: admin_id},
                dataType: 'JSON',
                success: function (res) {
                    console.log(res);
                    if (res && res.code == 200) {
                        $('#orderagreement-purchase_sn').val(res.data.purchase_sn);
                    } else {
                        $('#orderagreement-purchase_sn').val(temporary_purchase_sn);
                    }
                }
            });
        });

        function init() {
            if (!$('.select_id').length) {
                $('.select_all').hide();
                $('.purchase_save').hide();
                $('.field-orderpurchase-purchase_sn').hide();
                $('.field-orderpurchase-admin_id').hide();
                $('.field-orderpurchase-agreement_date').hide();
            }
            var mostLongTime = 0;
            var purchase_price = 0;
            var purchase_all_price = 0;
            var quote_mostLongTime = 0;
            var stat_low_price_all = 0;
            var stat_low_tax_price_all = 0;
            var most_low_deliver = 0;
            var stat_short_price_all = 0;
            var stat_short_tax_price_all = 0;
            var most_short_deliver = 0;
            $('.order_agreement_list').each(function (i, e) {
                var price = $(e).find('.price').text();
                var tax_price = price * (1 + '<?=$system_tax?>' / 100);
                var number = parseFloat($(e).find('.oldNumber').text());
                var delivery_time = parseFloat($(e).find('.delivery_time').text());
                var purchase_number = parseFloat($(e).find('.number').val());

                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                //$(e).find('.all_price').text(parseFloat(price * purchase_number).toFixed(2));
                //$(e).find('.all_tax_price').text(parseFloat(tax_price * purchase_number).toFixed(2));

                var all_price = parseFloat(price * purchase_number);
                var all_tax_price = parseFloat(tax_price * purchase_number);

                purchase_price += parseFloat(all_price);
                purchase_all_price += parseFloat(all_tax_price);

                //默认使用库存数量
                var use_number = number - purchase_number;
                if (use_number < 0) {
                    use_number = 0;
                }
                $(e).find('.use_stock').text(use_number);

                //合同货期
                var quote_delivery_time = parseFloat($(e).find('.quote_delivery_time').text());
                if (quote_delivery_time > quote_mostLongTime) {
                    quote_mostLongTime = quote_delivery_time;
                }

                //最低
                var low_price = parseFloat($(e).find('.low_price').text());
                var low_tax_price = low_price * (1 + '<?=$system_tax?>' / 100);
                var low_delivery = parseFloat($(e).find('.low_delivery').text());
                if (low_tax_price) {
                    $(e).find('.low_tax_price').text(parseFloat(low_tax_price * purchase_number).toFixed(2));
                    stat_low_tax_price_all += low_tax_price * purchase_number;
                }
                if (low_delivery > most_low_deliver) {
                    most_low_deliver = low_delivery;
                }
                //最短
                var short_price = parseFloat($(e).find('.short_price').text());
                var short_tax_price = short_price * (1 + '<?=$system_tax?>' / 100);
                var short_delivery = parseFloat($(e).find('.short_delivery').text());
                if (short_tax_price) {
                    $(e).find('.short_tax_price').text(parseFloat(short_tax_price * purchase_number).toFixed(2));
                    stat_short_tax_price_all += short_tax_price * purchase_number;
                }
                if (short_delivery > most_short_deliver) {
                    most_short_deliver = short_delivery;
                }
            });
            $('.stat_low_tax_price_all').text(stat_low_tax_price_all.toFixed(2));
            $('.most_low_deliver').text(most_low_deliver);
            $('.stat_short_tax_price_all').text(stat_short_tax_price_all.toFixed(2));
            $('.most_short_deliver').text(most_short_deliver);
            $('.mostLongTime').text(mostLongTime);
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));
            $('.quote_mostLongTime').text(quote_mostLongTime.toFixed(2));
        }

        //全选
        $('.select_all').click(function (e) {
            $('.select_id').prop("checked", $(this).prop("checked"));
        });

        //子选择
        $('.select_id').on('click', function (e) {
            if ($('.select_id').length == $('.select_id:checked').length) {
                $('.select_all').prop("checked", true);
            } else {
                $('.select_all').prop("checked", false);
            }
        });

        //输入数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            var a = number.replace(/[^\d]/g, '');
            $(this).val(a);

            var price = $(this).parent().parent().find('.price').text();
            var tax_price = price * (1 + '<?=$system_tax?>' / 100);
            //最低
            var low_price = parseFloat($(this).parent().parent().find('.low_price').text());
            var low_tax_price = low_price * (1 + '<?=$system_tax?>' / 100);
            //最短
            var short_price = parseFloat($(this).parent().parent().find('.short_price').text());
            var short_tax_price = short_price * (1 + '<?=$system_tax?>' / 100);

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));

            $(this).parent().parent().find('.low_tax_price').text(parseFloat(low_tax_price * number).toFixed(2));
            $(this).parent().parent().find('.short_tax_price').text(parseFloat(short_tax_price * number).toFixed(2));

            //默认使用库存数量
            var agreement_number = parseFloat($(this).parent().parent().find('.oldNumber').text());
            var use_number = agreement_number - number;
            if (use_number < 0) {
                use_number = 0;
            }
            var stock_number = parseFloat($(this).parent().parent().find('.stock_number').text());
            if (use_number > stock_number) {
                layer.msg('库存不足', {time:2000});
                $(this).val(agreement_number);
                $(this).parent().parent().find('.use_stock').text(0);
                return false;
            }

            $(this).parent().parent().find('.use_stock').text(use_number);

            var purchase_price = 0;
            var purchase_all_price = 0;
            var stat_low_tax_price_all = 0;
            var stat_short_tax_price_all = 0;
            $('.order_agreement_list').each(function (i, e) {
                var all_price = $(e).find('.all_price').text();
                var all_tax_price = $(e).find('.all_tax_price').text();
                var low_tax_price_all = $(e).find('.low_tax_price').text();
                var short_tax_price_all = $(e).find('.short_tax_price').text();
                purchase_price += parseFloat(all_price);
                purchase_all_price += parseFloat(all_tax_price);
                stat_low_tax_price_all += parseFloat(low_tax_price_all);
                stat_short_tax_price_all += parseFloat(short_tax_price_all);
            });
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));

            //对新加的最低和最短做动态变化
            $('.stat_low_tax_price_all').text(stat_low_tax_price_all.toFixed(2));
            $('.stat_short_tax_price_all').text(stat_short_tax_price_all.toFixed(2));
        });

        //保存
        $('.purchase_save').click(function (e) {
            //防止双击
            $(".purchase_save").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var goods_info = [];
            var number_flag = false;
            var supplier_flag = false;
            var flag_stock = false;
            var purchase_number_flag = false;
            var supplier_name = '';
            $('.select_id').each(function (index, element) {
                var item = {};
                if ($(element).prop("checked")) {
                    var s_name = $(element).parent().parent().find('.supplier_name').text();
                    if (!supplier_name) {
                        supplier_name = s_name;
                    } else {
                        if (supplier_name != s_name) {
                            supplier_flag = true;
                        }
                    }
                    if (!$(element).parent().parent().find('.number').val()) {
                        number_flag = true;
                    }

                    var purchase_number = parseFloat($(element).parent().parent().find('.number').val());
                    var stock_number = parseFloat($(element).parent().parent().find('.stock_number').text());
                    var old_number = parseFloat($(element).parent().parent().find('.oldNumber').text());
                    var use_stock = parseFloat($(element).parent().parent().find('.use_stock').text());

                    if (purchase_number == 0 && old_number > stock_number) {
                        flag_stock = true;
                    }
                    if (use_stock > stock_number) {
                        purchase_number_flag = true;
                    }

                    item.agreement_goods_id = $(element).data('agreement_goods_id');
                    item.goods_id = $(element).val();
                    item.number = $(element).parent().parent().find('.number').val();
                    item.type = $(element).data('type');
                    item.relevance_id = $(element).data('relevance_id');
                    item.delivery_time = $(element).parent().parent().find('.delivery_time').text();
                    goods_info.push(item);
                }
            });

            // if (supplier_flag) {
            //     layer.msg('一个支出合同不能有多个供应商', {time:2000});
            //     return false;
            // }

            if (purchase_number_flag) {
                layer.msg('使用库存数量不能比库存大', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            if (flag_stock) {
                layer.msg('需求数量大于库存数量时，采购数量不能为0', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            if (number_flag) {
                layer.msg('请给选中的行输入数量', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var purchase_sn = $('#orderagreement-purchase_sn').val();
            if (!purchase_sn) {
                layer.msg('请输入采购单号', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var admin_id = $('#orderagreement-admin_id').val();
            if (!admin_id) {
                layer.msg('请选择采购员', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var agreement_date = $('#orderagreement-agreement_date').val();
            if (!agreement_date) {
                layer.msg('请输入收入合同交货日期', {time: 2000});
                $(".purchase_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var order_agreement_id = $('.data').data('order_agreement_id');
            console.log({
                order_agreement_id: order_agreement_id,
                purchase_sn: purchase_sn,
                agreement_date: agreement_date,
                admin_id: admin_id,
                goods_info: goods_info
            });
            $.ajax({
                type: "post",
                url: '?r=order-purchase/save-order',
                data: {
                    order_agreement_id: order_agreement_id,
                    purchase_sn: purchase_sn,
                    agreement_date: agreement_date,
                    admin_id: admin_id,
                    goods_info: goods_info
                },
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        });

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
                    case 'admin_id':
                        parameter += '&admin_id=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order-agreement/detail&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
    });
</script>
