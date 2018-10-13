<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Order;
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
            ],
            [
                'attribute' => 'updated_at',
            ],
            [
                'attribute' => 'created_at',
            ],
        ],
    ])?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>订单号</th>
                <th>询价单号</th>
            </tr>
            <?php foreach ($orderInquiry as $inquiry):?>
            <tr>
                <td><?=$inquiry->order_id?></td>
                <td><?=Html::a($inquiry->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $inquiry->id]))?></td>
            </tr>
            <?php endforeach;?>
            </thead>
            <thead>
            <tr>
                <th>订单号</th>
                <th>最终订单号</th>
            </tr>
            <?php foreach ($orderFinal as $final):?>
                <tr>
                    <td><?=$final->order_id?></td>
                    <td><?=Html::a($final->final_sn, Url::to(['order-inquiry/detail', 'id' => $final->id]))?></td>
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
                    <td><?=Html::a($purchse->purchse_sn, Url::to(['order-purchse/detail', 'id' => $purchse->id]))?></td>
                </tr>
            <?php endforeach;?>
            </thead>
        </table>
    </div>
</div>


