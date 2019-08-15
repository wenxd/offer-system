<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use app\models\AuthAssignment;

$this->title = '支出合同详情';
$this->params['breadcrumbs'][] = $this->title;



$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_payment_id="<?=$_GET['id']?>">
            <tr>
                <th>序号</th>
                <th>零件号A</th>
                <th>零件号B</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>供应商</th>
                <th>货期(天)</th>
                <th>税率</th>
                <th>未率单价</th>
                <th>含率单价</th>
                <th>未率总价</th>
                <th>含率总价</th>
                <th>数量</th>
                <th>修改后未率单价</th>
                <th>修改后含率单价</th>
                <th>修改后未率总价</th>
                <th>修改后含率总价</th>
                <th>修改后数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_payment_list" data-payment_goods_id="<?=$item->id?>">
                    <td><?=$item->serial?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->inquiry->delivery_time?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td><?=$item->price?></td>
                    <td><?=$item->tax_price?></td>
                    <td><?=$item->all_price?></td>
                    <td><?=$item->all_tax_price?></td>
                    <td><?=$item->number?></td>
                    <td style="background-color: darkgrey" class="price"><?=$item->fixed_price?></td>
                    <td style="background-color: darkgrey" class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td style="background-color: darkgrey" class="all_price"><?=$item->fixed_all_price?></td>
                    <td style="background-color: darkgrey" class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <td style="background-color: darkgrey" class="afterNumber"><?=$item->fixed_number?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

    </div>

    <?php ActiveForm::end(); ?>
</div>
