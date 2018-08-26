<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Inquiry;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

$this->title = '预生成报价单、询价单列表';
$this->params['breadcrumbs'][] = $this->title;

if (!$model->id) {
    $model->provide_date = date('Y-m-d H:i:00');
}
?>
<style>
    .but button{
        float: right;
    }
    .but a{
        float: right;
        margin-right: 10px;
    }
    .offer {
        margin-top: 10px;
    }
</style>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>零件号</th>
                        <th>是否最新</th>
                        <th>是否优选</th>
                        <th>商品类型</th>
                        <th>价格</th>
                        <th>库存数量</th>
                        <th>询价时间</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>购买数量</th>
                        <th>金额</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="11" style="text-align: center;">最新询价记录</td>
                </tr>
                <?php foreach ($inquiryNewest as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['inquiry_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['inquiry_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td>
                            <a class="btn btn-danger btn-xs btn-flat delete" href="javascript:void(0);" data-cart-id="<?=$value['cart_id']?>" ><i class="fa fa-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="11" style="text-align: center;">优选记录</td>
                </tr>
                <?php foreach ($inquiryBetter as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['inquiry_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['inquiry_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td>
                            <a class="btn btn-danger btn-xs btn-flat delete" href="javascript:void(0);" data-cart-id="<?=$value['cart_id']?>" ><i class="fa fa-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="11" style="text-align: center;">本地库存零件记录</td>
                </tr>
                <?php foreach ($stockList as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td>无</td>
                        <td>无</td>
                        <td>库存商品</td>
                        <td><?=$value['price']?></td>
                        <td><?=$value['number']?></td>
                        <td>无</td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td>
                            <a class="btn btn-danger btn-xs btn-flat delete" href="javascript:void(0);" data-cart-id="<?=$value['cart_id']?>" ><i class="fa fa-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="10" style="text-align: right;"><b>金额合计</b></td>
                    <td class="all_money"></td>
                    <td></td>
                </tr>
                </tbody>
            </table>

            <?= $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'provide_date')->widget(DateTimePicker::className(), [
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd hh:ii:00',
                    'startView' =>2,  //其实范围（0：日  1：天 2：年）
                    'maxView'   =>2,  //最大选择范围（年）
                    'minView'   =>2,  //最小选择范围（年）
                ]
            ]);?>

            <?= $form->field($model, 'quote_price')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('保存报价单', [
                    'class' => 'btn btn-info quote_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::submitButton('保存询价单', [
                    'class' => 'btn btn-success inquiry_save',
                    'name'  => 'submit-button']
            )?>
            <?= Html::a('<i class="fa fa-reply"></i> 继续添加', Url::to(['search/index']), [
                'class' => 'btn btn-default btn-flat',
            ])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    window.onload = function() {
        var allMoney = 0;
        $('.money').each(function (index, element){
            allMoney += parseFloat(element.innerText)
        });
        $('.all_money').html(allMoney);

        function submit(type) {
            $('form').submit(function(e){
                e.preventDefault();
                var form = $(this).serializeArray();

                var parameter = '';
                var is_go = false;
                console.log(form);
                $.each(form, function() {
                    if (!this.value) {
                        is_go = true;
                    }
                    parameter += this.name + '=' + this.value + '&';
                });
                parameter += 'type=' + type;
                if (is_go) {
                    return false;
                }
                $.ajax({
                    type:"get",
                    url:"?r=order-inquiry/submit&" + parameter,
                    data:{},
                    dataType:'JSON',
                    success:function(res){
                        if (res && res.code == 200) {
                            layer.msg(res.msg, {time:1500}, function(){
                                if (type == 1) {
                                    location.replace("?r=order-inquiry/index");
                                } else {
                                    location.replace("?r=order-quote/index");
                                }
                            });
                        }
                    }
                })
            });
        }

        $('.inquiry_save').click(function () {
            submit(1);
        });

        $('.quote_save').click(function () {
            submit(2);
        });

        $('.delete').click(function () {
            var cart_id = $(this).data('cart-id');
            $.ajax({
                type:"get",
                url:"?r=cart/delete",
                data:{id:cart_id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time:1500}, function(){
                            location.reload();
                        });
                    }

                }
            })
        });

    }
</script>