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
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_id="<?=$_GET['id']?>">
            <tr>
                <th>零件号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>技术备注</th>
                <?php if (!$isShow):?>
                <th>是否加工</th>
                <th>是否特制</th>
                <th>是否铭牌</th>
                <th>图片</th>
                <?php endif;?>
                <th>数量</th>
                <th>是否出库</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderGoods as $item):?>
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
                    <?php endif;?>
                    <td class="number"><?=$item->number?></td>
                    <td><?=$item->is_out ? '是' : '否'?></td>
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

        $('.stock_out').click(function (e) {
            var order_id = $('.data').data('order_id');
            var id       = $(this).data('id');
            var number   = $(this).parent().parent().find('.number').text();

            var reg=/^\d{1,}$/;
            if (!reg.test(number)) {
                layer.msg('入库数量不能为空', {time:2000});
            }

            $.ajax({
                type:"post",
                url:'?r=stock-out/out',
                data:{order_id:order_id, id:id},
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
