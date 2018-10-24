<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

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
            <thead class="data" data-order_purchase_id="<?=$_GET['id']?>">
            <tr>
                <th>零件号</th>
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
                <th>图片</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未率单价</th>
                <th>含率单价</th>
                <th>货期(天)</th>
                <th>未率总价</th>
                <th>含率总价</th>
                <?php endif;?>
                <th>数量</th>
                <th>采购状态</th>
                <th>入库</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($purchaseGoods as $item):?>
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
                    <td><?=Html::img($item->goods->img_url, ['width' => '50px'])?></td>
                    <td><?=$item->type ? $item->stock->supplier->name : $item->inquiry->supplier->name?></td>
                    <td><?=$item->type ? $item->stock->tax_rate : $item->inquiry->tax_rate?></td>
                    <td class="price"><?=$item->type ? $item->stock->price : $item->inquiry->price?></td>
                    <td class="tax_price"><?=$item->type ? $item->stock->tax_price : $item->inquiry->tax_price?></td>
                    <td><?=$item->type ? '' : $item->inquiry->delivery_time?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <?php endif;?>
                    <td class="number"><?=$item->number?></td>
                    <td><?=$item->is_purchase ? '完成' : '未完成'?></td>
                    <td><?=in_array($item->goods_id, $stock_goods_ids) ? '是' : '否'?></td>
                    <td>
                        <?php if (!in_array($item->goods_id, $stock_goods_ids)):?>
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
    $(document).ready(function () {
        init();
        function init(){
            $('.order_final_list').each(function (i, e) {
                var price     = $(e).find('.price').text();
                var tax_price = $(e).find('.tax_price').text();
                var number    = $(e).find('.number').text();
                $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            });
        }

        $('.stock_in').click(function (e) {
            var order_purchase_id = $('.data').data('order_purchase_id');
            var goods_id          = $(this).data('goods_id');
            var number            = $(this).parent().parent().find('.number').text();

            var reg=/^\d{1,}$/;
            if (!reg.test(number)) {
                layer.msg('入库数量不能为空', {time:2000});
            }

            $.ajax({
                type:"post",
                url:'?r=stock-in/in',
                data:{goods_id:goods_id, order_purchase_id:order_purchase_id, number:number},
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
