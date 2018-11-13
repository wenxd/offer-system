<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成询价单';
$this->params['breadcrumbs'][] = $this->title;

$inquiryYes = [];
if ($orderInquiry) {
    foreach ($orderInquiry as $k => $item) {
        $goods_info = json_decode($item['goods_info'], true);
        foreach ($goods_info as $g) {
            $inquiryYes[] = $g['goods_id'];
        }
    }
}

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$model->end_date   = date('Y-m-d', (strtotime($order->provide_date) - 3600*24));
$model->inquiry_sn = 'X' . date('ymd_') . $number;

$order_goods_ids = [];
foreach ($orderGoods as $v) {
    $order_goods_ids[$v->goods_id] = $v->number;
}

?>
<style>
    .color {
        color : #5dcc6e;
    }
</style>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" name="select_all" class="select_all"></th>
                    <th>序号</th>
                    <th>零件号A</th>
                    <th>零件号B</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>加工</th>
                    <th>特制</th>
                    <th>铭牌</th>
                    <th>总成</th>
                    <th>更新时间</th>
                    <th>创建时间</th>
                    <th>技术备注</th>
                    <th>询价单号</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderGoods as $key => $item):?>
                <tr>
                    <td><?= in_array($item->goods_id, $inquiryYes) ? '' : "<input type='checkbox' name='select_id' value={$item->goods_id} class='select_id'>" ?></td>
                    <td class="serial"><?= $item->serial?></td>
                    <td><?= Html::a($item->goods->goods_number,
                            Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]),
                            ['target' => 'blank'])?></td>
                    <td><?= $item->goods->goods_number_b?></td>
                    <td><?= $item->goods->description?></td>
                    <td><?= $item->goods->description_en?></td>
                    <td><?= $item->goods->original_company?></td>
                    <td><?= $item->goods->original_company_remark?></td>
                    <td><?= $item->goods->unit?></td>
                    <td class="number"><?= $item->number?></td>
                    <td class="addColor"><?= Goods::$process[$item->goods->is_process]?></td>
                    <td class="addColor"><?= Goods::$special[$item->goods->is_special]?></td>
                    <td class="addColor"><?= Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td class="addColor"><?= Goods::$assembly[$item->goods->is_assembly]?></td>
                    <td><?= $item->goods->updated_at?></td>
                    <td><?= $item->goods->created_at?></td>
                    <td><?= $item->goods->technique_remark?></td>
                    <td><?= in_array($item->goods_id, $inquiryYes) ? $item->inquiryGoods->inquiry_sn : ''?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择询价员') ?>

        <?= $form->field($model, 'end_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' =>2,  //其实范围（0：日  1：天 2：年）
                'maxView'   =>2,  //最大选择范围（年）
                'minView'   =>2,  //最小选择范围（年）
            ]
        ]);?>

        <?= $form->field($model, 'order_id')->hiddenInput(['value' => $order->id])->label(false) ?>
        <?= $form->field($model, 'inquiry_sn')->textInput(['readonly' => true]) ?>
    </div>
    <div class="box-footer">
        <?= Html::button('保存询价单', [
                'class' => 'btn btn-success inquiry_save',
                'name'  => 'submit-button']
        )?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        init();
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
            var goods_info = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var item = {};
                    item.goods_id = $(element).val();
                    item.number   = $(element).parent().parent().find('.number').text();
                    goods_info.push(item);
                }
            });

            var admin_id = $('#orderinquiry-admin_id').val();
            var end_date = $('#orderinquiry-end_date').val();
            var order_id = $('#orderinquiry-order_id').val();
            var inquiry_sn = $('#orderinquiry-inquiry_sn').val();

            $.ajax({
                type:"post",
                url:'?r=order-inquiry/save-order',
                data:{inquiry_sn:inquiry_sn, order_id:order_id, end_date:end_date, admin_id:admin_id, goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
        function init(){
            if (!$('.select_id').length) {
                $('.select_all').hide();
                $('.inquiry_save').hide();
                document.getElementById("orderinquiry-admin_id").disabled = true;
                document.getElementById("orderinquiry-end_date").disabled = true;
            }

            $('.addColor').each(function (i, e) {
                if ($(this).text() == '是') {
                    $(this).addClass('color');
                }
            })
        }
    });
</script>
