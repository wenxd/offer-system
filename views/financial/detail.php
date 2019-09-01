<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;
use yii\widgets\DetailView;

$this->title = '待付款订单详情';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$stock_goods_ids = ArrayHelper::getColumn($stockLog, 'goods_id');
$userId   = Yii::$app->user->identity->id;

$isShow = in_array($userId, $adminIds);

?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_payment_id="<?=$_GET['id']?>">
            <tr>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>技术备注</th>
                <?php if (!$isShow):?>
                <th>加工</th>
                <th>特制</th>
                <th>铭牌</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未率单价</th>
                <th>含率单价</th>
                <th>货期(周)</th>
                <th>未率总价</th>
                <th>含率总价</th>
                <?php endif;?>
                <th>数量</th>
                <th>入库</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_final_list">
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->goods->technique_remark?></td>
                    <?php if (!$isShow):?>
                    <td><?=Goods::$process[$item->goods->is_process]?></td>
                    <td><?=Goods::$special[$item->goods->is_special]?></td>
                    <td><?=Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->fixed_price?></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td><?=$item->inquiry->delivery_time?></td>
                    <td class="all_price"><?=$item->fixed_all_price?></td>
                    <td class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <?php endif;?>
                    <td class="number"><?=$item->fixed_number?></td>
                    <td><?=in_array($item->goods_id, $stock_goods_ids) ? '是' : '否'?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <?= $form->field($model, 'financial_remark')->textInput(['maxlength' => true]) ?>
        <div class="customer-view">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'stock_at',
                    'advancecharge_at',
                    'payment_at',
                    'bill_at',
                ],
            ]) ?>
        </div>
    </div>
    <div class="box-footer">
        <?= Html::button('保存备注', [
                'class' => 'btn btn-primary save_remark',
                'name'  => 'submit-button']
        )?>
        <?php if(!$model->is_advancecharge):?>
        <?= Html::button('预付款完成', [
                'class' => 'btn btn-info rimary save_advance',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_payment):?>
        <?= Html::button('全单付款完成', [
                'class' => 'btn btn-info save_payment',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
        <?php if(!$model->is_bill):?>
        <?= Html::button('收到发票', [
                'class' => 'btn btn-info save_bill',
                'name'  => 'submit-button']
        )?>
        <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var id = $('.data').data('order_payment_id');
        $('.save_remark').click(function (e) {
            var remark = $('#orderpayment-financial_remark').val();
            $.ajax({
                type:"post",
                url:'?r=financial/add-remark',
                data:{id:id, remark:remark},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_advance').click(function (e) {
            $.ajax({
                type:"post",
                url:'?r=financial/change-advance',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_payment').click(function (e) {
            $.ajax({
                type:"post",
                url:'?r=financial/change-payment',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

        $('.save_bill').click(function (e) {
            $.ajax({
                type:"post",
                url:'?r=financial/change-bill',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
