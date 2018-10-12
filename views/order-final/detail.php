<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成采购单';
$this->params['breadcrumbs'][] = $this->title;

//同一个订单询价商品的IDs
$inquiryGoods_ids = ArrayHelper::getColumn($inquiryGoods, 'goods_id');


?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                <th>零件号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>技术备注</th>
                <th>是否加工</th>
                <th>是否特制</th>
                <th>是否铭牌</th>
                <th>图片</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未率单价</th>
                <th>含率单价</th>
                <th>货期(天)</th>
                <th>询价状态</th>
                <th>未率总价</th>
                <th>含率总价</th>
                <th>是否有采购单</th>
                <th>采购单号</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finalGoods as $item):?>
            <tr>
                <td><input type="checkbox" name="select_id" class="select_id"></td>
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
                <td><?=isset($inquiryGoods[$item->goods_id]) ? ($inquiryGoods[$item->goods_id]->is_inquiry ? '已询价' : '未询价') : '未询价'?></td>
                <td class="all_price"></td>
                <td class="all_tax_price"></td>
                <td></td>
                <td></td>
                <td><input type="number" size="4" class="number"></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <?= Html::button('保存采购单', [
                'class' => 'btn btn-success purchase_save',
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

        $(".number").bind('input propertychange', function (e) {

            var number = $(this).val();
            var a = number.replace(/[^\d]/g,'');
            $(this).val(a);

            var price = $(this).parent().parent().find('.price').text();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

            $(this).parent().parent().find('.all_price').text(price * number);
            $(this).parent().parent().find('.all_tax_price').text(tax_price * number);
        });
    });
</script>