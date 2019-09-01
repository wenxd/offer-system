<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use app\models\AuthAssignment;

$this->title = '支出合同详情';
$this->params['breadcrumbs'][] = $this->title;



$use_admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
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
                <?php if(in_array($userId, $adminIds)):?>
                <th>零件号</th>
                <?php endif;?>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>供应商</th>
                <th>期货(周)</th>
                <th>税率</th>
                <th>未率单价</th>
                <th>含率单价</th>
                <th>未率总价</th>
                <th>含率总价</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_payment_list" data-payment_goods_id="<?=$item->id?>">
                    <td><?=$item->serial?></td>
                    <?php if(in_array($userId, $adminIds)):?>
                    <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->inquiry->delivery_time?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td style="background-color: darkgrey" class="price"><?=$item->fixed_price?></td>
                    <td style="background-color: darkgrey" class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td style="background-color: darkgrey" class="all_price"><?=$item->fixed_all_price?></td>
                    <td style="background-color: darkgrey" class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <td style="background-color: darkgrey" class="afterNumber"><?=$item->fixed_number?></td>
                </tr>
            <?php endforeach;?>

            <tr style="background-color: #acccb9">
                <td colspan="11" rowspan="2">汇总统计</td>
                <td>支出未税总价</td>
                <td>支出含税总价</td>
                <td rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="sta_quote_all_price"></td>
                <td class="sta_quote_all_tax_price"></td>
            </tr>

            </tbody>
        </table>

    </div>

    <?php ActiveForm::end(); ?>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        function init() {
            var sta_quote_all_price = 0;
            var sta_quote_all_tax_price = 0;
            $('.order_payment_list').each(function (i, e) {
                var all_price = $(e).find('.all_price').text();
                var all_tax_price = $(e).find('.all_tax_price').text();
                if (all_price) {
                    sta_quote_all_price += parseFloat(all_price);
                }
                if (all_tax_price) {
                    sta_quote_all_tax_price += parseFloat(all_tax_price);
                }
            });
            $('.sta_quote_all_price').text(sta_quote_all_price.toFixed(2));
            $('.sta_quote_all_tax_price').text(sta_quote_all_tax_price.toFixed(2));
        }
    });
</script>
