<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '生成收入合同';
$this->params['breadcrumbs'][] = $this->title;

$model->agreement_date = date('Y-m-d');
$model->sign_date = date('Y-m-d');
$customer_name = $order->customer ? $order->customer->short_name : '';
$model->agreement_sn = 'S' . date('ymd_') . $customer_name . '_' . $number;

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
            <thead class="data" data-order_quote_id="<?=$_GET['id']?>">
            <tr>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>零件号</th>
                <?php endif;?>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>报价货期</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含税单价</th>
                <th>含税总价</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quoteGoods as $item):?>
                <tr class="order_quote_list">
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td class="goods_id" data-goods_id="<?=$item->goods_id?>" data-goods_type="<?=$item->type?>"
                        data-relevance_id="<?=$item->relevance_id?>" data-quote_goods_id="<?=$item->id?>">
                        <?=$item->goods->description?>
                    </td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->unit?></td>
                    <td class="delivery_time"><input type="text" value="<?=$item->quote_delivery_time?>"></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td class="price"><input type="text" class="change_price" value="<?=$item->quote_price?>"></td>
                    <td class="tax_price"><?=$item->quote_tax_price?></td>
                    <td class="all_tax_price"></td>
                    <td class="afterNumber">
                        <input type="number" size="4" class="number" min="1" value="<?=$item->number?>"
                               onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'\')}else{this.value=this.value.replace(/\D/g,\'\')}"
                               onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,\'0\')}else{this.value=this.value.replace(/\D/g,\'\')}"/>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'agreement_sn')->textInput() ?>

        <?= $form->field($model, 'sign_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ]);?>

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

    </div>
    <?php if ($orderQuote->is_quote):?>
    <div class="box-footer">
        <?= Html::button('生成收入合同', [
                'class' => 'btn btn-success quote_complete',
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
            $('.order_quote_list').each(function (i, e) {
                var price     = $(e).find('.change_price').val();
                var tax_price = $(e).find('.tax_price').text();
                var number    = $(e).find('.afterNumber').find('input').val();
                $(e).find('.all_price').text(parseFloat(price * number).toFixed(2));
                $(e).find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            });
            var date = '<?=$model->agreement_date?>';
            $('#orderpurchase-agreement_date').val(date);
        }

        //改变数量
        $(".number").bind('input propertychange', function (e) {
            var number = $(this).val();
            if (number == 0) {
                layer.msg('数量最少为1', {time:2000});
                return false;
            }
            var a = number.replace(/[\D]/g,'');
            $(this).val(a);

            var price = $(this).parent().parent().find('.change_price').val();
            var tax_price = $(this).parent().parent().find('.tax_price').text();

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
        });

        //修改单价
        $('.change_price').bind('input propertychange', function (e) {
            var price = $(this).val();
            var tax   = $(this).parent().parent().find('.tax').text();
            var tax_price = (price * (1 + tax / 100));
            var number    = $(this).parent().parent().find('.number').val();
            $(this).parent().parent().find('.tax_price').text(parseFloat(tax_price).toFixed(2));

            $(this).parent().parent().find('.all_price').text(parseFloat(price * number).toFixed(2));
            $(this).parent().parent().find('.all_tax_price').text(parseFloat(tax_price * number).toFixed(2));
            console.log(price);
        });

        $('.quote_complete').click(function (e) {
            var goods_info = [];

            $('.order_quote_list').each(function (i, ele) {
                var item = {};
                item.quote_goods_id  = $(ele).find('.goods_id').data('quote_goods_id');
                item.goods_id        = $(ele).find('.goods_id').data('goods_id');
                item.type            = $(ele).find('.goods_id').data('goods_type');
                item.relevance_id    = $(ele).find('.goods_id').data('relevance_id');
                item.number          = $(ele).find('.number').val();
                item.price           = $(ele).find('.change_price').val();
                item.tax_price       = $(ele).find('.tax_price').text();
                item.delivery_time   = $(ele).find('.delivery_time input').val();
                goods_info.push(item);

            });

            var agreement_sn = $('#orderagreement-agreement_sn').val();
            if (!agreement_sn) {
                layer.msg('请输入合同号', {time:2000});
                return false;
            }

            var sign_date = $('#orderagreement-sign_date').val();
            if (!sign_date) {
                layer.msg('请输入合同签订日期', {time:2000});
                return false;
            }

            var agreement_date = $('#orderagreement-agreement_date').val();
            if (!agreement_date) {
                layer.msg('请输入合同交货日期', {time:2000});
                return false;
            }

            // var admin_id = $('#orderagreement-admin_id').val();
            // if (!admin_id) {
            //     layer.msg('请选择询价员', {time:2000});
            //     return false;
            // }

            var id = $('.data').data('order_quote_id');
            $.ajax({
                type:"post",
                url:'?r=order-quote/create-agreement',
                data:{id:id, agreement_sn:agreement_sn, sign_date:sign_date, agreement_date:agreement_date, goods_info:goods_info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-agreement/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
