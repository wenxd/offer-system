<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Order;
use app\models\Customer;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {
    $model->created_at = date('Y-m-d H:i:s');
    $model->order_type = 1;
    $model->order_sn = 'D' . date('Ymd_');
    $model->manage_name = Yii::$app->user->identity->username;
}


?>

<div class="box">
    <div class="box-header">
        <?= Html::a('新增零件', Url::to(['goods/create']), [
                'class' => 'btn btn-success',
                'name'  => 'submit-button',
                'target' => 'blank']
        )?>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <?= $form->field($model, 'order_type')->radioList(Order::$orderType, ['class' => 'radio']) ?>

        <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'customer_id')->dropDownList(Customer::getAllDropDown())->label('客户名称') ?>

        <?= $form->field($model, 'customer_short_name')->textInput(['readonly' => true])->label('客户缩写') ?>

    <?= $form->field($model, 'manage_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'provide_date')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd 12:00:00',
            'startView' =>2,  //其实范围（0：日  1：天 2：年）
            'maxView'   =>2,  //最大选择范围（年）
            'minView'   =>2,  //最小选择范围（年）
        ]
    ]);?>

    <?= $form->field($model, 'created_at')->textInput(['readonly' => true]) ?>

    </div>

    <div class="box-footer">
        <?= Html::Button($model->isNewRecord ? '创建' :  '更新', [
                'class' => 'btn btn-success created',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#order-customer_id').change(function () {
            var id = $(this).val();
            $.ajax({
                type:"get",
                url:"?r=customer/info",
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200) {
                        $('#order-customer_short_name').val(res.data.short_name);
                    } else {
                        $('#order-customer_short_name').val('');
                    }
                }
            });
        });

        $('.created').on('click', function () {
            var parameter = '';

            var order_type = $('#order-order_type').find('input:checked').val();
            parameter += 'order_type=' + order_type + '&';
            var order_sn = $('#order-order_sn').val();
            if (order_sn === ''){
                layer.msg('请输入订单编号', {time:2000});
                return false;
            }
            parameter += 'order_sn=' + order_sn + '&';
            var customer_id = $('#order-customer_id').val();
            if (customer_id == 0){
                layer.msg('请选择客户名称', {time:2000});
                return false;
            }
            parameter += 'customer_id=' + customer_id + '&';
            var manage_name = $('#order-manage_name').val();
            if (manage_name === ''){
                layer.msg('请输入订单管理员名称', {time:2000});
                return false;
            }
            parameter += 'manage_name=' + manage_name + '&';
            var provide_date = $('#order-provide_date').val();
            if (provide_date === ''){
                layer.msg('请输入报价截止日期', {time:2000});
                return false;
            }
            var created_at = $('#order-created_at').val();
            parameter += 'provide_date=' + provide_date + '&' + 'created_at=' + created_at;
            location.replace("?r=order/generate&" + encodeURI(parameter));
        });
        var date = $('#order-order_sn').val();
        $('input:radio').change(function (e) {
            if ($("input:radio:checked").val() == 1) {
                $('#order-order_sn').val(date);
            } else {
                $('#order-order_sn').val('F' + date.substring(1));
            }
        });
    });
</script>
