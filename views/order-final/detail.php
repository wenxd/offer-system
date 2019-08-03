<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成报价单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');

$use_admin = AuthAssignment::find()->where(['item_name' => '报价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

$model->quote_sn = 'B' . date('ymd__') . $number;

?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_final_id="<?=$_GET['id']?>">
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
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
                <th>询价状态</th>
                <th>是否有报价单</th>
                <th>报价单号</th>
                <th>订单需求数量</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finalGoods as $item):?>
            <tr class="order_final_list">
                <td><?=isset($purchaseGoods[$item->goods_id]) ? '' : "<input type='checkbox' name='select_id' 
data-type={$item->type} data-relevance_id={$item->relevance_id}  value={$item->goods_id} class='select_id'>"?></td>
                <td><?=Html::a($item->goods->goods_number, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number]))?></td>
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
                <td class="delivery_time"><?=$item->type ? '' : $item->inquiry->delivery_time?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
                <td><?=isset($inquiryGoods[$item->goods_id]) ? ($inquiryGoods[$item->goods_id]->is_inquiry ? '已询价' : '未询价') : '未询价'?></td>
                <td><?=isset($purchaseGoods[$item->goods_id]) ? '是' : '否'?></td>
                <td><?=isset($purchaseGoods[$item->goods_id]) ? $purchaseGoods[$item->goods_id]->order_purchase_sn : ''?></td>
                <td><?=$orderGoods[$item->goods_id]->number?></td>
                <td class="afterNumber"><?=isset($purchaseGoods[$item->goods_id]) ? $purchaseGoods[$item->goods_id]->number :
                        '<input type="number" size="4" class="number" min="1" 
    onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'\')}else{this.value=this.value.replace(/\D/g,\'\')}"
    onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'0\')}else{this.value=this.value.replace(/\D/g,\'\')}">'?></td>
            </tr>
            <?php endforeach;?>
            <tr style="background-color: #acccb9">
                <td colspan="16" rowspan="2">汇总统计</td>
                <td>最长货期</td>
                <td>未税总价</td>
                <td>含税总价</td>
                <td colspan="5" rowspan="2"></td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="mostLongTime"></td>
                <td class="sta_all_price"></td>
                <td class="sta_all_tax_price"></td>
            </tr>
            </tbody>
        </table>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择报价员') ?>

        <?= $form->field($model, 'quote_sn')->textInput() ?>

        <?= $form->field($model, 'quote_ratio')->textInput() ?>


    </div>
    <div class="box-footer">
        <?= Html::button('保存报价单', [
                'class' => 'btn btn-success quote_save',
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
        function init(){
            if (!$('.select_id').length) {
                $('.select_all').hide();
                $('.quote_save').hide();
                $('.field-orderpurchase-admin_id').hide();
                $('.field-orderpurchase-end_date').hide();
            }
            var mostLongTime        = 0;
            $('.order_final_list').each(function (i, e) {
                var delivery_time   = $(e).find('.delivery_time').text();
                if (delivery_time > mostLongTime) {
                    mostLongTime = delivery_time;
                }
                if (!$(e).find('.afterNumber').find('.number').length) {
                    var price           = $(e).find('.price').text();
                    var tax_price       = $(e).find('.tax_price').text();
                    var number          = $(e).find('.afterNumber').text();
                    $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                    $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
                }
            });
            $('.mostLongTime').text(mostLongTime);
        }

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

        //输入数量
        $(".number").bind('input propertychange', function (e) {

            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time:2000});
                return false;
            }
            var a = number.replace(/[\D]/g,'');
            $(this).val(a);

            var price = $(this).parent().parent().find('.price').text();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));

            var sta_all_price       = 0;
            var sta_all_tax_price   = 0;
            $('.order_final_list').each(function (i, e) {
                var all_price       = $(e).find('.all_price').text();
                var all_tax_price   = $(e).find('.all_tax_price').text();

                if (all_price) {
                    sta_all_price      += parseFloat(all_price);
                }
                if (all_tax_price) {
                    sta_all_tax_price  += parseFloat(all_tax_price);
                }
            });
            $('.sta_all_price').text(sta_all_price.toFixed(2));
            $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        });

        //保存
        $('.quote_save').click(function (e) {
            var select_length = $('.select_id:checked').length;
            if (!select_length) {
                layer.msg('请最少选择一个零件', {time:2000});
                return false;
            }

            var goods_info = [];
            var number_flag = false;
            var number = 1;
            $('.select_id').each(function (index, element) {
                var item = {};
                if ($(element).prop("checked")) {
                    item.goods_id    = $(element).val();
                    if (!$(element).parent().parent().find('.number').val()){
                        number_flag  = true;
                    }
                    item.number        = $(element).parent().parent().find('.number').val();
                    item.type          = $(element).data('type');
                    item.relevance_id  = $(element).data('relevance_id');
                    goods_info.push(item);
                }
            });

            if (number_flag) {
                layer.msg('请给选中的行输入数量', {time:2000});
                return false;
            }
            var admin_id = $('#orderquote-admin_id').val();
            if (!admin_id) {
                layer.msg('请选择采购员', {time:2000});
                return false;
            }
            var quote_sn = $('#orderquote-quote_sn').val();
            if (!quote_sn) {
                layer.msg('请填写报价单号', {time:2000});
                return false;
            }
            // var end_date = $('#orderpurchase-end_date').val();
            // if (!end_date) {
            //     layer.msg('请输入采购截止时间', {time:2000});
            //     return false;
            // }

            var order_final_id = $('.data').data('order_final_id');

            $.ajax({
                type:"post",
                url:'?r=order-quote/save-order',
                data:{order_final_id:order_final_id, admin_id:admin_id, quote_sn:quote_sn, goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-quote/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });


    });
</script>
