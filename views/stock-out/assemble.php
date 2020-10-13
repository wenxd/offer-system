<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
$goods_number = $agreementGoods->goods->goods_number;
$this->title = "出库零件总成($goods_number)";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">
    <div class="box-body">
        <h3>总成零件信息</h3>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>收入合同单号</th>
                <th>订单号</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>单位</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?=$agreementGoods->order_agreement_sn?></td>
                <td><?=$agreementGoods->order->order_sn?></td>
                <td><?=$agreementGoods->goods->goods_number?></td>
                <td><?=$agreementGoods->goods->goods_number_b?></td>
                <td><?=$agreementGoods->goods->description?></td>
                <td><?=$agreementGoods->goods->description_en?></td>
                <td><?=$agreementGoods->goods->original_company?></td>
                <td><?=$agreementGoods->goods->unit?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
