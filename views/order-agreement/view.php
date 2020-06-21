<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '收入合同详情';
$this->params['breadcrumbs'][] = $this->title;

$model->agreement_date = substr($model->agreement_date, 0, 10);
$model->sign_date = substr($model->sign_date, 0, 10);

$use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['报价员']])->all();
$quote_adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId   = Yii::$app->user->identity->id;
?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_quote_id="<?=$_GET['id']?>">
            <tr>
                <th>序号</th>
                <th>品牌</th>
                <th>零件号</th>
                <?php if (!in_array($userId, $quote_adminIds)):?>
                    <th>厂家号</th>
                    <th>原厂家</th>
                <?php endif;?>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>数量</th>
                <th>单位</th>
                <th>税率</th>
                <?php if (!in_array($userId, array_merge($adminIds, $quote_adminIds))):?>
                <th>发行含税单价</th>
                <th>发行含税总价</th>
                <th>发行货期</th>
                <?php endif;?>
                <th>含税单价</th>
                <th>含税总价</th>
                <th>货期</th>
                <th>库存数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
                <tr class="order_quote_list">
                    <td><?=$item->serial?></td>
                    <td><?=$item->goods->material_code?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <?php if (!in_array($userId, $quote_adminIds)):?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->original_company?></td>
                    <?php endif;?>
                    <td class="goods_id" data-goods_id="<?=$item->goods_id?>" data-goods_type="<?=$item->type?>"
                        data-relevance_id="<?=$item->relevance_id?>" data-quote_goods_id="<?=$item->id?>">
                        <?=$item->goods->description?>
                    </td>
                    <td><?=$item->goods->description_en?></td>
                    <td class="afterNumber"><?=$item->number?></td>
                    <td><?=$item->goods->unit?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <?php if (!in_array($userId, array_merge($adminIds, $quote_adminIds))):?>
                    <?php
                        $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax_rate/100), 2, '.', '');
                    ?>
                    <td><?=$publish_tax_price?></td>
                    <td class="publish_tax_price"><?=$publish_tax_price * $item->number?></td>
                    <td class="publish_delivery_time"><?=$item->goods->publish_delivery_time?></td>
                    <?php endif;?>
                    <td class="tax_price"><?=$item->quote_tax_price?></td>
                    <td class="all_tax_price"><?=$item->quote_all_tax_price?></td>
                    <td><?=$item->quote_delivery_time?></td>
                    <td><?=isset($item->stock) ? $item->stock->number : 0?></td>
                </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="<?=in_array($userId, $adminIds) ? 8 : (in_array($userId, $quote_adminIds) ? 6 : 11)?>" rowspan="2">汇总统计</td>
                <?php if (!in_array($userId, array_merge($adminIds, $quote_adminIds))):?>
                <td>发行含税总价合计</td>
                <?php endif;?>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td>收入合同金额</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <?php if (!in_array($userId, array_merge($adminIds, $quote_adminIds))):?>
                <td class="sta_all_publish_tax_price"></td>
                <?php endif;?>
                <td class="sta_all_price"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'payment_price')->textInput(['readonly' => true])->label('收入合同单含税总价') ?>

        <?= $form->field($model, 'agreement_sn')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->textInput(['readonly' => true]);?>

        <?= $form->field($model, 'sign_date')->textInput(['readonly' => true]) ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    $(document).ready(function () {
        init();
        function init(){
            var sta_all_price               = 0;
            var sta_all_publish_tax_price   = 0;
            $('.order_quote_list').each(function (i, e) {
                var all_price      = $(e).find('.all_tax_price').text();
                if (all_price) {
                    sta_all_price  += parseFloat(all_price);
                }

                var publish_tax_price = parseFloat($(e).find('.publish_tax_price').text());
                if (publish_tax_price) {
                    sta_all_publish_tax_price += publish_tax_price;
                }
            });
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_publish_tax_price').text(sta_all_publish_tax_price.toFixed(2));
        }
    });
</script>
