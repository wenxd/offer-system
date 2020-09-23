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
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$model->purchase_sn    = 'B' . date('ymd_') . $number;
$model->agreement_date = substr($orderAgreement->agreement_date, 0, 10);

$system_tax = SystemConfig::find()->select('value')->where([
    'is_deleted' => 0,
    'title'      => SystemConfig::TITLE_TAX,
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
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 3000px; table-layout: auto">
            <thead class="data" data-order_agreement_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <th>总成</th>
                <th>拆分</th>
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
                <th>采购未税总价</th>
                <th>采购含税单价</th>
                <th>采购含税总价</th>
                <th>采购货期</th>
                <th>采购单号</th>
                <th>合同货期</th>
                <th>合同需求数量</th>
                <th>采购数量</th>
                <th>单位</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
                <tr class="order_agreement_list">
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
                                $checkbox           = false;
                                $order_purchase_sn  = $v['order_purchase_sn'];
                                $purchase_number    = $v['fixed_number'];
                            }
                        }
                    }
                    ?>
                    <td>
                        <?=$checkbox ? "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-agreement_goods_id={$item->id} value={$item->goods_id} class='select_id'>" : ""?>
                    </td>
                    <td><?=$item->goods_id?></td>
                    <td><?=$checkbox ? '是' : "否"?></td>
                    <td><?=empty($item->belong_to) ? '是' : "否"?></td>
                    <td><?=Html::a($item->goods->goods_number . ' ' . $item->goods->material_code, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td class="supplier_name"><?=$item->inquiry->supplier->name?></td>
                    <td><?=Admin::findOne($item->inquiry_admin_id)->username?></td>
                    <td><?=$item->tax_rate?></td>
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
                    <td class="price" style="background-color:#00FF33"><?=$item->price?></td>
                    <td class="all_price"><?=$item->all_price?></td>
                    <td class="tax_price"><?=$item->tax_price?></td>
                    <td class="all_tax_price"><?=number_format($item->price * (1+$system_tax/100) * $item->purchase_number, 2, '.', '')?></td>
                    <td class="delivery_time" style="background-color:#0099FF"><?=$item->delivery_time?></td>
                    <td><?=$order_purchase_sn?></td>
                    <td class="quote_delivery_time"><?=$item->quote_delivery_time?></td>
                    <td class="oldNumber"><?=$item->order_number?></td>
                    <td class="afterNumber">
                        <?=$item->purchase_number?>
                    </td>
                    <td><?=$item->goods->unit?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <?= Html::button('保存购策略', [
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

        //保存
        $('.purchase_save').click(function (e) {
            //防止双击
            // $(".purchase_save").attr("disabled", true).addClass("disabled");
            var goods_info              = [];
            var number_flag             = false;
            var supplier_flag           = false;
            var flag_stock              = false;
            var purchase_number_flag    = false;
            var supplier_name           = '';
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    goods_info.push($(element).val());
                }
            });
            var order_agreement_id = $('.data').data('order_agreement_id');
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
