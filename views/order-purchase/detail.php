<?php

use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use \app\models\Supplier;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '提交支出申请';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->agreement_date) {
    $model->agreement_date = date('Y-m-d');
}
$model->payment_sn = 'Z' . date('ymd_') . '_' . $number;
$model->payment_ratio = 30;
$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;

// 查询所有已确定零件，隐藏回退按钮
$where = ['order_id' => $orderPurchase->order_id, 'order_purchase_id' => $orderPurchase->id, 'source' => 'payment', 'is_confirm' => 1];
$payment_confirm_goods = \app\models\AgreementStock::find()->where($where)->asArray()->all();
$confirm_goods_id = ArrayHelper::getColumn($payment_confirm_goods, 'goods_id');

//显示按钮开关
$i = 0;

$model->delivery_date = date('Y-m-d');

//收入合同交货日期
$model->end_date = $order_agreement_at = $orderPurchase->orderAgreement ? substr($orderPurchase->orderAgreement->agreement_date, 0, 10) : $orderPurchase->end_date;

// 获取税率
$tax_arr = \app\models\SystemConfig::find()->select('value')->where(['title' => 'tax'])->asArray()->one();
$tax = $tax_arr['value'] ?? 13;
$model->tax_rate = $tax;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-header">

        <div class="col-md-12">
            <div class="col-md-8">
                <?= Html::a('导出', Url::to(['download', 'id' => $_GET['id']]), [
                    'data-pjax' => '0',
                    'class' => 'btn btn-primary btn-flat',
                ]) ?>
                <?= Html::input('text', 'select_serial', null, [
                    'class' => 'select_serial',
                    'placeholder' => '请输入序号多个用|分割，如 1|13|25|37',
                    'style' => 'margin-left:100px; width:300px;'
                ]) ?>
                <?= Html::button('选择', [
                    'class' => 'btn btn-success btn-flat select_ack',
                ]) ?>
                <?= Html::button('一键走库存', ['class' => 'btn btn-primary btn-flat', 'onclick' => 'exit_stock()']) ?>
                <script>
                    function exit_stock() {
                        $('.agreement_number').each(function (index, element) {
                            // 合同需求数量
                            var agreement_number = parseInt($(element).text());
                            // // 库存数量
                            var stock_number = parseInt($(this).parent().find('.stock_number').text());
                            // 库存数量 < 合同需求数量
                            if (stock_number < agreement_number) {
                                $(this).parent().find('.afterNumber').find('.number').val(agreement_number - stock_number);
                                $(this).parent().find('.use_number').text(stock_number);
                            } else {
                                $(this).parent().find('.afterNumber').find('.number').val(0);
                                $(this).parent().find('.use_number').text(agreement_number);
                            }
                        });

                    }
                </script>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'goods_info')->widget(\kartik\select2\Select2::className(), [
                    'data' => ArrayHelper::map(Goods::getGoodsCode(), 'goods_id', 'info'),
                    'options' => ['placeholder' => '请输入输入零件号或者厂家号', 'class' => 'form-control'],
                ])->label(false) ?>
            </div>
            <div class="col-md-2">
                <?= Html::button('添加', ['onclick' => 'add_goods()', 'class' => 'btn btn-primary', 'style' => 'width:60px']); ?>
            </div>
            <script>
                var OrderPurchase = <?=json_encode($model->toArray())?>;

                function add_goods() {
                    var goods_id = $('#orderpurchase-goods_info').val();
                    if (goods_id === '') {
                        layer.msg('输入厂家号或者厂家号', {time: 2000});
                        return false;
                    }
                    OrderPurchase.goods_id = goods_id;
                    $.ajax({
                        type:"post",
                        url:"?r=order-purchase/add-goods",
                        data:OrderPurchase,
                        dataType:'JSON',
                        success:function(res){
                            console.log(res);
                            if (res && res.code == 200) {
                                layer.msg(res.msg, {time: 2000});
                                location.reload();
                            } else {
                                layer.msg(res.msg, {time: 2000});
                                return false;
                            }
                        }
                    });

                }
            </script>
        </div>

    </div>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover" style="width: 2000px;">
            <thead class="data" data-order_purchase_id="<?= $_GET['id'] ?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>序号</th>
                <?php if (!in_array($userId, $adminIds)): ?>
                    <th>零件号</th>
                <?php endif; ?>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>原厂家</th>
                <th>供应商</th>
                <th>单位</th>
                <th>支出合同数量</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>未税总价</th>
                <th>含税总价</th>
                <th>货期(周)</th>
                <th width="80px;">是否入库</th>
                <th width="80px;">是否新增</th>
                <th>税率</th>
                <th>采购单需求数量</th>
                <th>使用库存数量</th>
                <th>使用库存状态</th>
                <th>临时库存数量</th>
                <th>库存数量</th>
                <th>审核状态</th>
                <th>驳回原因</th>
                <th>生成支出合同</th>
                <th>操作</th>
            </tr>
            <tr id="w3-filters" class="filters">
                <td>
                    <button type="button" class="btn btn-success btn-xs inquiry_search">搜索</button>
                </td>
                <td>
                    <?= Html::a('复位', '?r=order-purchase/detail&id=' . $_GET['id'], ['class' => 'btn btn-info btn-xs']) ?>
                </td>
                <?php if (!in_array($userId, $adminIds)): ?>
                    <td></td>
                <?php endif; ?>
                <td></td>
                <td></td>
                <td>
                    <input type="text" class="form-control" name="original_company"
                           value="<?= $_GET['original_company'] ?? '' ?>">
                </td>
                <td>
                    <select class="form-control" name="supplier_id">
                        <option value=""></option>
                        <?php foreach ($supplier as $value) : ?>
                            <option value="<?= $value->id ?>"
                                    <?= isset($_GET['supplier_id']) ? ($_GET['supplier_id'] === "$value->id" ? 'selected' : '') : '' ?>><?= $value->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <select class="form-control" name="is_stock">
                        <option value=""></option>
                        <option value="0"
                                <?= isset($_GET['is_stock']) ? ($_GET['is_stock'] === "$value->id" ? 'selected' : '') : '' ?>>
                            否
                        </option>
                        <option value="1"
                                <?= isset($_GET['is_stock']) ? ($_GET['is_stock'] === "$value->id" ? 'selected' : '') : '' ?>>
                            是
                        </option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="after">
                        <option value="0"></option>
                        <option value="1"
                                <?= isset($_GET['after']) ? ($_GET['after'] == 1 ? 'selected' : '') : '' ?>>
                            否
                        </option>
                        <option value="2"
                                <?= isset($_GET['after']) ? ($_GET['after'] == 2 ? 'selected' : '') : '' ?>>
                            是
                        </option>
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
            <?php foreach ($purchaseGoods as $item): ?>
                <tr class="order_purchase_list">
                    <?php
                    $str = "<input type='checkbox' name='select_id' value={$item->goods_id} class='select_id'>";
                    //是否生成过支出单
                    $open = true;
                    $purchaseGoodsIds = ArrayHelper::getColumn($paymentGoods, 'purchase_goods_id');
                    if (in_array($item->id, $purchaseGoodsIds)) {
                        $open = false;
                        $i++;
                    }
                    ?>
                    <td>
                        <?= $open || $item->after == 1 || $item->after == 9 ? $str : '' ?>
                    </td>
                    <td class="purchase_detail" data-purchase_goods_id="<?= $item->id ?>"
                        data-serial="<?= $item->serial ?>">
                        <?= $item->serial ?>
                    </td>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <td><?= $item->goods->goods_number . ' ' . $item->goods->material_code ?></td>
                    <?php endif; ?>
                    <td><?= $item->goods->goods_number_b ?><?= Html::a(' 询价记录', Url::to(['inquiry-temp/inquiry', 'id' => $item->goods_id])) ?></td>
                    <td><?= $item->goods->description ?></td>
                    <td><?= $item->goods->original_company ?></td>
                    <td class="supplier_name"><?= $item->inquiry->supplier->name ?></td>
                    <td><?= $item->goods->unit ?></td>
                    <td class="afterNumber">
                        <input type="number" size="4" class="number" min="1" style="width: 50px;"
                               value="<?= $item->fixed_number ?>">
                    </td>
                    <td class="price"><input type="text" value="<?= $item->fixed_price ?>" style="width: 100px;"></td>
                    <td class="tax_price"><input type="text" value="<?= $item->fixed_tax_price ?>"
                                                 style="width: 100px;"></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td class="delivery_time"><input type="text" value="<?= $item->delivery_time ?>"
                                                     style="width: 100px;"></td>
                    <td><?= $item::$stock[$item->is_stock] ?></td>
                    <td><?= $item->after ? '是' : "否" ?></td>
                    <!--税率$open-->
                    <td class="tax">
                        <input type="number" disabled='disabled' purchase_id="<?= $item->id ?>" size="4" min="0" tax_rate="<?= $item->tax_rate ?>"  style="width: 100px;" <?= !$open ? "disabled='disabled'" : '' ?>  value="<?= $item->tax_rate ?>">
                    </td>
                    <td class="agreement_number"><?= $item->number ?></td>
                    <td class="use_number"><?= $item->fixed_stock_number ?></td>
                    <td><?php
                        $stock_status = $item->is_fixed_stock ?? 0;
                        switch ($stock_status) {
                            case 1:
                                echo '待审核';
                                break;
                            case 9:
                                echo '确认';
                                break;
                            case 4:
                                echo '驳回';
                                break;
                        }
                        ?></td>
                    <td class="stock_number"><?php
//                        $stock = $item->stock ? $item->stock->number : 0;
//                        $use_number = $item->orderstock->use_number ?? 0;
//                        echo $stock - $use_number;
                        echo $item->stock ? $item->stock->temp_number : 0;
                        ?></td>
                    <td><?= $item->stock ? $item->stock->number : 0; ?></td>
                    <td><?php
                        if ($item->apply_status == 0) {
                            $status = '无';
                        } elseif ($item->apply_status == 1) {
                            $status = '审核中';
                        } elseif ($item->apply_status == 2) {
                            $status = '审核通过';
                        } elseif ($item->apply_status == 3) {
                            $status = '被驳回';
                        }
                        echo $status;
                        ?>
                    </td>
                    <td><?= $item->reason ?></td>
                    <!--勾选生成支出合同-->
                    <td class="contract"><?php
                        echo '<input type="checkbox" checked="checked" onclick="return false;" />';
                        if ($item->after == 0 ) {
                            /*onclick="return false;"*/
                        } else {
//                            echo '<input type="checkbox" checked="checked" onclick="exit_contract(this)"/>';
                        }
                        ?>
                        <script>
                            function exit_contract(obj) {
                                var checked_status = $(obj).prop('checked');
                                if (checked_status) {
                                    var checked_msg = '确认生成支出合同？';
                                    checked_status = false;
                                } else {
                                    var checked_msg = '放弃生成支出合同？';
                                    checked_status = 'checked';
                                }

                                layer.confirm(checked_msg, {
                                    btn: ['确认','取消'] //按钮
                                }, function(){
                                    layer.msg('确认成功', {icon: 1});
                                }, function(){
                                    $(obj).prop('checked', checked_status);
                                });
                            }
                        </script>
                    </td>
                    <td><?php
                        if (!in_array($item->apply_status, [1, 2])) {
                            if ($item->after == 1 && $open) {
                                echo Html::button('删除', [
                                    'class' => 'btn btn-danger btn-sm',
                                    'onclick' => "del_goods($item->id)"
                                ]);
                            }
                            if ($item->after == 0 && $open) {
                                if (!in_array($item->goods_id, $confirm_goods_id)) {
                                    echo Html::button('回退', [
                                        'class' => 'btn btn-success btn-sm',
                                        'onclick' => "exit_goods($item->id)"
                                    ]);
                                }
                            }
                        }
                         ?>
                    </td>
                    <script>
                        function exit_goods(id) {
                            console.log(id);
                            $.ajax({
                                type:"get",
                                url:"?r=order-purchase/exit-goods",
                                data:{id:id},
                                dataType:'JSON',
                                success:function(res){
                                    console.log(res);
                                    layer.msg(res.msg, {time: 2000});
                                    if (res && res.code == 200) {
                                        location.reload();
                                    }if (res && res.code == 202) {
                                        window.history.go(-1);
                                    } else {
                                        return false;
                                    }
                                }
                            });
                        }
                        function del_goods(id) {
                            console.log(id);
                            $.ajax({
                                type:"post",
                                url:"?r=order-purchase/del-goods",
                                data:{id:id},
                                dataType:'JSON',
                                success:function(res){
                                    console.log(res);
                                    if (res && res.code == 200) {
                                        layer.msg(res.msg, {time: 2000});
                                        location.reload();
                                    } else {
                                        layer.msg(res.msg, {time: 2000});
                                        return false;
                                    }
                                }
                            });
                        }
                    </script>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: #acccb9">
                <td colspan="<?= in_array($userId, $adminIds) ? 11 : 12 ?>" rowspan="2">汇总统计</td>
                <td>含税总价合计</td>
                <td colspan="8" rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="stat_all_tax_price"></td>
            </tr>
            </tbody>
        </table>
        <?= $form->field($model, 'pay_type')->widget(Select2::classname(), [
            'data' => \app\models\OrderPurchase::PAYTYPE,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('付款流程') ?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins, ['disabled' => true])->label('采购员') ?>

        <?= $form->field($model, 'end_date')->textInput(['readonly' => 'true']); ?>

        <?= $form->field($model, 'supplier_id')->widget(Select2::classname(), [
            'data' => Supplier::getCreateDropDown(),
            'options' => ['placeholder' => '选择供应商'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('供应商') ?>

        <?= $form->field($model, 'payment_ratio')->textInput(['placeholder' => '例如0.3 就是百分之30 不用加%']); ?>
        <?php if (!$model->is_complete): ?>
        <?php endif; ?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView' => 2,  //最大选择范围（年）
                'minView' => 2,  //最小选择范围（年）
            ]
        ])->label('支出合同签订时间'); ?>

        <?= $form->field($model, 'delivery_date')->widget(DateTimePicker::className(), [
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView' => 2,  //最大选择范围（年）
                'minView' => 2,  //最小选择范围（年）
            ]
        ])->label('支出合同交货时间'); ?>

        <?= $form->field($model, 'apply_reason')->textInput()->label('申请备注'); ?>
        <?= $form->field($model, 'tax_rate')->textInput([
                'placeholder' => '税率必须为整数', 'type' => 'number',
                'onkeyup' => "this.value=this.value.replace(/\D/g,'')", 'onafterpaste' => "this.value=this.value.replace(/\D/g,'')",
        ]); ?>
        <?= $form->field($model, 'payment_sn')->textInput(); ?>
        <?php if (!$model->is_complete): ?>
        <?php endif; ?>

    </div>
    <div class="box-footer">
        <?= Html::button('保存采购数量/使用库存', ['class' => 'btn btn-primary purchase_number_save', 'name' => 'submit-button']) ?>
        <?= Html::button('保存税率', ['class' => 'btn btn-primary tax_save', 'name' => 'submit-button']) ?>

        <?php
        if ($orderPurchase->is_purchase_number == 1) {
            $count = \app\models\AgreementStock::find()
                ->where(['order_id' => $orderPurchase->order_id, 'order_purchase_id' => $orderPurchase->id, 'is_confirm' => \app\models\AgreementStock::IS_CONFIRM_NO])
                ->count();
            if (!$count) {
                // 没有保存采购策略不允许保存采购订单
                echo Html::button('提交支出申请', [
                        'class' => 'btn btn-success payment_save',
                        'name' => 'submit-button']
                );
            } else {
                echo "<p class='text-danger'>使用库存未确认 * {$count}</p>";
            }

        } else {
            echo "<p class='text-danger'>没有保存采购单采购数量/使用库存</p>";
        }
         ?>
    </div>
    <?php if (!$model->is_complete): ?>

    <?php endif; ?>
    <?php ActiveForm::end(); ?>
</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    var base_tax = "<?=$tax?>"
    // 修改是否生成支出合同
    function exit_pay(){
        $('.select_id').each(function (index, element) {
            var pay_type = $('#orderpurchase-pay_type').val();
            var checked_status = true;
            if (pay_type == 2) {
                checked_status = false;
            }
            if ($(element).prop("checked")) {
                $(element).parent().parent().find('.contract input').prop('checked', checked_status);
            }
        });
    }
    $(document).ready(function () {

        //保存采购数量/使用库存
        $('.purchase_number_save').click(function (e) {
            var goods_info = [];
            $('.select_id').each(function (index, element) {
                var purchase_goods_id = $(element).parent().parent().find('.purchase_detail').data('purchase_goods_id');
                var number = $(element).parent().parent().find('.afterNumber input').val();
                goods_info.push({purchase_goods_id:purchase_goods_id,number:number});
            });
            if (goods_info.length) {
                $.ajax({
                    type:"post",
                    url:'<?=$_SERVER['REQUEST_URI']?>',
                    data:{goods_info:goods_info},
                    dataType:'JSON',
                    success:function(res){
                        console.log(res);
                        if (res && res.code == 200){
                            layer.msg(res.msg, {time:2000});
                            window.location.reload();
                        } else {
                            layer.msg(res.msg, {time:2000});
                        }
                    }
                });
            } else {
                layer.msg('无可勾选数据保存', {time: 2000});
            }

        });

        //保存税率
        $('.tax_save').click(function (e) {
            var goods_info = [];
            var status = false;
            $('.tax input').each(function (index, element) {
                var disabled = $(element).attr('disabled');
                // if (disabled != 'disabled') {
                    var tax = parseFloat($(element).val()).toFixed(2);
                    var tax_rate = $(element).attr('tax_rate');
                    if (tax != tax_rate) {
                        status = true;
                        var tax_price = $(element).parent().parent().find('.tax_price input').val();
                        var all_tax_price = $(element).parent().parent().find('.all_tax_price').text();
                        var purchase_goods_id = $(element).attr('purchase_id');
                        goods_info.push({purchase_goods_id:purchase_goods_id,tax:tax,tax_price:tax_price,all_tax_price:all_tax_price});

                    }
                // }
            });
            if (!status) {
                layer.msg('没有税率更新', {time: 2000});
                return false;
            }
            $.ajax({
                type:"post",
                url:'?r=search/order-purchase-tax-save',
                data:{goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    console.log(res);
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time:2000});
                    }
                }
            });
        });

        //全选
        $('.select_all').click(function (e) {
            $('.select_id').prop("checked", $(this).prop("checked"));
            stat();
            exit_pay();
        });

        //子选择
        $('.select_id').on('click', function (e) {
            if ($('.select_id').length == $('.select_id:checked').length) {
                $('.select_all').prop("checked", true);
            } else {
                $('.select_all').prop("checked", false);
            }
            stat();
            exit_pay();
        });

        init();

        function init() {
            $('.order_purchase_list').each(function (i, e) {
                var price = $(e).find('.price input').val();
                var tax_price = $(e).find('.tax_price input').val();
                var number = $(e).find('.afterNumber input').val();
                $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
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
                    case 'is_stock':
                        parameter += '&is_stock=' + $(e).find("option:selected").val();
                        break;
                    case 'after':
                        parameter += '&after=' + $(e).find("option:selected").val();
                        break;
                    default:
                        break;
                }
            });
            location.replace("?r=order-purchase/detail&id=<?=$_GET['id']?>" + encodeURI(parameter));
        });

        //输入未税单价
        $(".price input").bind('input propertychange', function (e) {
            var tax = parseFloat($(this).parent().parent().find('.tax input').val()).toFixed(2);
            var price = parseFloat($(this).val());
            var tax_price = (price * (1 + tax / 100)).toFixed(2);
            var number = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.tax_price input').val(tax_price);
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            stat();
        });

        //输入含税单价
        $(".tax_price input").bind('input propertychange', function (e) {
            var tax = parseFloat($(this).parent().parent().find('.tax input').val()).toFixed(2);
            var tax_price = parseFloat($(this).val());
            var price = (tax_price / (1 + tax / 100)).toFixed(2);
            var number = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.price input').val(price);
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            stat();
        });

        //输入整体税率
        $("#orderpurchase-tax_rate").bind('input propertychange', function (e) {
            var tax = parseInt($(this).val());
            if (tax < 0 || tax == 'NaN') {
                $(this).val(0);
                tax = 0;
            }
            $('.select_id').each(function (index, element) {
                console.log(tax);
                var parent_obj = $(element).parent().parent();
                if ($(element).prop("checked")) {
                    parent_obj.find('.tax input').val(tax)
                    var price = parseFloat(parent_obj.find('.price input').val());
                    var tax_price = (price * (1 + tax / 100)).toFixed(2);
                    var number = parent_obj.find('.number').val();
                    parent_obj.find('.tax_price input').val(tax_price);
                    parent_obj.find('.all_price').text(parseFloat(price * number).toFixed(2));
                    parent_obj.find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
                    stat();
                }
            });
        });
        //输入税率
        $(".tax input").bind('input propertychange', function (e) {
            var tax = parseFloat($(this).val()).toFixed(2);
            console.log(tax);
            if (tax < 0 || tax == 'NaN') {
                $(this).val('0');
            }
            var price = parseFloat($(this).parent().parent().find('.price input').val());
            var tax_price = (price * (1 + tax / 100)).toFixed(2);
            var number = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.tax_price input').val(tax_price);
            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            stat();
        });

        //输入数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            var a = number.replace(/[^\d]/g, '');
            if (a == '') a = 0;
            $(this).val(a);

            var agreement_number = $(this).parent().parent().find('.agreement_number').text();
            var price = parseFloat($(this).parent().parent().find('.price input').val());
            var tax_price = parseFloat($(this).parent().parent().find('.tax_price input').val());

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            var use_number = agreement_number - number;
            if (use_number < 0) {
                use_number = 0;
            }
            console.log(parseInt(use_number));
            // 合同需求数量

            var stock_number = parseInt($(this).parent().parent().find('.stock_number').text());
            // 如果输入的数量大于库存
            if (stock_number < parseInt(use_number)) {
                layer.msg('库存不足', {time: 2000});
                $(this).val(parseInt(agreement_number));
                use_number = 0;
            }

            $(this).parent().parent().find('.use_number').text(parseInt(use_number));
            stat();
        });

        // 修改供应商
        $('#orderpurchase-supplier_id').change(function (e) {
            var supplier_id = $(this).val();
            $.ajax({
                type: "get",
                url: '?r=supplier/detail',
                data: {id: supplier_id},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        var payment_sn = $('#orderpurchase-payment_sn').val();
                        payment_sn = payment_sn.split('_');
                        var first = payment_sn[0];
                        var end = payment_sn[2];
                        var after_payment_sn = first + '_' + res.data.short_name + '_' + end;
                        var pay_type = $('#orderpurchase-pay_type').val();
                        if (pay_type == 2) {
                            after_payment_sn += '_杂项';
                        }
                        $('#orderpurchase-payment_sn').val(after_payment_sn);
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        });

        // 修改付款流程
        $('#orderpurchase-pay_type').change(function (e) {
            exit_pay();
        });

        //保存支出合同
        $(".payment_save").click(function () {
            //防止双击
            // $(".payment_save").attr("disabled", true).addClass("disabled");
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time: 2000});
                $(".payment_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var goods_info = [];
            var supplier_name = '';
            var long_delivery_time = 0;
            var supplier_flag = false;
            var stock_flag = false;
            var is_contract = -1;
            var temp_tax = -1;
            var result = false;
            $('.select_id').each(function (index, element) {
                if ($(element).prop("checked")) {
                    // 判断是不是混合勾选
                    var contract = $(element).parent().parent().find('.contract input').prop('checked');
                    contract = contract ? 1 : 0;
                    if (is_contract == -1) {
                        is_contract = contract;
                    } else {
                        if (is_contract != contract) {
                            layer.msg('杂项零件不生成支出合同不可混合', {time: 2000});
                            $(".payment_save").removeAttr("disabled").removeClass("disabled");
                            result = true;
                            return false;
                        }
                    }
                    // 判断是不是税率不一样

                    var tax = parseInt($(element).parent().parent().find('.tax input').val());
                    if (temp_tax == -1) {
                        temp_tax = tax;
                    } else {
                        if (temp_tax != tax) {
                            layer.msg('税率必须统一', {time: 2000});
                            $(".payment_save").removeAttr("disabled").removeClass("disabled");
                            result = true;
                            return false;
                        }
                    }
                    var s_name = $(element).parent().parent().find('.supplier_name').text();
                    if (!supplier_name) {
                        supplier_name = s_name;
                    } else {
                        if (supplier_name != s_name) {
                            supplier_flag = true;
                        }
                    }
                    var item = {};
                    item.purchase_goods_id = $(element).parent().parent().find('.purchase_detail').data('purchase_goods_id');
                    item.goods_id = $(element).val();
                    item.fix_price = parseFloat($(element).parent().parent().find('.price input').val());
                    item.fix_tax_price = parseFloat($(element).parent().parent().find('.tax_price input').val());
                    item.fix_number = $(element).parent().parent().find('.afterNumber input').val();
                    var delivery_time = parseFloat($(element).parent().parent().find('.delivery_time input').val());
                    if (delivery_time > long_delivery_time) {
                        long_delivery_time = delivery_time;
                    }
                    item.delivery_time = delivery_time;
                    goods_info.push(item);

                    var use_num = parseFloat($(element).parent().parent().find('.use_number').text());
                    var stock_num = parseFloat($(element).parent().parent().find('.stock_number').text());
                    if (use_num > stock_num) {
                        stock_flag = true;
                    }
                }
            });
            if (result) {
                return false;
            }
            // 数量为0不能和有数量的同时生成合同
            var status = 2;
            for (var i in goods_info) {
                var temp_status = goods_info[i].fix_number == 0 ? 0 : 1;
                if (status == 2) {
                    status = temp_status;
                } else if (temp_status != status) {
                    layer.msg('数量为0不能和有数量的同时生成合同', {time: 2000});
                    $(".payment_save").removeAttr("disabled").removeClass("disabled");
                    return false;
                }
            }
            // if (supplier_flag) {
            //     layer.msg('一个支出合同不能有多个供应商', {time:2000});
            //     return false;
            // }

            // if (stock_flag) {
            //     layer.msg('使用库存数量不能大于临时库存数量', {time: 2000});
            //     $(".payment_save").removeAttr("disabled").removeClass("disabled");
            //     return false;
            // }

            var order_purchase_id = $('.data').data('order_purchase_id');
            var admin_id = $('#orderpurchase-admin_id').val();
            var end_date = $('#orderpurchase-end_date').val();
            var payment_sn = $('#orderpurchase-payment_sn').val();
            var supplier_id = $('#orderpurchase-supplier_id option:selected').val();
            if (!supplier_id) {
                layer.msg('请选择供应商', {time: 2000});
                $(".payment_save").removeAttr("disabled").removeClass("disabled");
                return false;
            }
            var apply_reason = $('#orderpurchase-apply_reason').val();
            var agreement_date = $('#orderpurchase-agreement_date').val();
            //合同交货日期
            var delivery_date = $('#orderpurchase-delivery_date').val();
            //收入合同交货日期
            var order_agreement_date = '<?=$order_agreement_at?>';
            var payment_ratio = $('#orderpurchase-payment_ratio').val();
            var pay_type = $('#orderpurchase-pay_type option:selected').text();
            var stat_all_tax_price = $('.stat_all_tax_price').text()
            var supplier_name = $('#orderpurchase-supplier_id option:selected').text();
            // console.log(pay_type,supplier_name,temp_tax,payment_ratio,agreement_date,delivery_date,stat_all_tax_price);return false;
            layer.confirm('<table class="table table-bordered table-hover" style="width: 260px;">\n'+
                '            <tr><td>付款流程</td><td>\n'+
                pay_type
                + '            </td></tr><tr><td>供应商</td><td>\n'+
                supplier_name
                + '            </td></tr><tr><td>税率</td><td>\n'+
                temp_tax
                + '            </td></tr><tr><td>预付款比例%</td><td>\n'+
                payment_ratio
                + '            </td></tr><tr><td>合同签订时间</td><td>\n'+
                agreement_date
                + '            </td></tr><tr><td>交货时间</td><td>\n'+
                delivery_date
                + '            </td></tr><tr><td>供应商</td><td>\n'+
                stat_all_tax_price
                + '            </td></tr></table>', {
                title: ['确认信息'],
                area: ['300', '70%'],
                btn: ['确定','取消'] //按钮
            }, function(){
                if ((new Date(delivery_date.replace('/-/g', '\/'))) > (new Date(order_agreement_date.replace('/-/g', '\/')))) {
                    layer.confirm('支出交货时间晚于收入', {
                        btn: ['重新选择', '确认'] //按钮
                    }, function (index) {
                        layer.close(index);
                        $(".payment_save").removeAttr("disabled").removeClass("disabled");
                        return false;
                    }, function (index) {
                        //创建支出合同
                        $.ajax({
                            type: "post",
                            url: '?r=order-purchase-verify/save-order',
                            data: {
                                order_purchase_id: order_purchase_id,
                                admin_id: admin_id,
                                is_contract: is_contract,
                                end_date: end_date,
                                payment_sn: payment_sn,
                                goods_info: goods_info,
                                long_delivery_time: long_delivery_time,
                                supplier_id: supplier_id,
                                apply_reason: apply_reason,
                                agreement_date: agreement_date,
                                delivery_date: delivery_date,
                                order_agreement_date: order_agreement_date,
                                payment_ratio: payment_ratio
                            },
                            dataType: 'JSON',
                            success: function (res) {
                                $(".payment_save").removeAttr("disabled").removeClass("disabled");
                                if (res && res.code == 200) {
                                    layer.msg(res.msg, {time: 2000});
                                    window.location.reload();
                                } else {
                                    layer.msg(res.msg, {time: 2000});
                                    return false;
                                }
                            }
                        });
                    });
                } else {
                    //创建支出合同
                    $.ajax({
                        type: "post",
                        url: '?r=order-purchase-verify/save-order',
                        data: {
                            order_purchase_id: order_purchase_id,
                            admin_id: admin_id,
                            end_date: end_date,
                            payment_sn: payment_sn,
                            is_contract: is_contract,
                            goods_info: goods_info,
                            long_delivery_time: long_delivery_time,
                            supplier_id: supplier_id,
                            apply_reason: apply_reason,
                            agreement_date: agreement_date,
                            delivery_date: delivery_date,
                            order_agreement_date: order_agreement_date,
                            payment_ratio: payment_ratio
                        },
                        dataType: 'JSON',
                        success: function (res) {
                            $(".payment_save").attr("disabled", false);
                            if (res && res.code == 200) {
                                layer.msg(res.msg, {time: 2000});
                                window.location.reload();
                            } else {
                                layer.msg(res.msg, {time: 2000});
                                return false;
                            }
                        }
                    });
                }
            }, function(){
                layer.msg('取消');
            });


            return false;
            if ((new Date(delivery_date.replace('/-/g', '\/'))) > (new Date(order_agreement_date.replace('/-/g', '\/')))) {
                layer.confirm('支出交货时间晚于收入', {
                    btn: ['重新选择', '确认'] //按钮
                }, function (index) {
                    layer.close(index);
                    $(".payment_save").removeAttr("disabled").removeClass("disabled");
                    return false;
                }, function (index) {
                    //创建支出合同
                    $.ajax({
                        type: "post",
                        url: '?r=order-purchase-verify/save-order',
                        data: {
                            order_purchase_id: order_purchase_id,
                            admin_id: admin_id,
                            is_contract: is_contract,
                            end_date: end_date,
                            payment_sn: payment_sn,
                            goods_info: goods_info,
                            long_delivery_time: long_delivery_time,
                            supplier_id: supplier_id,
                            apply_reason: apply_reason,
                            agreement_date: agreement_date,
                            delivery_date: delivery_date,
                            order_agreement_date: order_agreement_date,
                            payment_ratio: payment_ratio
                        },
                        dataType: 'JSON',
                        success: function (res) {
                            $(".payment_save").removeAttr("disabled").removeClass("disabled");
                            if (res && res.code == 200) {
                                layer.msg(res.msg, {time: 2000});
                                window.location.reload();
                            } else {
                                layer.msg(res.msg, {time: 2000});
                                return false;
                            }
                        }
                    });
                });
            } else {
                //创建支出合同
                $.ajax({
                    type: "post",
                    url: '?r=order-purchase-verify/save-order',
                    data: {
                        order_purchase_id: order_purchase_id,
                        admin_id: admin_id,
                        end_date: end_date,
                        payment_sn: payment_sn,
                        is_contract: is_contract,
                        goods_info: goods_info,
                        long_delivery_time: long_delivery_time,
                        supplier_id: supplier_id,
                        apply_reason: apply_reason,
                        agreement_date: agreement_date,
                        delivery_date: delivery_date,
                        order_agreement_date: order_agreement_date,
                        payment_ratio: payment_ratio
                    },
                    dataType: 'JSON',
                    success: function (res) {
                        $(".payment_save").attr("disabled", false);
                        if (res && res.code == 200) {
                            layer.msg(res.msg, {time: 2000});
                            window.location.reload();
                        } else {
                            layer.msg(res.msg, {time: 2000});
                            return false;
                        }
                    }
                });
            }
        });

        //进行输入序号筛选
        $('.select_ack').click(function (e) {
            var input_val = $('.select_serial').val();
            if (input_val == '') {
                return;
            }
            var stat_all_tax_price = 0;
            var input_arr = input_val.split('|');
            $('.order_purchase_list').each(function (i, e) {
                var serial = String($(e).find('.purchase_detail').data('serial'));
                var index = input_arr.indexOf(serial);
                if (index != -1) {
                    $(e).find('.select_id').prop("checked", true);
                }

                if ($(e).find('.select_id').prop("checked")) {
                    var all_tax_price = parseFloat($(e).find('.all_tax_price').text());
                    if (all_tax_price) {
                        stat_all_tax_price += all_tax_price;
                    }
                }
            });

            //统一处理总选择
            if ($('.select_id').length == $('.select_id:checked').length) {
                $('.select_all').prop("checked", true);
            } else {
                $('.select_all').prop("checked", false);
            }

            //计算统计
            $('.stat_all_tax_price').text(stat_all_tax_price.toFixed(2));
        });

        function stat() {
            var stat_all_tax_price = 0;
            $('.order_purchase_list').each(function (i, e) {
                if ($(e).find('.select_id').prop("checked")) {
                    var all_tax_price = parseFloat($(e).find('.all_tax_price').text());
                    if (all_tax_price) {
                        stat_all_tax_price += all_tax_price;
                    }
                }
            });
            //计算统计
            $('.stat_all_tax_price').text(stat_all_tax_price.toFixed(2));
        }
    });
</script>
