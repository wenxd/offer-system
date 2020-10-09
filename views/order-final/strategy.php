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
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 3000px; table-layout: auto">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
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
            </thead>
            <tbody>
            <?php foreach ($finalGoods as $item):?>
                <tr class="order_agreement_list">
                    <?php
                    $checkbox = false;
                    if (isset($item->goodsRelation)) {
                        $checkbox = true;
                    }
                    ?>
                    <td>
                        <?=!$checkbox ? '' : "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id} data-final_goods_id={$item->id} value={$item->goods_id} class='select_id'>"?>
                    </td>
                    <td><?=$item->serial?></td>
                    <td><?=$checkbox ? '是' : "否"?></td>
                    <td><?=empty($item->belong_to) ? '是' : "否"?></td>
                    <td><?=Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=Html::a($item->goods->goods_number_b, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td class="supplier_name"><?=$item->inquiry->supplier->name?></td>
                    <td><?=Admin::findOne($item->inquiry->admin_id)->username?></td>
                    <td><?=$system_tax?></td>
                    <?php
                    $lowPriceInquiry = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('price asc')->one();
                    $deliverInquiry  = Inquiry::find()->where(['good_id' => $item->goods_id])->orderBy('delivery_time asc')->one();
                    ?>
                    <td class="low_price" style="background-color:#00FF33"><?=$lowPriceInquiry ? $lowPriceInquiry->price : 0?></td>
                    <td class="low_tax_price"><?=$lowPriceInquiry ? number_format($lowPriceInquiry->price * (1 + $system_tax/100) * $item->number, 2, '.', '') : 0?></td>
                    <td class="low_delivery"><?=$lowPriceInquiry ? $lowPriceInquiry->delivery_time : 0?></td>
                    <td class="short_price"><?=$deliverInquiry ? $deliverInquiry->price : 0?></td>
                    <td class="short_tax_price"><?=$deliverInquiry ? number_format($deliverInquiry->price * (1 + $system_tax/100) * $item->number, 2, '.', '') : 0?></td>
                    <td class="short_delivery" style="background-color:#0099FF"><?=$deliverInquiry ? $deliverInquiry->delivery_time : 0?></td>

                    <td class="price"><?=$item->price?></td>
                    <?php
                    $tax_price = number_format($item->price * (1 + $system_tax/100), 2, '.', '');
                    ?>
                    <td class="tax_price"><?=$tax_price?></td>
                    <td class="all_price"><?=$item->price * $item->number?></td>
                    <td class="all_tax_price"><?=number_format($item->price * (1 + $system_tax/100) * $item->number, 2, '.', '')?></td>
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
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <?= Html::button('保存采购策略', [
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
            var select_length = $('.select_id:checked').length;
            // if (!select_length) {
            //     layer.msg('请最少选择一个零件', {time:2000});
            //     return false;
            // }

            var goods_info = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    goods_info.push($(element).val());
                }
            });
            $.ajax({
                type:"post",
                url:'<?=$_SERVER['REQUEST_URI']?>',
                data:{goods_info:goods_info},
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
    });
</script>
