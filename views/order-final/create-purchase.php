<?php

use app\extend\widgets\Bar;
use app\models\Inquiry;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\SystemConfig;
use app\models\AuthAssignment;

$this->title = '生成采购单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');
//采购商品IDs
$purchaseGoods_ids = ArrayHelper::getColumn($purchaseGoods, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '询价员', '采购员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$customer_name = $order->customer ? $order->customer->short_name : '';
$model->purchase_sn = 'B' . date('ymd_') . $number;
$model->end_date    = date('Y-m-d', time() + 3600 * 24 * 3);

$system_tax= SystemConfig::find()->select('value')->where([
        'title'      => SystemConfig::TITLE_TAX,
        'is_deleted' => SystemConfig::IS_DELETED_NO,
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
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{low} {short} {stock} {better} {new} {recover}',
            'buttons' => [
                'low' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 一键最低', Url::to(['low', 'id' => $_GET['id']]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                },
                'short' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 一键最短', Url::to(['short', 'id' => $_GET['id']]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat',
                    ]);
                },
                'better' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 一键优选', Url::to(['better', 'id' => $_GET['id']]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                },
                'new' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 一键最新', Url::to(['new', 'id' => $_GET['id']]), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat',
                    ]);
                },
            ]
        ])?>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 3000px; table-layout: auto">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <th>操作</th>
                <th style="width: 100px;">零件号</th>
                <th style="width: 100px;">厂家号</th>
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
                <th>采购含税单价</th>
                <th>采购未税总价</th>
                <th>采购含税总价</th>
                <th>采购货期</th>
                <th>采购单号</th>
                <th>订单需求数量</th>
                <th>采购数量</th>
                <th>单位</th>
                <th>使用库存数量</th>
                <th>库存数量</th>
                <th>建议库存</th>
                <th>高储</th>
                <th>低储</th>
            </tr>
            <tr id="w3-filters" class="filters">
                <td><button type="button" class="btn btn-success btn-xs inquiry_search">搜索</button></td>
                <td>
                    <?=Html::a('复位', '?r=order-final/create-purchase&id=' . $_GET['id'], ['class' => 'btn btn-info btn-xs'])?>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="width:100px">
                    <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                </td>
                <td></td>
                <td></td>
                <td>
                    <select class="form-control" name="admin_id">
                        <option value=""></option>
                        <?php foreach ($admins as $key => $value) :?>
                            <option value="<?=$key?>" <?=isset($_GET['admin_id']) ? ($_GET['admin_id'] === (string)$key ? 'selected' : '') : ''?>><?=$value?></option>
                        <?php endforeach;?>
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
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finalGoods as $item):?>
                <tr class="order_agreement_list">
                    <td>
                        <?=isset($purchaseGoods[$item->goods_id]) ? '' : "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-final_goods_id={$item->id} value={$item->goods_id} class='select_id'>"?>
                    </td>
                    <td><?=$item->serial?></td>
                    <td><?=Html::a('关联询价记录', Url::to(['inquiry/search', 'goods_id' => $item->goods_id, 'final_goods_id' => $item->id, 'order_final_id' => $_GET['id']], ['class' => 'btn btn-primary btn-flat']))?></td>
                    <td><?=Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td class="supplier_name"><?=$item->inquiry->supplier->name?></td>
                    <td><?=Admin::findOne($item->inquiry->admin_id)->username?></td>
                    <td><?=$item->tax?></td>
                    <?php
                        $lowPriceInquiry = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('price asc')->one();
                        $deliverInquiry  = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('delivery_time asc')->one();
                    ?>
                    <td class="low_price" style="background-color:#00FF33"><?=$lowPriceInquiry ? $lowPriceInquiry->price : 0?></td>
                    <td class="low_tax_price"><?=$lowPriceInquiry ? ($lowPriceInquiry->price * (1 + $system_tax/100)) * $item->number  : 0?></td>
                    <td class="low_delivery"><?=$lowPriceInquiry ? $lowPriceInquiry->delivery_time : 0?></td>
                    <td class="short_price"><?=$deliverInquiry ? $deliverInquiry->price : 0?></td>
                    <td class="short_tax_price"><?=$deliverInquiry ? ($deliverInquiry->price * (1 + $system_tax/100)) * $item->number  : 0?></td>
                    <td class="short_delivery" style="background-color:#0099FF"><?=$deliverInquiry ? $deliverInquiry->delivery_time : 0?></td>

                    <td class="price"><?=$item->price?></td>
                    <?php
                        $tax_price = number_format($item->price * (1 + $item->tax/100), 2, '.', '');
                    ?>
                    <td class="tax_price"><?=$tax_price?></td>
                    <td class="all_price"><?=$item->price * $item->number?></td>
                    <td class="all_tax_price"><?=$tax_price * $item->number?></td>
                    <td class="delivery_time"><?=$item->delivery_time?></td>
                    <td><?=isset($purchaseGoods[$item->goods_id]) ? $purchaseGoods[$item->goods_id]->order_purchase_sn : ''?></td>
                    <td class="oldNumber"><?=$item->number?></td>
                    <td class="afterNumber">
                        <input type="number" size="4" class="number" min="1" style="width: 50px;" value="<?=isset($purchaseGoods[$item->goods_id]) ? $purchaseGoods[$item->goods_id]->fixed_number : $item->number?>">
                    </td>
                    <td><?=$item->goods->unit?></td>
                    <td class="use_stock"></td>
                    <td><?=$item->stock ? $item->stock->number : 0?></td>
                    <td><?=$item->stock ? $item->stock->suggest_number : 0?></td>
                    <td><?=$item->stock ? $item->stock->high_number : 0?></td>
                    <td><?=$item->stock ? $item->stock->low_number : 0?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="13" rowspan="2">汇总统计</td>
                <td>最低含税总价</td>
                <td>最低最长货期</td>
                <td rowspan="2"></td>
                <td>货期最短含税总价</td>
                <td>货期最短最长货期</td>
                <td colspan="3" rowspan="2"></td>
                <td>采购含税总价</td>
                <td>最长货期</td>
                <td colspan="9"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="stat_low_tax_price_all"></td>
                <td class="most_low_deliver"></td>
                <td class="stat_short_tax_price_all"></td>
                <td class="most_short_deliver"></td>
                <td class="purchase_all_price"></td>
                <td class="mostLongTime"></td>
                <td colspan="9"></td>
            </tr>
            </tbody>
        </table>
        <?= $form->field($model, 'purchase_sn')->textInput() ?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择采购员') ?>

        <?= $form->field($model, 'end_date')->widget(DateTimePicker::className(), [
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
    <div class="box-footer">
        <?= Html::button('保存采购单', [
                'class' => 'btn btn-success purchase_save',
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
                $('.purchase_save').hide();
                $('.field-orderpurchase-purchase_sn').hide();
                $('.field-orderpurchase-admin_id').hide();
                $('.field-orderpurchase-end_date').hide();
            }
            var sta_all_price       = 0;
            var sta_all_tax_price   = 0;
            var mostLongTime        = 0;
            var purchase_price      = 0;
            var purchase_all_price  = 0;
            var stat_low_tax_price_all  = 0;
            var most_low_deliver        = 0;
            var stat_short_tax_price_all= 0;
            var most_short_deliver      = 0;
            $('.order_agreement_list').each(function (i, e) {
                //最低
                var low_tax_price   = parseFloat($(e).find('.low_tax_price').text());
                var low_delivery    = parseFloat($(e).find('.low_delivery').text());
                if (low_tax_price){
                    stat_low_tax_price_all += low_tax_price;
                }
                if (low_delivery > most_low_deliver) {
                    most_low_deliver = low_delivery;
                }
                //最短
                var short_tax_price = parseFloat($(e).find('.short_tax_price').text());
                var short_delivery  = parseFloat($(e).find('.short_delivery').text());
                if (short_tax_price){
                    stat_short_tax_price_all += short_tax_price;
                }
                if (short_delivery > most_short_deliver) {
                    most_short_deliver = short_delivery;
                }

                var price           = $(e).find('.price').text();
                var tax_price       = $(e).find('.tax_price').text();
                var number          = $(e).find('.oldNumber').text();
                var delivery_time   = parseFloat($(e).find('.delivery_time').text());
                var purchase_number = $(e).find('.number').val();

                sta_all_price += parseFloat(price * number);
                sta_all_tax_price += parseFloat(tax_price * number);
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }

                $(e).find('.all_price').text(parseFloat(price * purchase_number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * purchase_number).toFixed(2));

                var all_price     = parseFloat(price * purchase_number);
                var all_tax_price = parseFloat(tax_price* purchase_number);

                purchase_price     += parseFloat(all_price);
                purchase_all_price += parseFloat(all_tax_price);

                //默认使用库存数量
                var use_number = number - purchase_number;
                if (use_number < 0) {
                    use_number = 0;
                }
                $(e).find('.use_stock').text(use_number);
            });
            $('.stat_low_tax_price_all').text(stat_low_tax_price_all.toFixed(2));
            $('.most_low_deliver').text(most_low_deliver);
            $('.stat_short_tax_price_all').text(stat_short_tax_price_all.toFixed(2));
            $('.most_short_deliver').text(most_short_deliver);
            $('.mostLongTime').text(mostLongTime);
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));
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

        //输入数量
        $(".number").bind('input propertychange', function (e) {

            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time:2000});
                return false;
            }
            var a = number.replace(/[^\d]/g,'');
            $(this).val(a);

            var price = $(this).parent().parent().find('.price').text();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

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
            $(this).parent().parent().find('.use_stock').text(use_number);

            var purchase_price     = 0;
            var purchase_all_price = 0;
            var stat_low_tax_price_all  = 0;
            var stat_short_tax_price_all= 0;
            $('.order_agreement_list').each(function (i, e) {
                var all_price       = $(e).find('.all_price').text();
                var all_tax_price   = $(e).find('.all_tax_price').text();
                var low_tax_price_all   = $(e).find('.low_tax_price').text();
                var short_tax_price_all = $(e).find('.short_tax_price').text();
                purchase_price          += parseFloat(all_price);
                purchase_all_price      += parseFloat(all_tax_price);
                stat_low_tax_price_all  += parseFloat(low_tax_price_all);
                stat_short_tax_price_all+= parseFloat(short_tax_price_all);
            });
            $('.purchase_price').text(purchase_price.toFixed(2));
            $('.purchase_all_price').text(purchase_all_price.toFixed(2));
            //对新加的最低和最短做动态变化
            $('.stat_low_tax_price_all').text(stat_low_tax_price_all.toFixed(2));
            $('.stat_short_tax_price_all').text(stat_short_tax_price_all.toFixed(2));
        });

        //保存
        $('.purchase_save').click(function (e) {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }

            var goods_info = [];
            var number_flag = false;
            var supplier_flag = false;
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
                    if (!$(element).parent().parent().find('.number').val()){
                        number_flag  = true;
                    }
                    item.final_goods_id     = $(element).data('final_goods_id');
                    item.goods_id           = $(element).val();
                    item.number             = $(element).parent().parent().find('.number').val();
                    item.type               = $(element).data('type');
                    item.relevance_id       = $(element).data('relevance_id');
                    item.delivery_time      = $(element).parent().parent().find('.delivery_time').text();
                    goods_info.push(item);
                }
            });

            // if (supplier_flag) {
            //     layer.msg('一个支出合同不能有多个供应商', {time:2000});
            //     return false;
            // }

            if (number_flag) {
                layer.msg('请给选中的行输入数量', {time:2000});
                return false;
            }
            var purchase_sn = $('#orderpurchase-purchase_sn').val();
            if (!purchase_sn) {
                layer.msg('请输入采购单号', {time:2000});
                return false;
            }

            var admin_id = $('#orderpurchase-admin_id').val();
            if (!admin_id) {
                layer.msg('请选择采购员', {time:2000});
                return false;
            }

            var end_date = $('#orderpurchase-end_date').val();
            if (!end_date) {
                layer.msg('请输入采购截止时间', {time:2000});
                return false;
            }

            var order_final_id = $('.data').data('order_final_id');

            $.ajax({
                type:"post",
                url:'?r=order-final/save-purchase',
                data:{order_final_id:order_final_id, purchase_sn:purchase_sn, end_date:end_date, admin_id:admin_id, goods_info:goods_info},
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
            location.replace("?r=order-final/create-purchase&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
    });
</script>
