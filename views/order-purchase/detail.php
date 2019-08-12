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

if (!$model->agreement_date) {
    $model->agreement_date = substr($model->orderAgreement->agreement_date, 0, 10);
}

$model->payment_sn = 'Z' . date('ymd_') . '_' . $number;

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_purchase_id="<?=$_GET['id']?>">
                <tr>
                    <th><input type="checkbox" name="select_all" class="select_all"></th>
                    <th>序号</th>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <th>零件号A</th>
                    <?php endif;?>
                    <th>零件号B</th>
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
                    <th>供应商缩写</th>
                    <th>税率</th>
                    <th>未率单价</th>
                    <th>含率单价</th>
                    <th>货期(天)</th>
                    <th>未率总价</th>
                    <th>含率总价</th>
                    <th>数量</th>
                    <th>采购状态</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td><button type="button" class="btn btn-success inquiry_search">搜索</button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <select class="form-control" name="supplier_id">
                            <option value=""></option>
                            <?php foreach ($supplier as $value) :?>
                                <option value="<?=$value->id?>" <?=isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : ''?>><?=$value->name?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($purchaseGoods as $item):?>
                <tr class="order_final_list">
                    <?php
                    $str = "<input type='checkbox' name='select_id' value={$item->goods_id} class='select_id'>";
                    //是否生成过支出单
                    $open = false;
//                    foreach ($inquiryInfo as $n => $iv) {
//                        if ($iv['goods_id'] == $item->goods_id && $iv['serial'] == $item->serial) {
//                            $open = true;
//                            break;
//                        }
//                    }
                    ?>
                    <td>
                        <?=$str?>
                    </td>
                    <td><?=$item->serial?></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?></td>
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
                    <td><?=$item->type ? $item->stock->supplier->short_name : $item->inquiry->supplier->short_name?></td>
                    <td><?=$item->type ? $item->stock->tax_rate : $item->inquiry->tax_rate?></td>
                    <td class="price"><?=$item->type ? $item->stock->price : $item->inquiry->price?></td>
                    <td class="tax_price"><?=$item->type ? $item->stock->tax_price : $item->inquiry->tax_price?></td>
                    <td><?=$item->type ? '' : $item->inquiry->delivery_time?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td class="afterNumber"><?=$item->number?></td>
                    <td><?=$item->is_purchase ? '完成' : '未完成'?></td>
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
        ])->hiddenInput()->label(false);?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('采购员') ?>

        <?= $form->field($model, 'end_date')->textInput(['readonly' => 'true']); ?>

        <?= $form->field($model, 'payment_sn')->textInput(); ?>

    </div>
    <div class="box-footer">
        <?= Html::button('保存支出合同单', [
                'class' => 'btn btn-success payment_save',
                'name'  => 'submit-button']
        )?>
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

        //搜索功能
        $('.inquiry_search').click(function (e) {
            var search = $('#w3-filters').find('td input');
            var parameter = '';
            search.each(function (i, e) {
                switch ($(e).attr('name')) {
                    case 'goods_number':
                        parameter += '&goods_number=' + $(e).val();
                        break;
                    case 'goods_number_b':
                        parameter += '&goods_number_b=' + $(e).val();
                        break;
                    case 'original_company':
                        parameter += '&original_company=' + $(e).val();
                        break;
                    default:
                        break;
                }
            });
            var searchOption = $('#w3-filters').find('td select');
            searchOption.each(function (i, e) {
                switch ($(e).attr('name')) {
                    case 'supplier_id':
                        parameter += '&supplier_id=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order-purchase/detail&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
        
        $(".payment_save").click(function () {
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
                    item.serial   = $(element).parent().parent().find('.serial').text();
                    goods_info.push(item);
                }
            });
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
    });
</script>
