<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Supplier;
use app\models\Inquiry;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;
/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */
/* @var $form yii\widgets\ActiveForm */

$model->tax_rate='16';
if ($model->isNewRecord) {
    if (isset($_GET['goods_id']) && $_GET['goods_id']) {
        $model->good_id = $_GET['goods_id'];
    }
    $model->inquiry_datetime = date('Y-m-d H:i:s');
}

//$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
//$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
//$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
//foreach ($adminList as $key => $admin) {
//    $admins[$admin->id] = $admin->username;
//}
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">

    <?= $form->field($model, 'good_id')
        ->dropDownList($model->isNewRecord ? Goods::getCreateDropDown() : Goods::getAllDropDown())
        ->label('零件号') ?>

    <?= $form->field($model, 'supplier_id')->dropDownList(Supplier::getCreateDropDown())
        ->label('供应商名称') ?>

    <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true])
//        ->hint('只输入数字，例如10%，只输入10')
    ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inquiry_datetime')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd hh:ii:00'
        ]
    ]);?>

    <?= $form->field($model, 'delivery_time')->textInput(['maxlength' => true])->label('货期(天)');?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_better')->dropDownList(Inquiry::$better) ?>

    <?= $form->field($model, 'better_reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择询价员') ?>

    <?= $form->field($model, 'order_id')->textInput(['readonly' => true])->label('订单号') ?>

    <?= $form->field($model, 'order_inquiry_id')->textInput(['readonly' => true])->label('询价单号') ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
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
    //实现税率自动转换
    var tax = $('#inquiry-tax_rate').val();

    $('#inquiry-price').blur(function () {
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        $("#inquiry-tax_price").attr("value",tax_price.toFixed(2));
        $("#inquiry-tax_price").val(tax_price.toFixed(2));
    });

    $('#inquiry-tax_price').blur(function () {
        var tax_price = $('#inquiry-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#inquiry-price").attr("value",price.toFixed(2));
        $("#inquiry-price").val(price.toFixed(2));
    });

    var goods_id = $('#inquiry-good_id').val();
    getGoodsInfo(goods_id);
    function getGoodsInfo(goods_id) {
        $.ajax({
            type:"get",
            url:'?r=goods/get-info',
            data:{goods_id:goods_id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    console.log(res.data);
                    $('#inquiry-better_reason').val('原厂家');
                    $('#inquiry-order_id').val(res.data.orderGoods.order_id);
                    $('#inquiry-order_inquiry_id').val(res.data.orderInquiry.id);
                }
            }
        });
    }
    $('#inquiry-good_id').change(function (e) {
        goods_id = $(this).val();
        getGoodsInfo(goods_id);
    });
</script>
