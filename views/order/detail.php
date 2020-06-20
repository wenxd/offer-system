<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Order;
use app\models\Goods;
use app\models\OrderInquiry;
use yii\widgets\DetailView;

$this->title = '订单详情';
$this->params['breadcrumbs'][] = $this->title;
$use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
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
        <?php if (!in_array($userId, $adminIds)):?>
        <table id="example2" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>序号</th>
                <th>品牌</th>
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
                    <td><?= $item->goods->material_code?></td>
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
        <?php endif;?>
        <table id="example2" class="table table-bordered table-hover">
            <?php if (!in_array($userId, $adminIds)):?>
                <thead>
                <tr>
                    <th>订单号</th>
                    <th>询价单号</th>
                    <th>负责人</th>
                    <th>询价完成</th>
                </tr>
                <?php foreach ($orderInquiry as $inquiry):?>
                <tr>
                    <td><?=$inquiry->order_id?></td>
                    <td><?=Html::a($inquiry->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $inquiry->id]))?></td>
                    <td><?=$inquiry->admin ? $inquiry->admin->username : ''?></td>
                    <td><?=OrderInquiry::$Inquiry[$inquiry->is_inquiry]?></td>
                </tr>
                <?php endforeach;?>
                </thead>

                <thead>
                <tr>
                    <th>订单号</th>
                    <th>成本单号</th>
                    <th>负责人</th>
                </tr>
                <?php foreach ($orderFinal as $final):?>
                    <tr>
                        <td><?=$final->order_id?></td>
                        <td><?=Html::a($final->final_sn, Url::to(['order-final/view', 'id' => $final->id]))?></td>
                        <td><?=$final->admin ? $final->admin->username : ''?></td>
                    </tr>
                <?php endforeach;?>
                </thead>

                <thead>
                <tr>
                    <th>订单号</th>
                    <th>报价单号</th>
                    <th>负责人</th>
                </tr>
                <?php foreach ($orderQuote as $quote):?>
                    <tr>
                        <td><?=$quote->order_id?></td>
                        <td><?=Html::a($quote->quote_sn, Url::to(['order-quote/view', 'id' => $quote->id]))?></td>
                        <td><?=$quote->admin ? $quote->admin->username : ''?></td>
                    </tr>
                <?php endforeach;?>
                </thead>

                <thead>
                <tr>
                    <th>订单号</th>
                    <th>采购单号</th>
                    <th>负责人</th>
                </tr>
                <?php foreach ($orderPurchase as $purchase):?>
                    <tr>
                        <td><?=$purchase->order_id?></td>
                        <td><?=Html::a($purchase->purchase_sn, Url::to(['order-purchase/detail', 'id' => $purchase->id]))?></td>
                        <td><?=$purchase->admin ? $purchase->admin->username : ''?></td>
                    </tr>
                <?php endforeach;?>
                </thead>
            <?php endif;?>
            <thead>
            <tr>
                <th>订单号</th>
                <th>收入合同单号</th>
                <th>负责人</th>
                <th>收入合同金额</th>
            </tr>
            <?php $orderAgreementPrice = 0;?>
            <?php foreach ($orderAgreement as $agreement):?>
                <tr>
                    <td><?=$agreement->order_id?></td>
                    <td><?=Html::a($agreement->agreement_sn, Url::to(['order-agreement/view', 'id' => $agreement->id]))?></td>
                    <td><?=$agreement->admin ? $agreement->admin->username : ''?></td>
                    <td><?=$agreement->payment_price?></td>
                </tr>
                <?php $orderAgreementPrice += $agreement->payment_price?>
            <?php endforeach;?>
            <tr>
                <td colspan="2"></td>
                <td>汇总</td>
                <td><?=$orderAgreementPrice?></td>
            </tr>
            </thead>

            <thead>
            <tr>
                <th>订单号</th>
                <th>支出合同单号</th>
                <th>负责人</th>
                <th>支出合同金额</th>
            </tr>
            <?php $orderPaymentPrice = 0;?>
            <?php foreach ($orderPayment as $payment):?>
                <tr>
                    <td><?=$payment->order_id?></td>
                    <td><?=Html::a($payment->payment_sn, Url::to(['order-payment/detail', 'id' => $payment->id]))?></td>
                    <td><?=$payment->admin ? $payment->admin->username : ''?></td>
                    <td><?=$payment->payment_price?></td>
                </tr>
                <?php $orderPaymentPrice += $payment->payment_price?>
            <?php endforeach;?>
                <tr>
                    <td colspan="2"></td>
                    <td>汇总</td>
                    <td><?=$orderPaymentPrice?></td>
                </tr>
            </thead>

            <thead>
            <tr>
                <th></th>
                <th>使用库存</th>
                <th>负责人</th>
                <th>使用库存金额</th>
            </tr>
            <?php $stockPrice = 0;?>
            <?php foreach ($orderUseStock as $orderStock):?>
                <?php $stockPrice += $orderStock->all_tax_price?>
            <?php endforeach;?>
            <tr>
                <td ></td>
                <td><?=Html::a('汇总明细',Url::to(['agreement-stock/index', "AgreementStockSearch[order_sn]" => $model->order_sn]))?></td>
                <td><?=isset($orderStock) ? ($orderStock->admin ? $orderStock->admin->username : '') : ''?></td>
                <td><?=$stockPrice?></td>
            </tr>
            </thead>

            <thead>
            <tr>
                <th colspan="2"></th>
                <th>利润率公式</th>
                <th>结果</th>
            </tr>
            <tr>
                <th colspan="2"></th>
                <td>（收入-支出-库存）/ 收入</td>
                <td><?=$orderAgreementPrice ? number_format((($orderAgreementPrice - $orderPaymentPrice - $stockPrice) / $orderAgreementPrice) * 100, 2, '.', '') . '%' : 0?></td>
            </tr>
            </thead>
        </table>
    </div>
</div>


