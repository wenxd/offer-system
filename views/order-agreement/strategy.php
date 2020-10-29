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

$this->title = '生成采购策略';
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
                <?=Html::button('一键走库存', ['class' => 'btn btn-primary btn-flat', 'onclick' => 'exit_stock()'])?>
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
        <table id="example2" class="table table-bordered table-hover" style="table-layout: auto">
            <thead class="data" data-order_agreement_id="<?= $_GET['id'] ?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <th>总成</th>
                <th>拆分</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>供应商</th>
                <!--                <th>询价员</th>-->
                <!--                <th>税率</th>-->
                <!--                <th>最低未税单价</th>-->
                <!--                <th>最低含税总价</th>-->
                <!--                <th>最低货期</th>-->
                <!--                <th>货期最短未税单价</th>-->
                <!--                <th>货期最短含税总价</th>-->
                <!--                <th>货期最短货期</th>-->
                <!--                <th>采购未税单价</th>-->
                <!--                <th>采购未税总价</th>-->
                <!--                <th>采购含税单价</th>-->
                <!--                <th>采购含税总价</th>-->
                <!--                <th>采购货期</th>-->
                <th>采购单号</th>
                <th>合同货期</th>
                <th>合同需求数量</th>
                <th>采购数量</th>
                <th>单位</th>
                <th>使用库存数量</th>
                <th>临时库存数量</th>
                <th>库存数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item): ?>
                <tr>
                    <?php
                    $checkbox = false;
                    if (isset($item->goodsRelation)) {
                        $checkbox = true;
                    }
                    $order_purchase_sn = '';
                    $purchase_number = 0;
                    if (isset($purchaseGoods[$item->goods_id])) {
                        $purchaseGoodsList = $purchaseGoods[$item->goods_id];
                        foreach ($purchaseGoodsList as $k => $v) {
                            if ($v['serial'] == $item->serial && $v['goods_id'] == $item->goods_id) {
                                $order_purchase_sn = $v['order_purchase_sn'];
                                $purchase_number = $v['fixed_number'];
                            }
                        }
                    }
                    ?>
                    <td>
                        <?= $checkbox ? "<input type='checkbox' number={$item->purchase_number} name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-agreement_goods_id={$item->id} value={$item->goods_id} class='select_id'>" : "" ?>
                    </td>
                    <td><?= $item->goods_id ?></td>
                    <td><?= $checkbox ? '是' : "否" ?></td>
                    <td><?= empty($item->belong_to) ? '是' : "否" ?></td>
                    <td><?= Html::a($item->goods->goods_number . ' ' . $item->goods->material_code, Url::to(['goods/search-result', 'goods_id' => $item->goods->id])) ?></td>
                    <td><?= Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'goods_id' => $item->goods->id])) ?></td>
                    <td><?= $item->goods->description ?></td>
                    <td><?= $item->goods->description_en ?></td>
                    <td><?= $item->goods->original_company ?></td>
                    <td><?= $item->goods->original_company_remark ?></td>
                    <td class="supplier_name"><?= $item->inquiry->supplier->name ?></td>
                    <!--<td><? /*=Admin::findOne($item->inquiry_admin_id)->username*/ ?></td>
                    <td><? /*=$item->tax_rate*/ ?></td>
                    <?php
                    /*                    $lowPriceInquiry = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('price asc')->one();
                                        $deliverInquiry  = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('delivery_time asc')->one();
                                        */ ?>
                    <td class="low_price" style="background-color:#00FF33"><? /*=$lowPriceInquiry ? $lowPriceInquiry->price : 0*/ ?></td>
                    <td class="low_tax_price"><? /*=$lowPriceInquiry ? ($lowPriceInquiry->price * (1 + $system_tax/100)) * $item->number  : 0*/ ?></td>
                    <td class="low_delivery"><? /*=$lowPriceInquiry ? $lowPriceInquiry->delivery_time : 0*/ ?></td>
                    <td class="short_price"><? /*=$deliverInquiry ? $deliverInquiry->price : 0*/ ?></td>
                    <td class="short_tax_price"><? /*=$deliverInquiry ? ($deliverInquiry->price * (1 + $system_tax/100)) * $item->number  : 0*/ ?></td>
                    <td class="short_delivery" style="background-color:#0099FF"><? /*=$deliverInquiry ? $deliverInquiry->delivery_time : 0*/ ?></td>
                    <td class="price" style="background-color:#00FF33"><? /*=$item->price*/ ?></td>
                    <td class="all_price"><? /*=$item->all_price*/ ?></td>
                    <td class="tax_price"><? /*=$item->tax_price*/ ?></td>
                    <td class="all_tax_price"><? /*=number_format($item->price * (1+$system_tax/100) * $item->purchase_number, 2, '.', '')*/ ?></td>
                    <td class="delivery_time" style="background-color:#0099FF"><?= $item->delivery_time ?></td>-->
                    <td><?= $order_purchase_sn ?></td>
                    <td class="quote_delivery_time"><?= $item->quote_delivery_time ?></td>
                    <td class="oldNumber"><?= $item->order_number ?></td>
                    <?php if ($orderAgreement->is_strategy_number == 1) :?>
                        <td class="afterNumber">
                            <input goods_id="<?=$item->goods_id?>" type="number" size="4" class="number" min="1" style="width: 50px;"
                                   value="<?= $item->strategy_number ?>">
                        </td>
                        <td><?= $item->goods->unit ?></td>
                        <td class="use_stock">
                            <!--计算库存-->
                            <?php
                                if ($item->strategy_number == 0 ) {
                                    echo $item->number;
                                } elseif ($item->strategy_number < $item->number) {
                                    echo $item->number - $item->strategy_number;
                                } else {
                                    echo 0;
                                }
                            ?>
                        </td>
                    <?php else:;?>
                        <td class="afterNumber">
                            <input goods_id="<?=$item->goods_id?>" type="number" size="4" class="number" min="1" style="width: 50px;"
                                   value="<?= $item->number ?>">
                        </td>
                        <td><?= $item->goods->unit ?></td>
                        <td class="use_stock">0</td>
                    <?php endif;?>
                    <td class="stock_number"><?= $item->stock ? $item->stock->temp_number : 0 ?></td>
                    <td><?= $item->stock ? $item->stock->number : 0 ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="box-footer">
        <?= Html::button('保存采购数量/使用库存', ['class' => 'btn btn-primary strategy_save', 'name' => 'submit-button']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <?php
        // 保存采购数量/使用库存保存后才允许显示
        if ($orderAgreement->is_strategy_number == 1) {
            // 查询是否有未确认使用库存列表
            $count = \app\models\AgreementStock::find()
                ->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $orderAgreement->id, 'is_confirm' => \app\models\AgreementStock::IS_CONFIRM_NO])
                ->count();
            if (!$count) {
                // 判断是不是有支出合同产生
                if (!(\app\models\OrderPayment::find()->where(['order_id' => $orderAgreement->order_id])->count())) {
                    echo Html::button('保存采购策略', ['class' => 'btn btn-success purchase_save', 'name' => 'submit-button']);
                }
            } else {
                echo "<p class='text-danger'>使用库存未确认 * {$count}</p>";
            }
        }
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //输入数量.children()
        $(".number").bind('input propertychange', function (e) {
            // 采购的数量 checkbox_1
            var number = $(this).val();
            if (number < 1) {
                number = 0;
                $(this).val(0);
            }
            $(this).parent().parent().children().find('.select_id').attr("number", number);
            // console.log($(this).parent().parent().children().find('.select_id').html());
            //合同需求数量
            var oldNumber = parseFloat($(this).parent().parent().find('.oldNumber').text());
            // 库存数量
            var stock_number = parseFloat($(this).parent().parent().find('.stock_number').text());
            var use_number = 0;
            // 如果采购数量小于需求数据则计算库存
            if (number < oldNumber) {
                use_number = oldNumber - number;
                // 如果库存小于差额使用库存数量
                if (stock_number < use_number) {
                    layer.msg('库存不足', {time: 1000});
                    use_number = 0;
                    $(this).val(oldNumber);
                }
            }
            $(this).parent().parent().find('.use_stock').text(use_number);
        });
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

        //保存采购数量/使用库存
        $('.strategy_save').click(function (e) {
            //防止双击
            // $(".purchase_save").attr("disabled", true).addClass("disabled");
            var goods_info = [];
            $('.number').each(function (index, element) {
                var goods_id = $(element).attr('goods_id');
                var strategy_number = $(element).val();
                var goods = [];
                goods_info.push({goods_id:goods_id,strategy_number:strategy_number});
            });
            console.log(goods_info);
            $.ajax({
               type:"post",
               url:'<?=Url::to(['save-strategy-number', 'id' => $id])?>',
               data:{goods_info:goods_info},
               dataType:'JSON',
               success:function(res){
                   if (res && res.code == 200){
                       layer.msg(res.msg, {time:2000});
                       window.location.reload();
                   } else {
                       layer.msg(res.msg, {time:2000});
                   }
               }
            });
        });

        //保存采购策略
        $('.purchase_save').click(function (e) {
            //防止双击
            // $(".purchase_save").attr("disabled", true).addClass("disabled");
            var goods_info = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    goods_info.push($(element).val());
                }
            });
            console.log(goods_info);
            $.ajax({
                type:"post",
                url:'<?=$_SERVER['REQUEST_URI']?>',
                data:{goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.history.back();
                    } else {
                        layer.msg(res.msg, {time:2000});
                    }
                }
            });
        });
    });
</script>
