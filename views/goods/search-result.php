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
<div class="box table-responsive">
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="2">零件基础数据</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>厂家备注</th>
                <th>单位</th>
                <th>发行未税单价</th>
                <th>发行货期</th>
                <th>加工</th>
                <th>特制</th>
                <th>总成</th>
                <th>铭牌</th>
                <th>更新时间</th>
                <th>创建时间</th>
            </tr>
            <tr>
                <td><?=$goods ? $goods->goods_number : ''?></td>
                <td><?=$goods ? $goods->goods_number_b : ''?></td>
                <td><?=$goods ? $goods->description : ''?></td>
                <td><?=$goods ? $goods->description_en : ''?></td>
                <td><?=$goods ? $goods->original_company : ''?></td>
                <td><?=$goods ? $goods->original_company_remark : ''?></td>
                <td><?=$goods ? $goods->unit : ''?></td>
                <td><?=$goods ? $goods->publish_price : 0?></td>
                <td><?=$goods ? $goods->publish_delivery_time : ''?></td>
                <td><?=$goods ? ($goods->is_process == 1 ? '<b class="color">' . Goods::$process[$goods->is_process] . '</b>' : Goods::$process[$goods->is_process]) : ''?></td>
                <td><?=$goods ? ($goods->is_special == 1 ? '<b class="color">' . Goods::$special[$goods->is_special] . '</b>' : Goods::$special[$goods->is_special]) : ''?></td>
                <td><?=$goods ? ($goods->is_assembly == 1 ? '<b class="color">' . Goods::$assembly[$goods->is_assembly] . '</b>' : Goods::$assembly[$goods->is_assembly]) : ''?></td>
                <td><?=$goods ? ($goods->is_nameplate == 1 ? '<b class="color">' . Goods::$nameplate[$goods->is_nameplate] . '</b>' : Goods::$nameplate[$goods->is_nameplate]) : ''?></td>
                <td><?=$goods ? substr($goods->updated_at, 0, 10) : ''?></td>
                <td><?=$goods ? substr($goods->created_at, 0, 10) : ''?></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="2">库存记录</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>库存位置</th>
                <th>建议库存</th>
                <th>高储</th>
                <th>低储</th>
                <th>含税总价</th>
            </tr>
            <tr class="inquiry_list stock_list">
                <td><b><?= $stock ? $stock->number : 0 ?></b></td>
                <td><?= $stock ? $stock->tax_rate : 0 ?></td>
                <td><b class="color"><?= $stock ? $stock->price : 0 ?></b></td>
                <td class="tax_price"><?= $stock ? $stock->tax_price : 0 ?></td>
                <td><?= $stock ? $stock->position : 0 ?></td>
                <td><?= $stock ? $stock->suggest_number : 0 ?></td>
                <td class="high_number"><?= $stock ? $stock->high_number : 0 ?></td>
                <td class="low_number"><?= $stock ? $stock->low_number : 0 ?></td>
                <td><?= $stock ? ($stock->number * $stock->tax_price) : 0 ?></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="5"><?=Html::a('询价记录', Url::to(['inquiry/index', 'InquirySearch[good_id]' => $goods->id]))?></th>
                <th>类型</th>
                <th>供应商</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>是否采购记录</th>
                <th>货期</th>
                <th>询价员</th>
                <th>询价时间</th>
                <th>订单号</th>
                <th>询价单号</th>
                <th>含税总价</th>
            </tr>
            <tr class="inquiry_list">
                <td>优选</td>
                <td class="stressColor"><?= $inquiryBetter ? $inquiryBetter->supplier->name : '' ?></td>
                <td class="number"><?=$inquiryBetter ? $inquiryBetter->number : 0?></td>
                <td><?= $inquiryBetter ? $inquiryBetter->tax_rate : 0 ?></td>
                <td><b class="color"><?= $inquiryBetter ? $inquiryBetter->price : 0 ?></b></td>
                <td class="tax_price"><?= $inquiryBetter ? $inquiryBetter->tax_price : 0 ?></td>
                <td><?= $inquiryBetter ? Inquiry::$purchase[$inquiryBetter->is_purchase] : '否' ?></td>
                <td class="stressColor"><b class="color"><?= $inquiryBetter ? $inquiryBetter->delivery_time : 0 ?></b></td>
                <td><?=$inquiryBetter ? ($inquiryBetter->admin_id ? $inquiryBetter->admin->username : '') : '' ?></td>
                <td><?=$inquiryBetter ? substr($inquiryBetter->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?=$inquiryBetter ? Html::a($inquiryBetter->order ? $inquiryBetter->order->order_sn : '', $inquiryBetter->order ? Url::to(['order/detail', 'id' => $inquiryBetter->order_id]) : '') : '' ?></td>
                <td><?=$inquiryBetter ? Html::a($inquiryBetter->orderInquiry ? $inquiryBetter->orderInquiry->inquiry_sn : '', $inquiryBetter->orderInquiry ? Url::to(['order-inquiry/view', 'id' => $inquiryBetter->order_inquiry_id]) : '') : '' ?></td>
                <td><?=$inquiryBetter ? ($inquiryBetter->number * $inquiryBetter->tax_price) : 0 ?></td>
            </tr>
            <tr class="inquiry_list">
                <td>价格</td>
                <td class="stressColor"><?= $inquiryPrice ? $inquiryPrice->supplier->name : '' ?></td>
                <td class="number"><?=$inquiryPrice ? $inquiryPrice->number : 0?></td>
                <td><?= $inquiryPrice ? $inquiryPrice->tax_rate : 0 ?></td>
                <td><b class="color"><?= $inquiryPrice ? $inquiryPrice->price : 0 ?></b></td>
                <td class="tax_price"><?= $inquiryPrice ? $inquiryPrice->tax_price : 0 ?></td>
                <td><?= $inquiryPrice ? Inquiry::$purchase[$inquiryPrice->is_purchase] : '否' ?></td>
                <td class="stressColor"><b class="color"><?= $inquiryPrice ? $inquiryPrice->delivery_time : 0 ?></b></td>
                <td><?= $inquiryPrice ? ($inquiryPrice->admin_id ? $inquiryPrice->admin->username : '') : '' ?></td>
                <td><?= $inquiryPrice ? substr($inquiryPrice->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryPrice ? Html::a($inquiryPrice->order ? $inquiryPrice->order->order_sn : '', $inquiryPrice->order ? Url::to(['order/detail', 'id' => $inquiryPrice->order_id]) : '') : '' ?></td>
                <td><?= $inquiryPrice ? Html::a($inquiryPrice->orderInquiry ? $inquiryPrice->orderInquiry->inquiry_sn : '', $inquiryPrice->orderInquiry ? Url::to(['order-inquiry/view', 'id' => $inquiryPrice->order_inquiry_id]) : '') : '' ?></td>
                <td><?= $inquiryPrice ? ($inquiryPrice->number * $inquiryPrice->tax_price) : 0 ?></td>
            </tr>
            <tr class="inquiry_list">
                <td>货期</td>
                <td class="stressColor"><?= $inquiryTime ? $inquiryTime->supplier->name : '' ?></td>
                <td class="number"><?=$inquiryTime ? $inquiryTime->number : 0?></td>
                <td><?= $inquiryTime ? $inquiryTime->tax_rate : 0 ?></td>
                <td><b class="color"><?= $inquiryTime ? $inquiryTime->price : 0 ?></b></td>
                <td class="tax_price"><?= $inquiryTime ? $inquiryTime->tax_price : 0 ?></td>
                <td><?= $inquiryTime ? Inquiry::$purchase[$inquiryTime->is_purchase] : '否' ?></td>
                <td class="stressColor"><b class="color"><?= $inquiryTime ? $inquiryTime->delivery_time : 0 ?></b></td>
                <td><?= $inquiryTime ? ($inquiryTime->admin_id ? $inquiryTime->admin->username : '') : '' ?></td>
                <td><?= $inquiryTime ? substr($inquiryTime->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?= $inquiryTime ? Html::a($inquiryTime->order ? $inquiryTime->order->order_sn : '', $inquiryTime->order ? Url::to(['order/detail', 'id' => $inquiryTime->order_id]) : '') : '' ?></td>
                <td><?= $inquiryTime ? Html::a($inquiryTime->orderInquiry ? $inquiryTime->orderInquiry->inquiry_sn : '', $inquiryTime->orderInquiry ? Url::to(['order-inquiry/view', 'id' => $inquiryTime->order_inquiry_id]) : '') : '' ?></td>
                <td><?= $inquiryTime ? ($inquiryTime->number * $inquiryTime->tax_price) : 0 ?></td>
            </tr>
            <tr class="inquiry_list">
                <td>最新</td>
                <td class="stressColor"><?= $inquiryNew ? $inquiryNew->supplier->name : '' ?></td>
                <td class="number"><?=$inquiryNew ? $inquiryNew->number : 0?></td>
                <td><?= $inquiryNew ? $inquiryNew->tax_rate : 0 ?></td>
                <td><b class="color"><?= $inquiryNew ? $inquiryNew->price : 0 ?></b></td>
                <td class="tax_price"><?= $inquiryNew ? $inquiryNew->tax_price : 0 ?></td>
                <td><?= $inquiryNew ? Inquiry::$purchase[$inquiryNew->is_purchase] : '否' ?></td>
                <td class="stressColor"><b class="color"><?= $inquiryNew ? $inquiryNew->delivery_time : 0 ?></b></td>
                <td><?=$inquiryNew ? ($inquiryNew->admin_id ? $inquiryNew->admin->username : '') : '' ?></td>
                <td><?=$inquiryNew ? substr($inquiryNew->inquiry_datetime, 0, 10) : '' ?></td>
                <td><?=$inquiryNew ? Html::a($inquiryNew->order ? $inquiryNew->order->order_sn : '', $inquiryNew->order ? Url::to(['order/detail', 'id' => $inquiryNew->order_id]) : '') : '' ?></td>
                <td><?=$inquiryNew ? Html::a($inquiryNew->orderInquiry ? $inquiryNew->orderInquiry->inquiry_sn : '', $inquiryNew->orderInquiry ? Url::to(['order-inquiry/view', 'id' => $inquiryNew->order_inquiry_id]) : '') : '' ?></td>
                <td><?=$inquiryNew ? ($inquiryNew->number * $inquiryNew->tax_price) : 0 ?></td>
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
                <th>合同签订时间</th>
                <th>合同交货时间</th>
                <th>入库时间</th>
                <th>理论货期</th>
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
                <td><?= $paymentNew ? ($paymentNew->orderPayment ?  substr($paymentNew->orderPayment->agreement_at, 0, 10) : '') : '' ?></td>
                <td><?= $paymentNew ? ($paymentNew->orderPayment ?  substr($paymentNew->orderPayment->delivery_date, 0, 10) : '') : '' ?></td>
                <td><?= $paymentNew ? ($paymentNew->orderPayment ?  substr($paymentNew->orderPayment->stock_at, 0, 10) : '') : '' ?></td>
                <td>
                    <?php
                        if ($paymentNew && $paymentNew->orderPayment) {
                            $agreement_at  = substr($paymentNew->orderPayment->agreement_at, 0, 10);
                            $delivery_date = substr($paymentNew->orderPayment->delivery_date, 0, 10);
                            $theory_time = strtotime($delivery_date) - strtotime($agreement_at);
                            if ($theory_time) {
                                echo $theory_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo 0;
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($paymentNew && $paymentNew->orderPayment && $paymentNew->orderPayment->stock_at) {
                            $agreement_at = substr($paymentNew->orderPayment->agreement_at, 0, 10);
                            $stock_at     = substr($paymentNew->orderPayment->stock_at, 0, 10);
                            $reality_time = strtotime($stock_at) - strtotime($agreement_at);
                            if ($reality_time) {
                                echo $reality_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo '';
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
                <td><?= $paymentPrice ? ($paymentPrice->orderPayment ?  substr($paymentPrice->orderPayment->agreement_at, 0, 10) : '') : '' ?></td>
                <td><?= $paymentPrice ? ($paymentPrice->orderPayment ?  substr($paymentPrice->orderPayment->delivery_date, 0, 10) : '') : '' ?></td>
                <td><?= $paymentPrice ? ($paymentPrice->orderPayment ?  substr($paymentPrice->orderPayment->stock_at, 0, 10) : '') : '' ?></td>
                <td>
                    <?php
                        if ($paymentPrice && $paymentPrice->orderPayment) {
                            $agreement_at  = substr($paymentPrice->orderPayment->agreement_at, 0, 10);
                            $delivery_date = substr($paymentPrice->orderPayment->delivery_date, 0, 10);
                            $theory_time = strtotime($delivery_date) - strtotime($agreement_at);
                            if ($theory_time) {
                                echo $theory_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo 0;
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($paymentPrice && $paymentPrice->orderPayment && $paymentPrice->orderPayment->stock_at) {
                            $agreement_at = substr($paymentPrice->orderPayment->agreement_at, 0, 10);
                            $stock_at     = substr($paymentPrice->orderPayment->stock_at, 0, 10);
                            $reality_time = strtotime($stock_at) - strtotime($agreement_at);
                            if ($reality_time) {
                                echo $reality_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo '';
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
                <td><?= $paymentDay ? ($paymentDay->orderPayment ?  substr($paymentDay->orderPayment->agreement_at, 0, 10) : '') : '' ?></td>
                <td><?= $paymentDay ? ($paymentDay->orderPayment ?  substr($paymentDay->orderPayment->delivery_date, 0, 10) : '') : '' ?></td>
                <td><?= $paymentDay ? ($paymentDay->orderPayment ?  substr($paymentDay->orderPayment->stock_at, 0, 10) : '') : '' ?></td>
                <td>
                    <?php
                        if ($paymentDay && $paymentDay->orderPayment) {
                            $agreement_at  = substr($paymentDay->orderPayment->agreement_at, 0, 10);
                            $delivery_date = substr($paymentDay->orderPayment->delivery_date, 0, 10);
                            $theory_time = strtotime($delivery_date) - strtotime($agreement_at);
                            if ($theory_time) {
                                echo $theory_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo 0;
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($paymentDay && $paymentDay->orderPayment && $paymentDay->orderPayment->stock_at) {
                            $agreement_at = substr($paymentDay->orderPayment->agreement_at, 0, 10);
                            $stock_at     = substr($paymentDay->orderPayment->stock_at, 0, 10);
                            $reality_time = strtotime($stock_at) - strtotime($agreement_at);
                            if ($reality_time) {
                                echo $reality_time/3600/24 . '天';
                            } else {
                                echo 0;
                            }
                        } else {
                            echo '';
                        }
                    ?>
                </td>
                <td><?= $paymentDay ? Html::a($paymentDay->order->order_sn, Url::to(['order/detail', 'id' => $paymentDay->order_id])) : ''?></td>
                <td><?= $paymentDay ? Html::a($paymentDay->order_payment_sn, Url::to(['order-payment/detail', 'id' => $paymentDay->order_payment_id])) : ''?></td>
                <td><?= $paymentDay ? $paymentDay->number * $paymentDay->fixed_tax_price : 0 ?></td>
            </tr>
            </thead>

        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="4">收入记录</th>
                <th>类型</th>
                <th>客户名称</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期</th>
                <th>合同签订日期</th>
                <th>订单号</th>
                <th>收入合同单号</th>
                <th>含税总价</th>
            </tr>
            <tr>
                <td>最新</td>
                <td><?=$agreementGoodsNew ? (isset($agreementGoodsNew->orderAgreement->customer) ? $agreementGoodsNew->orderAgreement->customer->name : '') : ''?></td>
                <td><?=$agreementGoodsNew ? $agreementGoodsNew->number : 0 ?></td>
                <td><?=$agreementGoodsNew ? $agreementGoodsNew->tax_rate : 0 ?></td>
                <td><b class="color"><?=$agreementGoodsNew ? $agreementGoodsNew->quote_price : 0 ?></b></td>
                <td><?=$agreementGoodsNew ? $agreementGoodsNew->quote_tax_price : 0 ?></td>
                <td><?=$agreementGoodsNew ? $agreementGoodsNew->quote_delivery_time : 0 ?></td>
                <td><?=$agreementGoodsNew ? substr($agreementGoodsNew->orderAgreement->sign_date, 0, 10) : 0 ?></td>
                <td><?=$agreementGoodsNew ? Html::a($agreementGoodsNew->order->order_sn, Url::to(['order/detail', 'id' => $agreementGoodsNew->order_id])) : ''?></td>
                <td><?=$agreementGoodsNew ? Html::a($agreementGoodsNew->order_agreement_sn, Url::to(['order-agreement/view', 'id' => $agreementGoodsNew->order_agreement_id])) : '' ?></td>
                <td><?=$agreementGoodsNew ? $agreementGoodsNew->quote_tax_price * $agreementGoodsNew->number : 0 ?></td>
            </tr>
            <tr>
                <td>最高价</td>
                <td><?=$agreementGoodsHigh ? (isset($agreementGoodsHigh->orderAgreement->customer) ? $agreementGoodsHigh->orderAgreement->customer->name : '') : ''?></td>
                <td><?=$agreementGoodsHigh ? $agreementGoodsHigh->number : 0 ?></td>
                <td><?=$agreementGoodsHigh ? $agreementGoodsHigh->tax_rate : 0 ?></td>
                <td><b class="color"><?=$agreementGoodsHigh ? $agreementGoodsHigh->quote_price : 0 ?></b></td>
                <td><?=$agreementGoodsHigh ? $agreementGoodsHigh->quote_tax_price : 0 ?></td>
                <td><?=$agreementGoodsHigh ? $agreementGoodsHigh->quote_delivery_time : 0 ?></td>
                <td><?=$agreementGoodsHigh ? substr($agreementGoodsHigh->orderAgreement->sign_date, 0, 10) : 0 ?></td>
                <td><?=$agreementGoodsHigh ? Html::a($agreementGoodsHigh->order->order_sn, Url::to(['order/detail', 'id' => $agreementGoodsHigh->order_id])) : ''?></td>
                <td><?=$agreementGoodsHigh ? Html::a($agreementGoodsHigh->order_agreement_sn, Url::to(['order-agreement/view', 'id' => $agreementGoodsHigh->order_agreement_id])) : '' ?></td>
                <td><?=$agreementGoodsHigh ? $agreementGoodsHigh->quote_tax_price * $agreementGoodsHigh->number : 0 ?></td>
            </tr>
            <tr>
                <td>最低价</td>
                <td><?=$agreementGoodsLow ? (isset($agreementGoodsLow->orderAgreement->customer) ? $agreementGoodsLow->orderAgreement->customer->name : '') : ''?></td>
                <td><?=$agreementGoodsLow ? $agreementGoodsLow->number : 0 ?></td>
                <td><?=$agreementGoodsLow ? $agreementGoodsLow->tax_rate : 0 ?></td>
                <td><b class="color"><?=$agreementGoodsLow ? $agreementGoodsLow->quote_price : 0 ?></b></td>
                <td><?=$agreementGoodsLow ? $agreementGoodsLow->quote_tax_price : 0 ?></td>
                <td><?=$agreementGoodsLow ? $agreementGoodsLow->quote_delivery_time : 0 ?></td>
                <td><?=$agreementGoodsLow ? substr($agreementGoodsLow->orderAgreement->sign_date, 0, 10) : 0 ?></td>
                <td><?=$agreementGoodsLow ? Html::a($agreementGoodsLow->order->order_sn, Url::to(['order/detail', 'id' => $agreementGoodsLow->order_id])) : ''?></td>
                <td><?=$agreementGoodsLow ? Html::a($agreementGoodsLow->order_agreement_sn, Url::to(['order-agreement/view', 'id' => $agreementGoodsLow->order_agreement_id])) : '' ?></td>
                <td><?=$agreementGoodsLow ? $agreementGoodsLow->quote_tax_price * $agreementGoodsLow->number : 0 ?></td>
            </tr>
            </thead>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th rowspan="5"><?=Html::a('竞争对手记录', Url::to(['competitor-goods/index', 'CompetitorGoodsSearch[goods_id]' => $goods->id]))?></th>
                <th>类型</th>
                <th>竞争对手</th>
                <th>针对客户</th>
                <th>数量</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期</th>
                <th>库存数量</th>
                <th>报价时间</th>
                <th>含税总价</th>
            </tr>
            <tr>
                <td>发行价</td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->competitor->name : ''?></td>
                <td><?=($competitorGoodsIssue && $competitorGoodsIssue->customer) ? $competitorGoodsIssue->customers->name : ''?></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->number : ''?></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->tax_rate : ''?></td>
                <td><b class="color"><?=$competitorGoodsIssue ? $competitorGoodsIssue->price : ''?></b></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->tax_price : ''?></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->delivery_time : ''?></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->stock_number : 0?></td>
                <td><?=$competitorGoodsIssue ? substr($competitorGoodsIssue->offer_date, 0, 10) : ''?></td>
                <td><?=$competitorGoodsIssue ? $competitorGoodsIssue->tax_price * $competitorGoodsIssue->number : 0?></td>
            </tr>
            <tr>
                <td>最新</td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->competitor->name : ''?></td>
                <td><?=($competitorGoodsNew && $competitorGoodsNew->customer) ? $competitorGoodsNew->customers->name : ''?></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->number : ''?></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->tax_rate : ''?></td>
                <td><b class="color"><?=$competitorGoodsNew ? $competitorGoodsNew->price : ''?></b></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->tax_price : ''?></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->delivery_time : ''?></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->stock_number : 0?></td>
                <td><?=$competitorGoodsNew ? substr($competitorGoodsNew->offer_date, 0, 10) : ''?></td>
                <td><?=$competitorGoodsNew ? $competitorGoodsNew->tax_price * $competitorGoodsNew->number : 0?></td>
            </tr>
            <tr>
                <td>最高价</td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->competitor->name : ''?></td>
                <td><?=($competitorGoodsHigh && $competitorGoodsHigh->customer) ? $competitorGoodsHigh->customers->name : ''?></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->number : ''?></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->tax_rate : ''?></td>
                <td><b class="color"><?=$competitorGoodsHigh ? $competitorGoodsHigh->price : ''?></b></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->tax_price : ''?></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->delivery_time : ''?></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->stock_number : 0?></td>
                <td><?=$competitorGoodsHigh ? substr($competitorGoodsHigh->offer_date, 0, 10) : ''?></td>
                <td><?=$competitorGoodsHigh ? $competitorGoodsHigh->tax_price * $competitorGoodsHigh->number : 0?></td>
            </tr>
            <tr>
                <td>最低价</td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->competitor->name : ''?></td>
                <td><?=($competitorGoodsLow && $competitorGoodsLow->customer) ? $competitorGoodsLow->customers->name : ''?></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->number : ''?></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->tax_rate : ''?></td>
                <td><b class="color"><?=$competitorGoodsLow ? $competitorGoodsLow->price : ''?></b></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->tax_price : ''?></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->delivery_time : ''?></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->stock_number : 0?></td>
                <td><?=$competitorGoodsLow ? substr($competitorGoodsLow->offer_date, 0, 10) : ''?></td>
                <td><?=$competitorGoodsLow ? $competitorGoodsLow->tax_price * $competitorGoodsLow->number : 0?></td>
            </tr>
            </thead>
        </table>
    </div>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        $('.inquiry_list').each(function (i, e) {
            var number = $(e).find('.number').text();
            var price = $(e).find('.price').text();
            var tax_price = $(e).find('.tax_price').text();
            var all_price = number * price;
            var all_tax_price = number * tax_price;
            $(e).find('.all_price').text(all_price.toFixed(2));
            $(e).find('.all_tax_price').text(all_tax_price.toFixed(2));
        });

        function init(){
            var stock_number = parseInt($('.stock_list').find('.number').text());
            var high_number  = parseInt($('.stock_list').find('.high_number').text());
            var low_number   = parseInt($('.stock_list').find('.low_number').text());
            if (stock_number > high_number || stock_number < low_number) {
                $('.stock_list').find('.number').removeClass('color');
                $('.stock_list').find('.number').addClass('changeColor');
            }
        }

    });
</script>
