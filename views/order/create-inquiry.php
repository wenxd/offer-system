<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\InquiryGoods;
use app\models\AuthAssignment;

if ($level == 1) {
    $this->title = '生成询价单(顶)';
} else {
    $this->title = '生成询价单(子)';
}
$this->params['breadcrumbs'][] = $this->title;

$inquiryInfo = [];
if ($orderInquiry) {
    foreach ($orderInquiry as $item) {
        $itemList = InquiryGoods::find()->where([
            'order_id'          => $item['order_id'],
            'order_inquiry_id'  => $item['id'],
        ])->asArray()->all();
        $inquiryInfo = array_merge($inquiryInfo, $itemList);
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

$model->inquiry_sn = 'X' . date('ymd_') . $number;

$order_goods_ids = [];
foreach ($orderGoods as $v) {
    $order_goods_ids[$v->goods_id] = $v->number;
}

if ($model->isNewRecord) {
    $model->end_date   = date('Y-m-d', time() + 3600 * 24 * 2);
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
        <table id="example2" class="table table-bordered table-hover" style="width: 1800px; table-layout: auto">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" name="select_all" class="select_all">
                    </th>
                    <th>序号</th>
                    <th>品牌</th>
                    <th>零件号</th>
                    <th>厂家号</th>
                    <th style="width: 200px;">推荐供应商</th>
                    <th style="width: 200px;">特殊说明</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>主零件</th>
                    <th style="width: 80px;">加工</th>
                    <th style="width: 80px;">特制</th>
                    <th style="width: 80px;">进口</th>
                    <th style="width: 80px;">标准</th>
                    <th style="width: 80px;">铭牌</th>
                    <th style="width: 80px;">总成</th>
                    <th style="width: 80px;">是否有询价记录</th>
                    <th>询价条目</th>
                    <th style="width: 80px;">是否优选</th>
                    <th>库存数量</th>
                    <th>技术备注</th>
                    <th>询价单号</th>
                </tr>
                <tr id="w3-filters" class="filters">
                    <td>
                        <button type="button" class="btn btn-success btn-xs inquiry_search">搜索</button>
                    </td>
                    <td>
                        <?=Html::a('复位', '?r=order/create-inquiry&id=' . $_GET['id'], ['class' => 'btn btn-info btn-xs'])?>
                    </td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="goods_number" value="<?=$_GET['goods_number'] ?? ''?>">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="goods_number_b" value="<?=$_GET['goods_number_b'] ?? ''?>">
                    </td>
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
                    <td>
                        <input type="text" class="form-control" name="belong_to" value="<?=$_GET['belong_to'] ?? ''?>">
                    </td>
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
                        <select class="form-control" name="is_import">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_import']) ? ($_GET['is_import'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_import']) ? ($_GET['is_import'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="is_standard">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_standard']) ? ($_GET['is_standard'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_standard']) ? ($_GET['is_standard'] === '1' ? 'selected' : '') : ''?>>是</option>
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
                    <td>
                        <select class="form-control" name="is_inquiry">
                            <option value=""></option>
                            <option value="0" <?=isset($_GET['is_inquiry']) ? ($_GET['is_inquiry'] === '0' ? 'selected' : '') : ''?>>否</option>
                            <option value="1" <?=isset($_GET['is_inquiry']) ? ($_GET['is_inquiry'] === '1' ? 'selected' : '') : ''?>>是</option>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
                        <?=$open ?  '' : $str?>
                    </td>
                    <td class="serial"><?= $item->serial?></td>
                    <td><?= $item->goods->material_code?></td>
                    <td><?= Html::a($item->goods->goods_number,
                            Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]),
                            ['target' => 'blank'])?></td>
                    <td><?= $item->goods->goods_number_b?></td>
                    <td class="supplier">
                        <input type="text" style="width: 150px;">
                    </td>
                    <td class="remark">
                        <input type="text" style="width: 150px;">
                    </td>
                    <td><?= $item->goods->description?></td>
                    <td><?= $item->goods->description_en?></td>
                    <td><?= $item->goods->original_company?></td>
                    <td><?= $item->goods->original_company_remark?></td>
                    <td><?= $item->goods->unit?></td>
                    <td class="number"><?= $item->number?></td>
                    <td><?php
                        $text = '';
                        foreach (json_decode($item->belong_to, true) as $key => $device) {
                            $text .= $key . ':' . $device . '<br/>';
                        }
                        echo $text;
                        ?>
                    </td>
                    <td class="belong_to" style="display: none"><?=$item->belong_to?></td>
                    <td class="addColor"><?= Goods::$process[$item->goods->is_process]?></td>
                    <td class="addColor"><?= Goods::$special[$item->goods->is_special]?></td>
                    <td class="addColor"><?= Goods::$import[$item->goods->is_import]?></td>
                    <td class="addColor"><?= Goods::$nameplate[$item->goods->is_standard]?></td>
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
                    <td><?= $open ? $item->inquiryGoods->inquiry_sn : ''?></td>
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
    <?php if (!$order->is_dispatch):?>

    <?php endif;?>
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
    var level = <?=$level?>;
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
            //防止双击
            $(".inquiry_save").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                $(".inquiry_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var goods_info = [];
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    var item = {};
                    item.goods_id      = $(element).val();
                    item.number        = $(element).parent().parent().find('.number').text();
                    item.serial        = $(element).parent().parent().find('.serial').text();
                    item.belong_to     = $(element).parent().parent().find('.belong_to').text();
                    var supplier_name  = $(element).parent().parent().find('.supplier input').val();
                    var remark         = $(element).parent().parent().find('.remark input').val();
                    item.supplier_name = supplier_name;
                    item.remark = remark;
                    goods_info.push(item);
                }
            });

            var admin_id   = $('#orderinquiry-admin_id').val();
            var end_date   = $('#orderinquiry-end_date').val();
            var order_id   = $('#orderinquiry-order_id').val();
            var inquiry_sn = $('#orderinquiry-inquiry_sn').val();
            console.log({inquiry_sn:inquiry_sn, order_id:order_id, end_date:end_date, admin_id:admin_id, goods_info:goods_info, level:level});
            $.ajax({
                type:"post",
                url:'?r=order-inquiry/save-order',
                data:{inquiry_sn:inquiry_sn, order_id:order_id, end_date:end_date, admin_id:admin_id, goods_info:goods_info, level:level},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                        $(".inquiry_save").removeAttr("disabled").removeClass("disabled");
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
                    case 'belong_to':
                        parameter += '&belong_to=' + $(e).val();
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
                    case 'is_import':
                        parameter += '&is_import=' + $(e).find("option:selected").val();
                        break;
                    case 'is_standard':
                        parameter += '&is_standard=' + $(e).find("option:selected").val();
                        break;
                    case 'is_nameplate':
                        parameter += '&is_nameplate=' + $(e).find("option:selected").val();
                        break;
                    case 'is_assembly':
                        parameter += '&is_assembly=' + $(e).find("option:selected").val();
                        break;
                    case 'is_inquiry':
                        parameter += '&is_inquiry=' + $(e).find("option:selected").val();
                        break;
                    case 'is_inquiry_better':
                        parameter += '&is_inquiry_better=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order/create-inquiry&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });
    });
</script>
