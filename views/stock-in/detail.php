<?php

use app\extend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Goods;
use app\models\PaymentGoods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '入库管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$stock_goods_ids = ArrayHelper::getColumn($stockLog, 'goods_id');
$userId   = Yii::$app->user->identity->id;

$isShow = in_array($userId, $adminIds);

?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>

    <div class="box-header">
        <?= Html::button('批量入库', [
                'class' => 'btn btn-success more-stock',
                'name'  => 'submit-button']
        )?>
        <?= Html::button('批量质检', [
                'class' => 'btn btn-info more-quality',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>

    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_payment_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>单位</th>
                <th>供应商</th>
                <?php if (!$isShow):?>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>货期(周)</th>
                <th>未税总价</th>
                <th>含税总价</th>
                <?php endif;?>
                <th>数量</th>
                <th>库存位置</th>
                <th>入库</th>
                <th>质检</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_payment_list">
                    <?php
                        $str = "<input type='checkbox' class='select_id' value={$item->id}>";
                    ?>
                    <td class="id"><?=in_array($item->goods_id, $stock_goods_ids) ? '' : $str?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->supplier->name?></td>
                    <?php if (!$isShow):?>
                    <td><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->fixed_price?></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td><?=$item->delivery_time?></td>
                    <td class="all_price"><?=$item->fixed_all_price?></td>
                    <td class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <?php endif;?>
                    <td class="number"><?=$item->fixed_number?></td>
                    <td class="position"><input type="text" value="<?=$item->stock ? $item->stock->position : ''?>" style="width: 80px;"></td>
                    <td><?=in_array($item->goods_id, $stock_goods_ids) ? '是' : '否'?></td>
                    <td class="quality_text"><?=PaymentGoods::$quality[$item->is_quality]?></td>
                    <td>
                        <?php if (!in_array($item->goods_id, $stock_goods_ids)):?>
                            <?php if(!$item->is_quality):?>
                                <a class="btn btn-success btn-xs btn-flat quality" href="javascript:void(0);" data-id="<?=$item->id?>">质检</a>
                            <?php endif;?>
                            <a class="btn btn-success btn-xs btn-flat stock_in" href="javascript:void(0);" data-goods_id="<?=$item->goods_id?>">入库</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">

        //全选
        $('.select_all').click(function (e) {
            $('.select_id').prop("checked",$(this).prop("checked"));
        });

        //子选择
        $('.select_id').on('click',function (e) {
            if ($('.select_id').length == $('.select_id:checked').length) {
                $('.select_all').prop("checked",true);
            } else {
                $('.select_all').prop("checked",false);
            }
        });


        //质检
        $('.quality').click(function (e) {
            //防止双击
            $(".quality").attr("disabled", true).addClass("disabled");
            var payment_goods_id = $(this).data('id');
            $.ajax({
                type:"post",
                url:'?r=stock-in/quality',
                data:{payment_goods_id:payment_goods_id},
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

        //批量质检
        $('.more-quality').click(function () {
            //防止双击
            $(".more-quality").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                $(".more-quality").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var paymentGoods_ids = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var id = $(element).val();
                    paymentGoods_ids.push(id);
                }
            });
            $.ajax({
                type:"post",
                url:'?r=stock-in/more-quality',
                data:{ids:paymentGoods_ids},
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

        //入库
        $('.stock_in').click(function (e) {
            //防止双击
            $(".stock_in").attr("disabled", true).addClass("disabled");
            var quality = $(this).parent().parent().find('.quality_text').text();
            if (quality == '否') {
                layer.msg('请先质检', {time:2000});
                $(".stock_in").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var order_payment_id = $('.data').data('order_payment_id');
            var goods_id          = $(this).data('goods_id');
            var number            = $(this).parent().parent().find('.number').text();
            var reg=/^\d{1,}$/;
            if (!reg.test(number)) {
                layer.msg('入库数量不能为空', {time:2000});
                $(".stock_in").removeAttr("disabled").removeClass("disabled");
                return;
            }
            var position = $(this).parent().parent().find('.position input').val();
            if (position == '') {
                layer.msg('请输入库存位置', {time:2000});
                $(".stock_in").removeAttr("disabled").removeClass("disabled");
                return;
            }

            $.ajax({
                type:"post",
                url:'?r=stock-in/in',
                data:{goods_id:goods_id, order_payment_id:order_payment_id, number:number, position:position},
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

        //批量入库
        $('.more-stock').click(function () {
            //防止双击
            $(".more-stock").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                $(".more-stock").removeAttr("disabled").removeClass("disabled");
                return false;
            }

            var position_open     = false;
            var quality_open      = false;
            var paymentGoods_info = [];
            $('.order_payment_list').each(function (i, e) {
                var item = {};
                if ($(e).find('.select_id').prop("checked")) {
                    item.payment_goods_id = $(e).find('.select_id').val();
                    item.position         = $(e).find('.position input').val();
                    if ($(e).find('.position input').val() == '') {
                        position_open = true;
                    }
                    paymentGoods_info.push(item);
                    var quality = $(e).find('.quality_text').text();
                    if (quality == '否') {
                        quality_open = true;
                    }
                }
            });
            if (quality_open) {
                layer.msg('请先质检', {time:3000});
                $(".more-stock").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            if (position_open) {
                layer.msg('请输入库存位置', {time:3000});
                $(".more-stock").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            $.ajax({
                type:"post",
                url:'?r=stock-in/more-in',
                data:{paymentGoods_info:paymentGoods_info},
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

</script>
