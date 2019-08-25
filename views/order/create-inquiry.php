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

//$inquiryYes = [];
$inquiryInfo = [];
if ($orderInquiry) {
    foreach ($orderInquiry as $k => $item) {
        $goods_info = json_decode($item['goods_info'], true);
        foreach ($goods_info as $g) {
            //$inquiryYes[] = $g['goods_id'];
            $inquiryInfo[] = $g;
        }
    }
}

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
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
                    <th>零件号</th>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th style="width: 80px;">加工</th>
                    <th style="width: 80px;">特制</th>
                    <th style="width: 80px;">铭牌</th>
                    <th style="width: 80px;">总成</th>
                    <th>是否询价</th>
                    <th>询价数量</th>
                    <th>是否优选</th>
                    <th>库存数量</th>
                    <th>技术备注</th>
                    <th>询价单号</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td><button type="button" class="btn btn-success inquiry_search">搜索</button></td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="goods_number" value="<?=$_GET['goods_number'] ?? ''?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="goods_number_b" value="<?=$_GET['goods_number_b'] ?? ''?>">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="original_company" value="<?=$_GET['original_company'] ?? ''?>">
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <select class="form-control" name="is_process">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_process']) ? ($_GET['is_process'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_process']) ? ($_GET['is_process'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="is_special">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_special']) ? ($_GET['is_special'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_special']) ? ($_GET['is_special'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="is_nameplate">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_nameplate']) ? ($_GET['is_nameplate'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_nameplate']) ? ($_GET['is_nameplate'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="is_assembly">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_assembly']) ? ($_GET['is_assembly'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_assembly']) ? ($_GET['is_assembly'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderGoods as $key => $item):?>
                <tr>
                    <?php
                        $str = "<input type='checkbox' name='select_id' value={$item->goods_id} class='select_id'>";
                        //是否生成过询价单
                        $open = false;
                        foreach ($inquiryInfo as $n => $iv) {
                            if ($iv['goods_id'] == $item->goods_id && $iv['serial'] == $item->serial) {
                                $open = true;
                                break;
                            }
                        }
                    ?>
                    <td>
                        <?=$open ? ($item->inquiryGoods->is_result ? $str : '') : $str?>
                    </td>
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
                    <td><?=isset($inquiryList[$item->goods_id]) ? '是' : '否'?></td>
                    <td><?=isset($inquiryList[$item->goods_id]) ? count($inquiryList[$item->goods_id]) : 0?></td>
                    <td>
                        <?php
                            $is_better = '否';
                            if (isset($inquiryList[$item->goods_id])) {
                                foreach ($inquiryList[$item->goods_id] as $value) {
                                    if ($value->is_better) {
                                        $is_better = '是';
                                        break;
                                    }
                                }
                            }
                            echo $is_better;
                        ?>
                    </td>
                    <td><?=isset($stockList[$item->goods_id]) ? $stockList[$item->goods_id]->number : 0?></td>
                    <td><?= $item->goods->technique_remark?></td>
                    <td><?= $open ? ($item->inquiryGoods->is_result ? $item->inquiryGoods->inquiry_sn : '') : ''?></td>
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
                    item.serial   = $(element).parent().parent().find('.serial').text();
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

        //初始化
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
                    case 'is_process':
                        parameter += '&is_process=' + $(e).find("option:selected").val();
                        break;
                    case 'is_special':
                        parameter += '&is_special=' + $(e).find("option:selected").val();
                        break;
                    case 'is_nameplate':
                        parameter += '&is_nameplate=' + $(e).find("option:selected").val();
                        break;
                    case 'is_assembly':
                        parameter += '&is_assembly=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order/create-inquiry&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
    });
</script>
