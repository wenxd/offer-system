<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '采购单详情';
$this->params['breadcrumbs'][] = $this->title;
if (!$model->agreement_sn) {
    $model->agreement_sn = 'HT' . date('YmdHis') . rand(10, 99);
}
if (!$model->agreement_date) {
    $model->agreement_date = substr($model->orderFinal->agreement_date, 0, 10);
}

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

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
                <th>数量</th>
                <th>采购状态</th>
                <th>合同号</th>
                <th>交货日期</th>
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
                    <td class="afterNumber"><?=$item->number?></td>
                    <td><?=$item->is_purchase ? '完成' : '未完成'?></td>
                    <td>
                        <?php if ($item->agreement_sn):?>
                            <?=$item->agreement_sn?>
                        <?php else:?>
                            <input type="text" class="agreement_sn">
                        <?php endif;?>
                    </td>
                    <td>
                        <div style="width: 140px;">
                        <?php if ($item->purchase_date):?>
                            <?=substr($item->purchase_date, 0, 10)?>
                        <?php else:?>
                            <input size="16" type="text" value="" readonly class="form_datetime delivery_date">
                        <?php endif;?>
                        </div>
                    </td>
                    <td>
                        <?php if (!$item->is_purchase):?>
                        <a class="btn btn-success btn-xs btn-flat complete" href="javascript:void(0);" data-id="<?=$item->id?>">完成采购</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ]);?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('采购员') ?>

        <?= $form->field($model, 'end_date')->textInput(['readonly' => 'true']); ?>
    </div>
    <?php if (!$orderPurchase->is_purchase):?>
    <div class="box-footer">
        <?= Html::button('完成采购', [
                'class' => 'btn btn-success purchase_complete',
                'name'  => 'submit-button']
        )?>
    </div>
    <?php endif;?>
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
                var number    = $(e).find('.afterNumber').text();
                $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            });
            var open = true;
            $('.order_final_list').each(function (i, item) {
                if ($(item).children().last().prev().text() == '未完成') {
                    open = false;
                }
            });
            if (open) {
                var date = '<?=$model->agreement_date?>';
                $('#orderpurchase-agreement_date').val(date);
            }
            $(".form_datetime").datetimepicker({
                format    : 'yyyy-mm-dd',
                startView : 2,  //其实范围（0：日  1：天 2：年）
                maxView   : 2,  //最大选择范围（年）
                minView   : 2,  //最小选择范围（年）
                autoclose : true,
            });
        }

        $('.complete').click(function (e) {
            var id = $(this).data('id');
            var this_agreement_sn = $(this).parent().parent().find('.agreement_sn').val();
            var this_delivery_date = $(this).parent().parent().find('.delivery_date').val();
            if (!this_agreement_sn) {
                layer.msg('请输入合同号', {time:2000});
                return false;
            }

            if (!this_delivery_date) {
                layer.msg('请输入交货日期', {time:2000});
                return false;
            }
            $.ajax({
                type:"post",
                url:'?r=order-purchase/complete',
                data:{id:id, this_agreement_sn:this_agreement_sn, this_delivery_date:this_delivery_date},
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

        $('.purchase_complete').click(function (e) {
            var flag = false;
            $('.order_final_list').each(function (i, item) {
                if ($(item).children().last().prev().text() == '未完成') {
                    flag = true;
                }
            });
            if (flag) {
                layer.msg('请完成每条零件的采购', {time:2000});
                return false;
            }

            var agreement_sn = $('#orderpurchase-agreement_sn').val();
            if (!agreement_sn) {
                layer.msg('请输入合同号', {time:2000});
                return false;
            }

            var agreement_date = $('#orderpurchase-agreement_date').val();
            if (!agreement_date) {
                layer.msg('请输入合同日期', {time:2000});
                return false;
            }

            var id = $('.data').data('order_purchase_id');
            $.ajax({
                type:"post",
                url:'?r=order-purchase/complete-all',
                data:{id:id, agreement_sn:agreement_sn, agreement_date:agreement_date},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-purchase/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
