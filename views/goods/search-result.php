<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Goods;
use app\models\Stock;

$this->title = '零件管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .but button{
        float: right;
    }
    .but a{
        float: right;
        margin-right: 10px;
    }
</style>
<div class="box table-responsive">
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="2">零件基础数据</th>
                <th>零件号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>厂家备注</th>
                <th>加工</th>
                <th>特制</th>
                <th>铭牌</th>
                <th>技术备注</th>
                <th>更新时间</th>
                <th>创建时间</th>
            </tr>
            <tr>
                <td><?=$goods ? $goods->goods_number : ''?></td>
                <td><?=$goods ? $goods->description : ''?></td>
                <td><?=$goods ? $goods->description_en : ''?></td>
                <td><?=$goods ? $goods->original_company : ''?></td>
                <td><?=$goods ? $goods->original_company_remark : ''?></td>
                <td><?=$goods ? Goods::$process[$goods->is_process] : ''?></td>
                <td><?=$goods ? Goods::$special[$goods->is_special] : ''?></td>
                <td><?=$goods ? Goods::$nameplate[$goods->is_nameplate] : ''?></td>
                <td><?=$goods ? $goods->technique_remark : ''?></td>
                <td><?=$goods ? substr($goods->updated_at, 0, 10) : ''?></td>
                <td><?=$goods ? substr($goods->created_at, 0, 10) : ''?></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="5">询价记录</th>
                <th>类型</th>
                <th>零件号</th>
                <th>单位</th>
                <th>供应商</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期</th>
                <th>询价员</th>
                <th>询价时间</th>
                <th>是否优选</th>
                <th>优选理由</th>
                <th>备注</th>
                <th>订单号</th>
                <th>询价单号</th>
                <th>未税总价</th>
                <th>含税总价</th>
            </tr>
            <tr class="inquiry_list">
                <td>价格</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $inquiryPrice ? $inquiryPrice->supplier->name : '' ?></td>
                <td class="number">1</td>
                <td><?= $inquiryPrice ? $inquiryPrice->tax_rate : 0 ?></td>
                <td class="price"><?= $inquiryPrice ? $inquiryPrice->price : 0 ?></td>
                <td class="tax_price"><?= $inquiryPrice ? $inquiryPrice->tax_price : 0 ?></td>
                <td><?= $inquiryPrice ? $inquiryPrice->delivery_time : 0 ?></td>
                <td><?= $inquiryPrice ? ($inquiryPrice->admin_id ? $inquiryPrice->admin->username : '') : '' ?></td>
                <td><?= $inquiryPrice ? substr($inquiryPrice->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryPrice ? Inquiry::$better[$inquiryPrice->is_better] : ''?></td>
                <td><?= $inquiryPrice ? $inquiryPrice->better_reason : ''?></td>
                <td><?= $inquiryPrice ? $inquiryPrice->remark : ''?></td>
                <td><?= $inquiryPrice ? Html::a($inquiryPrice->order_id ? $inquiryPrice->order->order_sn : '', $inquiryPrice->order_id ? Url::to(['order/detail', 'id' => $inquiryPrice->order_id]) : '') : '' ?></td>
                <td><?= $inquiryPrice ? Html::a($inquiryPrice->order_inquiry_id ? $inquiryPrice->orderInquiry->inquiry_sn : '', $inquiryPrice->order_inquiry_id ? Url::to(['order-inquiry/view', 'id' => $inquiryPrice->order_inquiry_id]) : '') : '' ?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            <tr class="inquiry_list">
                <td>货期</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $inquiryTime ? $inquiryTime->supplier->name : '' ?></td>
                <td class="number">1</td>
                <td><?= $inquiryTime ? $inquiryTime->tax_rate : 0 ?></td>
                <td class="price"><?= $inquiryTime ? $inquiryTime->price : 0 ?></td>
                <td class="tax_price"><?= $inquiryTime ? $inquiryTime->tax_price : 0 ?></td>
                <td><?= $inquiryTime ? $inquiryTime->delivery_time : 0 ?></td>
                <td><?= $inquiryTime ? ($inquiryTime->admin_id ? $inquiryTime->admin->username : '') : '' ?></td>
                <td><?= $inquiryTime ? substr($inquiryTime->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryTime ? Inquiry::$better[$inquiryTime->is_better] : ''?></td>
                <td><?= $inquiryTime ? $inquiryTime->better_reason : ''?></td>
                <td><?= $inquiryTime ? $inquiryTime->remark : ''?></td>
                <td><?= $inquiryTime ? Html::a($inquiryTime->order_id ? $inquiryTime->order->order_sn : '', $inquiryTime->order_id ? Url::to(['order/detail', 'id' => $inquiryTime->order_id]) : '') : '' ?></td>
                <td><?= $inquiryTime ? Html::a($inquiryTime->order_inquiry_id ? $inquiryTime->orderInquiry->inquiry_sn : '', $inquiryTime->order_inquiry_id ? Url::to(['order-inquiry/view', 'id' => $inquiryTime->order_inquiry_id]) : '') : '' ?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            <tr class="inquiry_list">
                <td>最新</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $inquiryNew ? $inquiryNew->supplier->name : '' ?></td>
                <td class="number">1</td>
                <td><?= $inquiryNew ? $inquiryNew->tax_rate : 0 ?></td>
                <td class="price"><?= $inquiryNew ? $inquiryNew->price : 0 ?></td>
                <td class="tax_price"><?= $inquiryNew ? $inquiryNew->tax_price : 0 ?></td>
                <td><?= $inquiryNew ? $inquiryNew->delivery_time : 0 ?></td>
                <td><?= $inquiryNew ? ($inquiryNew->admin_id ? $inquiryNew->admin->username : '') : '' ?></td>
                <td><?= $inquiryNew ? substr($inquiryNew->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryNew ? Inquiry::$better[$inquiryNew->is_better] : ''?></td>
                <td><?= $inquiryNew ? $inquiryNew->better_reason : ''?></td>
                <td><?= $inquiryNew ? $inquiryNew->remark : ''?></td>
                <td><?= $inquiryNew ? Html::a($inquiryNew->order_id ? $inquiryNew->order->order_sn : '', $inquiryNew->order_id ? Url::to(['order/detail', 'id' => $inquiryNew->order_id]) : '') : '' ?></td>
                <td><?= $inquiryNew ? Html::a($inquiryNew->order_inquiry_id ? $inquiryNew->orderInquiry->inquiry_sn : '', $inquiryNew->order_inquiry_id ? Url::to(['order-inquiry/view', 'id' => $inquiryNew->order_inquiry_id]) : '') : '' ?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            <tr class="inquiry_list">
                <td>优选</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $inquiryBetter ? $inquiryBetter->supplier->name : '' ?></td>
                <td class="number">1</td>
                <td><?= $inquiryBetter ? $inquiryBetter->tax_rate : 0 ?></td>
                <td class="price"><?= $inquiryBetter ? $inquiryBetter->price : 0 ?></td>
                <td class="tax_price"><?= $inquiryBetter ? $inquiryBetter->tax_price : 0 ?></td>
                <td><?= $inquiryBetter ? $inquiryBetter->delivery_time : 0 ?></td>
                <td><?= $inquiryBetter ? ($inquiryBetter->admin_id ? $inquiryBetter->admin->username : '') : '' ?></td>
                <td><?= $inquiryBetter ? substr($inquiryBetter->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryBetter ? Inquiry::$better[$inquiryBetter->is_better] : ''?></td>
                <td><?= $inquiryBetter ? $inquiryBetter->better_reason : ''?></td>
                <td><?= $inquiryBetter ? $inquiryBetter->remark : ''?></td>
                <td><?= $inquiryBetter ? Html::a($inquiryBetter->order_id ? $inquiryBetter->order->order_sn : '', $inquiryBetter->order_id ? Url::to(['order/detail', 'id' => $inquiryBetter->order_id]) : '') : '' ?></td>
                <td><?= $inquiryBetter ? Html::a($inquiryBetter->order_inquiry_id ? $inquiryBetter->orderInquiry->inquiry_sn : '', $inquiryBetter->order_inquiry_id ? Url::to(['order-inquiry/view', 'id' => $inquiryBetter->order_inquiry_id]) : '') : '' ?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="2">库存记录</th>
                <th>类型</th>
                <th>零件号</th>
                <th>单位</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>库存位置</th>
                <th>是否紧急</th>
                <th>建议库存</th>
                <th>高储</th>
                <th>低储</th>
                <th>未税总价</th>
                <th>含税总价</th>
            </tr>
            <tr class="inquiry_list">
                <td>库存记录</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td class="number"><?= $stock ? $stock->number : 0 ?></td>
                <td><?= $stock ? $stock->tax_rate : 0 ?></td>
                <td class="price"><?= $stock ? $stock->price : 0 ?></td>
                <td class="tax_price"><?= $stock ? $stock->tax_price : 0 ?></td>
                <td><?= $stock ? $stock->position : 0 ?></td>
                <td><?= $stock ? Stock::$emerg[$stock->is_emerg] : '' ?></td>
                <td><?= $stock ? $stock->suggest_number : 0 ?></td>
                <td><?= $stock ? $stock->high_number : 0 ?></td>
                <td><?= $stock ? $stock->low_number : 0 ?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="4">采购记录</th>
                <th>类型</th>
                <th>零件号</th>
                <th>单位</th>
                <th>供应商</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期</th>
                <th>采购员</th>
                <th>采购时间</th>
                <th>入库时间</th>
                <th>订单号</th>
                <th>采购单号</th>
                <th>未税总价</th>
                <th>含税总价</th>
            </tr>
            <tr class="inquiry_list">
                <td>价格</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $purchasePrice ? ($purchasePrice->type ? $purchasePrice->stock->supplier->name : $purchasePrice->inquiry->supplier->name) : '' ?></td>
                <td class="number"><?= $purchasePrice ? $purchasePrice->number : 0 ?></td>
                <td><?= $purchasePrice ? ($purchasePrice->type ? $purchasePrice->stock->tax_rate : $purchasePrice->inquiry->tax_rate) : 0 ?></td>
                <td class="price"><?= $purchasePrice ? ($purchasePrice->type ? $purchasePrice->stock->price : $purchasePrice->inquiry->price) : 0 ?></td></td>
                <td class="tax_price"><?= $purchasePrice ? ($purchasePrice->type ? $purchasePrice->stock->tax_price : $purchasePrice->inquiry->tax_price) : 0 ?></td>
                <td></td>
                <td><?= $purchasePrice ? $purchasePrice->orderPurchase->admin->username : '' ?></td>
                <td><?= $purchasePrice ? substr($purchasePrice->created_at, 0, 10) : '' ?></td>
                <td><?= $purchasePrice ? substr($purchasePrice->orderPurchase->updated_at, 0, 10) : '' ?></td>
                <td><?=$purchasePrice ? Html::a($purchasePrice->order->order_sn, Url::to(['order/detail', 'id' => $purchasePrice->order_id])) : ''?></td>
                <td><?=$purchasePrice ? Html::a($purchasePrice->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $purchasePrice->order_purchase_id])) : ''?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            <tr class="inquiry_list">
                <td>货期</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $purchaseDay ? ($purchaseDay->type ? $purchaseDay->stock->supplier->name : $purchaseDay->inquiry->supplier->name) : '' ?></td>
                <td class="number"><?= $purchaseDay ? $purchaseDay->number : 0 ?></td>
                <td><?= $purchaseDay ? ($purchaseDay->type ? $purchaseDay->stock->tax_rate : $purchaseDay->inquiry->tax_rate) : 0 ?></td>
                <td class="price"><?= $purchaseDay ? ($purchaseDay->type ? $purchaseDay->stock->price : $purchaseDay->inquiry->price) : 0 ?></td></td>
                <td class="tax_price"><?= $purchaseDay ? ($purchaseDay->type ? $purchaseDay->stock->tax_price : $purchaseDay->inquiry->tax_price) : 0 ?></td>
                <td><?= $purchaseDay ? $purchaseDay->inquiry->delivery_time : '' ?></td>
                <td><?= $purchaseDay ? $purchaseDay->orderPurchase->admin->username : '' ?></td>
                <td><?= $purchaseDay ? substr($purchaseDay->created_at, 0, 10) : '' ?></td>
                <td><?= $purchaseDay ? substr($purchaseDay->orderPurchase->updated_at,0, 10) : '' ?></td>
                <td><?= $purchaseDay ? Html::a($purchaseDay->order->order_sn, Url::to(['order/detail', 'id' => $purchaseDay->order_id])) : ''?></td>
                <td><?= $purchaseDay ? Html::a($purchaseDay->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $purchaseDay->order_purchase_id])) : ''?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            <tr class="inquiry_list">
                <td>最新</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?= $goods ? $goods->unit : '' ?></td>
                <td><?= $purchaseNew ? ($purchaseNew->type ? $purchaseNew->stock->supplier->name : $purchaseNew->inquiry->supplier->name) : '' ?></td>
                <td class="number"><?= $purchaseNew ? $purchaseNew->number : 0 ?></td>
                <td><?= $purchaseNew ? ($purchaseNew->type ? $purchaseNew->stock->tax_rate : $purchaseNew->inquiry->tax_rate) : 0 ?></td>
                <td class="price"><?= $purchaseNew ? ($purchaseNew->type ? $purchaseNew->stock->price : $purchaseNew->inquiry->price) : 0 ?></td></td>
                <td class="tax_price"><?= $purchaseNew ? ($purchaseNew->type ? $purchaseNew->stock->tax_price : $purchaseNew->inquiry->tax_price) : 0 ?></td>
                <td><?= $purchaseNew ? $purchaseNew->inquiry->delivery_time : '' ?></td>
                <td><?= $purchaseNew ? $purchaseNew->orderPurchase->admin->username : '' ?></td>
                <td><?= $purchaseNew ? substr($purchaseNew->created_at, 0, 10) : '' ?></td>
                <td><?= $purchaseNew ? substr($purchaseNew->orderPurchase->updated_at, 0, 10) : '' ?></td>
                <td><?= $purchaseNew ? Html::a($purchaseNew->order->order_sn, Url::to(['order/detail', 'id' => $purchaseNew->order_id])) : ''?></td>
                <td><?= $purchaseNew ? Html::a($purchaseNew->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $purchaseNew->order_purchase_id])) : ''?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="2">竞争对手记录</th>
                <th>类型</th>
                <th>零件号</th>
                <th>竞争对手</th>
                <th>针对客户</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期</th>
                <th>报价时间</th>
                <th>备注</th>
            </tr>
            <tr>
                <td>对手记录</td>
                <td><?= $goods ? $goods->goods_number : '' ?></td>
                <td><?=$competitorGoods ? $competitorGoods->competitor->name : ''?></td>
                <td></td>
                <td><?=$competitorGoods ? $competitorGoods->tax_rate : ''?></td>
                <td><?=$competitorGoods ? $competitorGoods->price : ''?></td>
                <td><?=$competitorGoods ? $competitorGoods->tax_price : ''?></td>
                <td></td>
                <td><?=$competitorGoods ? substr($competitorGoods->offer_date, 0, 10) : ''?></td>
                <td><?=$competitorGoods ? $competitorGoods->remark : ''?></td>
            </tr>
            </thead>
        </table>
    </div>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.inquiry_list').each(function (i, e) {
            var number = $(e).find('.number').text();
            var price = $(e).find('.price').text();
            var tax_price = $(e).find('.tax_price').text();
            $(e).find('.all_price').text(number * price);
            $(e).find('.all_tax_price').text(number * tax_price);
        });
    });
</script>
