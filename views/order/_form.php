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
}


?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">
        <?= $form->field($model, 'order_type')->radioList(Order::getType(), ['class' => 'radio']) ?>

        <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'customer_id')->dropDownList(Customer::getAllDropDown())->label('客户名称') ?>

        <?= $form->field($model, 'customer_short_name')->textInput(['readonly' => true])->label('客户缩写') ?>

    <?= $form->field($model, 'manage_name')->textInput(['maxlength' => true]) ?>

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
           
       });
       (function(e){
           e.preventDefault();
           var form = $(this).serializeArray();
           console.log(form);
           $(".created").removeAttr("disabled").removeClass("disabled");
           return ;
           var parameter = '';
           $.each(form, function() {
               parameter += this.name + '=' + this.value + '&';
           });

           var type = $('.on').data('type');
           parameter += 'type=' + type;

           $.ajax({
               type:"GET",
               url:"?r=paper/index",
               data:{},
               dataType:'JSON',
               success:function(res){
                   // console.log(location.href.split("?")[0] + "?r=paper/index&" + parameter);
                   location.replace(location.href.split("?")[0] + "?r=paper/index&" + parameter);
               }
           })
       });

    });
</script>
