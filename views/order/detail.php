<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Order;
use app\models\Goods;
use app\models\OrderInquiry;
use yii\widgets\DetailView;

$this->title = '订单详情';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box table-responsive">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'order_type',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Order::$orderType[$model->order_type];
                }
            ],
            [
                'attribute' => 'status',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Order::$status[$model->status];
                }
            ],
            [
                'attribute' => 'customer_name',
                'value'     => function ($model) {
                    if ($model->customer) {
                        return $model->customer->name;
                    }
                }
            ],
            'order_sn',
            [
                'attribute' => 'provide_date',
                'value'     => function($model){
                    return substr($model->provide_date, 0, 10);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
                }
            ],
        ],
    ])?>
    
    <div class="box-body">
        <table id="example2" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>序号</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>数量</th>
                <th>加工</th>
                <th>特制</th>
                <th>铭牌</th>
                <th>技术备注</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderGoods as $key => $item):?>
                <tr>
                    <td class="serial"><?= $item->serial?></td>
                    <td><?= $item->goods->goods_number?></td>
                    <td><?= $item->goods->goods_number_b?></td>
                    <td><?= $item->goods->description?></td>
                    <td><?= $item->goods->description_en?></td>
                    <td><?= $item->goods->original_company?></td>
                    <td><?= $item->goods->original_company_remark?></td>
                    <td><?= $item->goods->unit?></td>
                    <td class="number"><?= $item->number?></td>
                    <td class="addColor"><?= Goods::$process[$item->goods->is_process]?></td>
                    <td class="addColor"><?= Goods::$special[$item->goods->is_special]?></td>
                    <td class="addColor"><?= Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?= $item->goods->technique_remark?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>订单号</th>
                <th>询价单号</th>
                <th>询价完成</th>
            </tr>
            <?php foreach ($orderInquiry as $inquiry):?>
            <tr>
                <td><?=$inquiry->order_id?></td>
                <td><?=Html::a($inquiry->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $inquiry->id]))?></td>
                <td><?=OrderInquiry::$Inquiry[$inquiry->is_inquiry]?></td>
            </tr>
            <?php endforeach;?>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>成本单号</th>
            </tr>
            <?php foreach ($orderFinal as $final):?>
                <tr>
                    <td><?=$final->order_id?></td>
                    <td><?=Html::a($final->final_sn, Url::to(['order-final/view', 'id' => $final->id]))?></td>
                </tr>
            <?php endforeach;?>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>报价单号</th>
            </tr>
            <?php foreach ($orderQuote as $quote):?>
                <tr>
                    <td><?=$quote->order_id?></td>
                    <td><?=Html::a($quote->quote_sn, Url::to(['order-quote/view', 'id' => $quote->id]))?></td>
                </tr>
            <?php endforeach;?>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>采购单号</th>
            </tr>
            <?php foreach ($orderPurchase as $purchse):?>
                <tr>
                    <td><?=$purchse->order_id?></td>
                    <td><?=Html::a($purchse->purchase_sn, Url::to(['order-purchase/detail', 'id' => $purchse->id]))?></td>
                </tr>
            <?php endforeach;?>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>收入合同单号</th>
            </tr>
            <?php foreach ($orderAgreement as $agreement):?>
                <tr>
                    <td><?=$agreement->order_id?></td>
                    <td><?=Html::a($agreement->agreement_sn, Url::to(['order-agreement/view', 'id' => $agreement->id]))?></td>
                </tr>
            <?php endforeach;?>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>支出合同单号</th>
                <th>支出合同金额</th>
            </tr>
            <?php $order_price = 0;?>
            <?php foreach ($orderPayment as $payment):?>
                <tr>
                    <td><?=$payment->order_id?></td>
                    <td><?=Html::a($payment->payment_sn, Url::to(['order-payment/detail', 'id' => $payment->id]))?></td>
                    <td><?=$payment->payment_price?></td>
                </tr>
                <?php $order_price += $payment->payment_price?>
            <?php endforeach;?>
                <tr>
                    <td></td>
                    <td>汇总</td>
                    <td><?=$order_price?></td>
                </tr>
            </thead>

        </table>
    </div>
</div>


