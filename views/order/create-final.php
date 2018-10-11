<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;

$this->title = '生成最终订单';
$this->params['breadcrumbs'][] = $this->title;

$model->end_date   = date('Y-m-d', (strtotime($order->provide_date) - 3600*24));
$model->inquiry_sn = date('YmdHis') . rand(1000, 9999);

$inquiry_goods_ids = ArrayHelper::getColumn($inquiry, 'good_id');

?>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>零件号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>原厂家备注</th>
                        <th>单位</th>
                        <th>是否加工</th>
                        <th>是否特制</th>
                        <th>是否铭牌</th>
                        <th>更新时间</th>
                        <th>创建时间</th>
                        <th>技术备注</th>
                        <th>是否关联询价记录</th>
                        <th>询价ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goods as $key => $good):?>
                    <tr>
                        <td><?= Html::a($good->goods_number, Url::to(['inquiry/index', 'InquirySearch[goods_number]' => $good->goods_number ]));?></td>
                        <td><?= $good->description?></td>
                        <td><?= $good->description_en?></td>
                        <td><?= $good->original_company?></td>
                        <td><?= $good->original_company_remark?></td>
                        <td><?= $good->unit?></td>
                        <td><?= Goods::$process[$good->is_process]?></td>
                        <td><?= Goods::$special[$good->is_special]?></td>
                        <td><?= Goods::$nameplate[$good->is_nameplate]?></td>
                        <td><?= $good->updated_at?></td>
                        <td><?= $good->created_at?></td>
                        <td><?= $good->technique_remark?></td>
                        <td><?= in_array($good->id, $inquiry_goods_ids) ? '是' : '否'?></td>
                        <td><?= isset($inquiry[$good->id]) ? Html::a($inquiry[$good->id]['id'], Url::to(['inquiry/view', 'id' => $inquiry[$good->id]['id']])) : ''?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="box-footer">
            <?= Html::button('保存最终订单', [
                    'class' => 'btn btn-success inquiry_save',
                    'name'  => 'submit-button']
            )?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

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
        //保存询价单
        $('.inquiry_save').click(function (e) {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }
            var goods_ids = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    goods_ids.push($(element).val());
                }
            });

            var admin_id = $('#orderinquiry-admin_id').val();
            var end_date = $('#orderinquiry-end_date').val();
            var order_id = $('#orderinquiry-order_id').val();
            var inquiry_sn = $('#orderinquiry-inquiry_sn').val();

            $.ajax({
                type:"post",
                url:'?r=order-inquiry/save-order',
                data:{inquiry_sn:inquiry_sn, order_id:order_id, end_date:end_date, admin_id:admin_id, goods_ids:goods_ids},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-inquiry/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
