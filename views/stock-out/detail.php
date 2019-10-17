<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '出库管理';
$this->params['breadcrumbs'][] = $this->title;

$stock_goods_ids = ArrayHelper::getColumn($stockLog, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
$isShow = in_array($userId, $adminIds);

?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>

    <div class="box-header">
        <?= Html::button('批量出库', [
                'class' => 'btn btn-success more-stock',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>

    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_agreement_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
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
                <th>图片</th>
                <?php endif;?>
                <th>数量</th>
                <th>出库</th>
                <th>库存数量</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
                <tr class="order_final_list">
                    <?php
                        $str = "<input type='checkbox' class='select_id' value={$item->id}>";
                    ?>
                    <td class="id"><?=in_array($item->goods_id, $stock_goods_ids) ? '' : $str?></td>
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
                    <?php endif;?>
                    <td class="number"><?=$item->number?></td>
                    <td><?=$item->is_out ? '是' : '否'?></td>
                    <td><?=$item->stock ? $item->stock->number : 0?></td>
                    <td>
                        <?php if (!$item->is_out):?>
                            <a class="btn btn-success btn-xs btn-flat stock_out" href="javascript:void(0);" data-id="<?=$item->id?>">出库</a>
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

        //出库
        $('.stock_out').click(function (e) {
            var order_agreement_id = $('.data').data('order_agreement_id');
            var id       = $(this).data('id');
            var number   = $(this).parent().parent().find('.number').text();

            var reg=/^\d{1,}$/;
            if (!reg.test(number)) {
                layer.msg('出库数量不能为空', {time:2000});
            }

            $.ajax({
                type:"post",
                url:'?r=stock-out/out',
                data:{order_agreement_id:order_agreement_id, id:id},
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

        //批量出库
        $('.more-stock').click(function () {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }
            var paymentGoods_ids = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var id = $(element).val();
                    paymentGoods_ids.push(id);
                }
            });
            var order_agreement_id = "<?=$_GET['id'] ?? ''?>";
            $.ajax({
                type:"post",
                url:'?r=stock-out/more-out',
                data:{ids:paymentGoods_ids, order_agreement_id:order_agreement_id},
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
