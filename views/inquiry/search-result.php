<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Goods;
use app\models\Stock;

$this->title = '零件询价记录';
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
    .changeColor {
        color : red;
    }
    .stressColor{
        color : #13064b;
    }
    .price {
        color : #13064b;
    }
    .color {
        color: #070dee;
    }
</style>
<section class="content">
    <div class="box table-responsive">
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="2">零件基础数据</th>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>加工</th>
                    <th>特制</th>
                    <th>铭牌</th>
                    <th>总成</th>
                    <th>技术备注</th>
                    <th>更新时间</th>
                    <th>创建时间</th>
                </tr>
                <tr>
                    <td class="data" data-goods_id="<?=$goods ? $goods->id : ''?>" data-order_id="<?=$_GET['order_id'] ?? ''?>" data-key="<?=$_GET['key'] ?? ''?>" data-serial="<?=$_GET['serial'] ?? ''?>">
                        <?=$goods ? $goods->goods_number_b : ''?>
                    </td>
                    <td><?=$goods ? $goods->description : ''?></td>
                    <td><?=$goods ? $goods->description_en : ''?></td>
                    <td><?=$goods ? $goods->original_company : ''?></td>
                    <td><?=$goods ? $goods->original_company_remark : ''?></td>
                    <td><?=$goods ? ($goods->is_process == 1 ? '<b class="color">' . Goods::$process[$goods->is_process] . '</b>' : Goods::$process[$goods->is_process]) : ''?></td>
                    <td><?=$goods ? ($goods->is_special == 1 ? '<b class="color">' . Goods::$special[$goods->is_special] . '</b>' : Goods::$special[$goods->is_special]) : ''?></td>
                    <td><?=$goods ? ($goods->is_assembly == 1 ? '<b class="color">' . Goods::$assembly[$goods->is_assembly] . '</b>' : Goods::$assembly[$goods->is_assembly]) : ''?></td>
                    <td><?=$goods ? ($goods->is_nameplate == 1 ? '<b class="color">' . Goods::$nameplate[$goods->is_nameplate] . '</b>' : Goods::$nameplate[$goods->is_nameplate]) : ''?></td>
                    <td><?=$goods ? $goods->technique_remark : ''?></td>
                    <td><?=$goods ? substr($goods->updated_at, 0, 10) : ''?></td>
                    <td><?=$goods ? substr($goods->created_at, 0, 10) : ''?></td>
                </tr>
                </thead>
            </table>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="5"><?=Html::a('询价记录', Url::to(['inquiry/index', 'InquirySearch[good_id]' => $goods->id]))?></th>
                    <th></th>
                    <th>类型</th>
                    <th>厂家号</th>
                    <th>单位</th>
                    <th>供应商</th>
                    <th>数量</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>货期</th>
                    <th>询价员</th>
                    <th>询价时间</th>
                    <th>优选</th>
                    <th>优选理由</th>
                    <th>备注</th>
                    <th>订单号</th>
                    <th>询价单号</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                    <th>操作</th>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryPrice ? $inquiryPrice->id : ''?>"></td>
                    <td>价格</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->supplier->name : '' ?></td>
                    <td class="number"><?=$inquiryPrice ? $inquiryPrice->number : 0?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryPrice ? $inquiryPrice->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryPrice ? $inquiryPrice->tax_price : 0 ?></td>
                    <td class="stressColor"><?= $inquiryPrice ? $inquiryPrice->delivery_time : 0 ?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->admin_id ? $inquiryPrice->admin->username : '') : '' ?></td>
                    <td><?= $inquiryPrice ? substr($inquiryPrice->inquiry_datetime, 0, 10) : '' ?></td>
                    <td><?= $inquiryPrice ? Inquiry::$better[$inquiryPrice->is_better] : ''?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->better_reason : ''?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->remark : ''?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->order_id ? $inquiryPrice->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->order_inquiry_id ? $inquiryPrice->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>" target="_blank"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryTime ? $inquiryTime->id : ''?>"></td>
                    <td>货期</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryTime ? $inquiryTime->supplier->name : '' ?></td>
                    <td class="number"><?=$inquiryTime ? $inquiryTime->number : 0?></td>
                    <td><?= $inquiryTime ? $inquiryTime->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryTime ? $inquiryTime->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryTime ? $inquiryTime->tax_price : 0 ?></td>
                    <td class="stressColor"><?= $inquiryTime ? $inquiryTime->delivery_time : 0 ?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->admin_id ? $inquiryTime->admin->username : '') : '' ?></td>
                    <td><?= $inquiryTime ? substr($inquiryTime->inquiry_datetime, 0 , 10) : '' ?></td>
                    <td><?= $inquiryTime ? Inquiry::$better[$inquiryTime->is_better] : ''?></td>
                    <td><?= $inquiryTime ? $inquiryTime->better_reason : ''?></td>
                    <td><?= $inquiryTime ? $inquiryTime->remark : ''?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->order_id ? $inquiryTime->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->order_inquiry_id ? $inquiryTime->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>" target="_blank"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryNew ? $inquiryNew->id : ''?>"></td>
                    <td>最新</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryNew ? $inquiryNew->supplier->name : '' ?></td>
                    <td class="number"><?=$inquiryNew ? $inquiryNew->number : 0?></td>
                    <td><?= $inquiryNew ? $inquiryNew->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryNew ? $inquiryNew->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryNew ? $inquiryNew->tax_price : 0 ?></td>
                    <td class="stressColor"><?= $inquiryNew ? $inquiryNew->delivery_time : 0 ?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->admin_id ? $inquiryNew->admin->username : '') : '' ?></td>
                    <td><?= $inquiryNew ? substr($inquiryNew->inquiry_datetime, 0, 10) : '' ?></td>
                    <td><?= $inquiryNew ? Inquiry::$better[$inquiryNew->is_better] : ''?></td>
                    <td><?= $inquiryNew ? $inquiryNew->better_reason : ''?></td>
                    <td><?= $inquiryNew ? $inquiryNew->remark : ''?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->order_id ? $inquiryNew->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->order_inquiry_id ? $inquiryNew->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>" target="_blank"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryBetter ? $inquiryBetter->id : ''?>"></td>
                    <td>优选</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->supplier->name : '' ?></td>
                    <td class="number"><?=$inquiryBetter ? $inquiryBetter->number : 0?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryBetter ? $inquiryBetter->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryBetter ? $inquiryBetter->tax_price : 0 ?></td>
                    <td class="stressColor"><?= $inquiryBetter ? $inquiryBetter->delivery_time : 0 ?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->admin_id ? $inquiryBetter->admin->username : '') : '' ?></td>
                    <td><?= $inquiryBetter ? substr($inquiryBetter->inquiry_datetime, 0, 10) : '' ?></td>
                    <td><?= $inquiryBetter ? Inquiry::$better[$inquiryBetter->is_better] : ''?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->better_reason : ''?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->remark : ''?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->order_id ? $inquiryBetter->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->order_inquiry_id ? $inquiryBetter->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>" target="_blank"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                </thead>
            </table>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="2">库存记录</th>
                    <th>厂家号</th>
                    <th>单位</th>
<!--                    <th>供应商</th>-->
                    <th>数量</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>库存位置</th>
                    <th>建议库存</th>
                    <th>高储</th>
                    <th>低储</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                </tr>
                <tr class="inquiry_list">
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td class="number"><?= $stock ? $stock->number : 0 ?></td>
                    <td><?= $stock ? $stock->tax_rate : 0 ?></td>
                    <td class="price"><?= $stock ? $stock->price : 0 ?></td>
                    <td class="tax_price"><?= $stock ? $stock->tax_price : 0 ?></td>
                    <td><?= $stock ? $stock->position : 0 ?></td>
                    <td><?= $stock ? $stock->suggest_number : 0 ?></td>
                    <td class="high_number"><?= $stock ? $stock->high_number : 0 ?></td>
                    <td class="low_number"><?= $stock ? $stock->low_number : 0 ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                </tr>
                </thead>
            </table>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="4"><?=Html::a('采购记录', Url::to(['purchase-goods/index', 'PurchaseGoodsSearch[goods_id]' => $goods->id]))?></th>
                    <th>类型</th>
                    <th>供应商</th>
                    <th>数量</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>货期</th>
                    <th>采购员</th>
                    <th>支出合同签订时间</th>
                    <th>入库时间</th>
                    <th>实际货期</th>
                    <th>订单号</th>
                    <th>支出合同号</th>
                    <th>含税总价</th>
                </tr>
                <tr class="inquiry_list">
                    <td>最新</td>
                    <td class="stressColor"><?= $paymentNew ? $paymentNew->supplier->name : '' ?></td>
                    <td class="number"><?= $paymentNew ? $paymentNew->number : 0 ?></td>
                    <td><?= $paymentNew ? $paymentNew->tax_rate : 0 ?></td>
                    <td><b class="color"><?= $paymentNew ? $paymentNew->fixed_price : 0 ?></b></td>
                    <td class="tax_price"><?= $paymentNew ? $paymentNew->fixed_tax_price : 0 ?></b></td>
                    <td class="stressColor"><b class="color"><?= $paymentNew ? $paymentNew->delivery_time : 0 ?></b></td>
                    <td><?= $paymentNew ? (isset($adminList[$paymentNew->inquiry_admin_id]) ? $adminList[$paymentNew->inquiry_admin_id]->username : '') : '' ?></td>
                    <td><?= $paymentNew ? ($paymentNew->orderPayment ?  substr($paymentNew->orderPayment->agreement_at, 0, 10): '') : '' ?></td>
                    <td><?= $paymentNew ? ($paymentNew->orderPayment ?  substr($paymentNew->orderPayment->stock_at, 0, 10): '') : '' ?></td>
                    <td>
                        <?php
                        if ($paymentNew && $paymentNew->orderPayment->stock_at) {
                            echo number_format((strtotime($paymentNew->orderPayment->stock_at) - strtotime($paymentNew->orderPayment->agreement_at))/(3600*24), 2, '.', '') . '天';
                        }
                        ?>
                    </td>
                    <td><?= $paymentNew ? Html::a($paymentNew->order->order_sn, Url::to(['order/detail', 'id' => $paymentNew->order_id])) : ''?></td>
                    <td><?= $paymentNew ? Html::a($paymentNew->order_payment_sn, Url::to(['order-payment/detail', 'id' => $paymentNew->order_payment_id])) : ''?></td>
                    <td><?=$paymentNew ? $paymentNew->number * $paymentNew->fixed_tax_price : 0 ?></td>
                </tr>
                <tr class="inquiry_list">
                    <td>价格</td>
                    <td class="stressColor"><?= $paymentPrice ? $paymentPrice->supplier->name : '' ?></td>
                    <td class="number"><?= $paymentPrice ? $paymentPrice->number : 0 ?></td>
                    <td><?= $paymentPrice ? $paymentPrice->tax_rate : 0 ?></td>
                    <td><b class="color"><?= $paymentPrice ? $paymentPrice->fixed_price : 0 ?></b></td>
                    <td class="tax_price"><?= $paymentPrice ? $paymentPrice->fixed_tax_price : 0 ?></td>
                    <td class="stressColor"><b class="color"><?= $paymentPrice ? $paymentPrice->delivery_time : 0 ?></b></td>
                    <td><?= $paymentPrice ? (isset($adminList[$paymentPrice->inquiry_admin_id]) ? $adminList[$paymentPrice->inquiry_admin_id]->username : '') : '' ?></td>
                    <td><?= $paymentPrice ? ($paymentPrice->orderPayment ?  substr($paymentPrice->orderPayment->agreement_at, 0, 10): '') : '' ?></td>
                    <td><?= $paymentPrice ? ($paymentPrice->orderPayment ?  substr($paymentPrice->orderPayment->stock_at, 0, 10): '') : '' ?></td>
                    <td>
                        <?php
                        if ($paymentPrice && $paymentPrice->orderPayment->stock_at) {
                            echo number_format((strtotime($paymentPrice->orderPayment->stock_at) - strtotime($paymentPrice->orderPayment->agreement_at))/(3600*24), 2, '.', '') . '天';
                        }
                        ?>
                    </td>
                    <td><?= $paymentPrice ? Html::a($paymentPrice->order->order_sn, Url::to(['order/detail', 'id' => $paymentPrice->order_id])) : ''?></td>
                    <td><?= $paymentPrice ? Html::a($paymentPrice->order_payment_sn, Url::to(['order-payment/detail', 'id' => $paymentPrice->order_payment_id])) : ''?></td>
                    <td><?=$paymentPrice ? $paymentPrice->number * $paymentPrice->fixed_tax_price : 0 ?></td>
                </tr>
                <tr class="inquiry_list">
                    <td>货期</td>
                    <td class="stressColor"><?= $paymentDay ? $paymentDay->supplier->name : '' ?></td>
                    <td class="number"><?= $paymentDay ? $paymentDay->number : 0 ?></td>
                    <td><?= $paymentDay ? $paymentDay->tax_rate : 0 ?></td>
                    <td><b class="color"><?= $paymentDay ? $paymentDay->fixed_price : 0 ?></b></td>
                    <td class="tax_price"><?= $paymentDay ? $paymentDay->fixed_tax_price : 0 ?></td>
                    <td class="stressColor"><b class="color"><?= $paymentDay ? $paymentDay->delivery_time : 0 ?></b></td>
                    <td><?= $paymentDay ? (isset($adminList[$paymentDay->inquiry_admin_id]) ? $adminList[$paymentDay->inquiry_admin_id]->username : '') : '' ?></td>
                    <td><?= $paymentDay ? ($paymentDay->orderPayment ?  substr($paymentDay->orderPayment->agreement_at, 0, 10): '') : '' ?></td>
                    <td><?= $paymentDay ? ($paymentDay->orderPayment ?  substr($paymentDay->orderPayment->stock_at, 0, 10): '') : '' ?></td>
                    <td>
                        <?php
                        if ($paymentDay && $paymentDay->orderPayment->stock_at) {
                            echo number_format((strtotime($paymentDay->orderPayment->stock_at) - strtotime($paymentDay->orderPayment->agreement_at))/(3600*24), 2, '.', '') . '天';
                        }
                        ?>
                    </td>
                    <td><?= $paymentDay ? Html::a($paymentDay->order->order_sn, Url::to(['order/detail', 'id' => $paymentDay->order_id])) : ''?></td>
                    <td><?= $paymentDay ? Html::a($paymentDay->order_payment_sn, Url::to(['order-payment/detail', 'id' => $paymentDay->order_payment_id])) : ''?></td>
                    <td><?= $paymentDay ? $paymentDay->number * $paymentDay->fixed_tax_price : 0 ?></td>
                </tr>
                </thead>
            </table>
        </div>
        <div class="box-footer">
            <?= Html::button('关联最终订单', [
                    'class' => 'btn btn-success relevance_save',
                    'name'  => 'submit-button']
            )?>
        </div>
    </div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        $('.inquiry_list').each(function (i, e) {
            var number = $(e).find('.number').text();
            var price = $(e).find('.price').text();
            var tax_price = $(e).find('.tax_price').text();
            $(e).find('.all_price').text(number * price);
            $(e).find('.all_tax_price').text(number * tax_price);
        });

        $('.relevance_save').click(function (e) {
            var a = $("[name=relevance]:checked").val();
            if (!a) {
                layer.msg('请选择一个记录关联', {time:2000});
                return false;
            }
            var type      = '';
            var select_id = 0;
            $('.relevance').each(function (i, element) {
                if ($(this).is(":checked")) {
                    type      = $(element).data("type");
                    select_id = $(element).data("select_id");
                }
            });
            if (!select_id) {
                layer.msg('请先添加记录', {time:2000});
                return false;
            }
            var goods_id = $('.data').data('goods_id');
            var order_id = $('.data').data('order_id');
            var key      = $('.data').data('key');
            var serial   = $('.data').data('serial');

            $.ajax({
                type:"post",
                url:'?r=order-final/relevance',
                data:{type:type, select_id:select_id, goods_id:goods_id, order_id:order_id, key:key, serial:serial},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order/create-final&id=" + order_id + '&key=' + key);
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        function init(){
            var stock_number = $('.stock_list').find('.number').text();
            var high_number  = $('.stock_list').find('.high_number').text();
            var low_number   = $('.stock_list').find('.low_number').text();

            if (stock_number > high_number || stock_number < low_number) {
                $('.stock_list').find('.number').addClass('changeColor');
            }
        }

    });
</script>

