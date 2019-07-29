<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Supplier;
use app\models\{Inquiry, Goods, Admin, AuthAssignment, SystemConfig};
/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */
/* @var $form yii\widgets\ActiveForm */


$model->tax_rate = SystemConfig::find()->select('value')->where([
        'title'  => SystemConfig::TITLE_TAX,
    'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
if ($model->isNewRecord) {
    if (isset($_GET['goods_id']) && $_GET['goods_id']) {
        $model->good_id = $_GET['goods_id'];
        $goods = Goods::findOne($_GET['goods_id']);
        $model->goods_number_b = $goods->goods_number_b;
    }
    $model->inquiry_datetime = date('Y-m-d');
    $model->delivery_time    = SystemConfig::find()->select('value')->where([
        'title'  => SystemConfig::TITLE_DELIVERY_TIME,
        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
} else {
    $model->supplier_name    = $model->supplier->name;
    $model->goods_number     = $model->goods->goods_number;
    $model->goods_number_b   = $model->goods->goods_number_b;
    $model->inquiry_datetime = substr($model->inquiry_datetime, 0, 10);
}
//超级管理员
$user_super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
$superAdminIds  = ArrayHelper::getColumn($user_super, 'user_id');
//判断当前用户是否是超管
$is_super = false;
if (in_array(Yii::$app->user->identity->id, $superAdminIds)) {
    $is_super = true;
}

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
?>
<style>
    /*供应商*/
    .box-search li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-ul {
        margin-left: -40px;
    }
    .box-search {
        width: 200px;
        margin-top: -10px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search li:hover {
        background-color: #84b5bc;
    }
    .cancel {
        display: none;
    }

    /*零件号A*/
    .box-search-goods_number li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-ul-goods_number {
        margin-left: -40px;
    }
    .box-search-goods_number {
        width: 200px;
        margin-top: -10px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search-goods_number li:hover {
        background-color: #84b5bc;
    }
    .cancel-goods_number {
        display: none;
    }

    /*零件号B*/
    .box-search-goods_number_b li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-ul-goods_number_b {
        margin-left: -40px;
    }
    .box-search-goods_number_b {
        width: 200px;
        margin-top: -15px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search-goods_number_b li:hover {
        background-color: #84b5bc;
    }
    .cancel-goods_number_b {
        display: none;
    }
</style>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">
        <?= $form->field($model, 'good_id')->textInput()->hiddenInput()->label(false) ?>
    <?php if (isset($_GET['order_inquiry'])) :?>
        <?= $form->field($model, 'goods_number_b')->textInput(['maxlength' => true])->label('零件号B') ?>
        <div class="box-search-goods_number_b cancel-goods_number_b">
            <ul class="box-search-ul-goods_number_b">

            </ul>
        </div>
    <?php else :?>
        <?php if ($is_super):?>
            <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true])->label('零件号A') ?>
            <div class="box-search-goods_number cancel-goods_number">
                <ul class="box-search-ul-goods_number">

                </ul>
            </div>
        <?php endif;?>
        <?= $form->field($model, 'goods_number_b')->textInput(['maxlength' => true])->label('零件号B') ?>
        <div class="box-search-goods_number_b cancel-goods_number_b">
            <ul class="box-search-ul-goods_number_b">

            </ul>
        </div>
    <?php endif;?>
    <div class="form-group field-inquiry-price">
        <label class="control-label" for="inquiry-supplier_name">供应商</label>
        <input type="text" id="inquiry-supplier_name" class="form-control" name="Inquiry[supplier_name]"
               value="<?=$model->supplier_name?>" autocomplete="off" aria-invalid="false">
        <div class="help-block"></div>
    </div>
    <div class="box-search cancel">
        <ul class="box-search-ul">

        </ul>
    </div>
    <?= $form->field($model, 'tax_rate')->textInput(['readonly' => true])
//        ->hint('只输入数字，例如10%，只输入10')
    ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inquiry_datetime')->widget(DateTimePicker::className(), [
        'removeButton'  => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd',
            'startView' =>2,  //其实范围（0：日  1：天 2：年）
            'maxView'   =>2,  //最大选择范围（年）
            'minView'   =>2,  //最小选择范围（年）
        ]
    ]);?>

    <?= $form->field($model, 'delivery_time')->textInput(['maxlength' => true])->label('货期(天)');?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_better')->dropDownList(Inquiry::$better) ?>

    <?= $form->field($model, 'better_reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择询价员') ?>

    <?= $form->field($model, 'order_id')->textInput(['readonly' => true])->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'order_inquiry_id')->textInput(['readonly' => true])->hiddenInput()->label(false)  ?>

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

    $("#inquiry-price").bind('input propertychange', function (e) {
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        $("#inquiry-tax_price").attr("value",tax_price.toFixed(2));
        $("#inquiry-tax_price").val(tax_price.toFixed(2));
    });
    $("#inquiry-tax_price").bind('input propertychange', function (e) {
        var tax_price = $('#inquiry-tax_price').val();
        var price = tax_price / (1 + tax/100);
        $("#inquiry-price").attr("value",price.toFixed(2));
        $("#inquiry-price").val(price.toFixed(2));
    });

    // $('#inquiry-price').blur(function () {
    //     var price = $('#inquiry-price').val();
    //     var tax_price = price * (1 + tax/100);
    //     $("#inquiry-tax_price").attr("value",tax_price.toFixed(2));
    //     $("#inquiry-tax_price").val(tax_price.toFixed(2));
    // });
    // $('#inquiry-tax_price').blur(function () {
    //     var tax_price = $('#inquiry-tax_price').val();
    //     var price = tax_price / (1 + tax/100);
    //     $("#inquiry-price").attr("value",price.toFixed(2));
    //     $("#inquiry-price").val(price.toFixed(2));
    // });

    init();
    function init() {
        $('#inquiry-order_id').val(0);
        $('#inquiry-order_inquiry_id').val(0);
        var is_inquiry = '<?=$_GET['order_inquiry'] ?? 0?>';
        if (is_inquiry == 1) {
            var goods_id = '<?=$_GET['goods_id'] ?? 0?>';
            getGoodsInfo(goods_id);
        }
    }

    function getGoodsInfo(goods_id) {
        $.ajax({
            type:"get",
            url:'?r=goods/get-info',
            data:{goods_id:goods_id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
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

    //供应商搜索
    $("#inquiry-supplier_name").bind('input propertychange', function (e) {
        var supplier_name = $('#inquiry-supplier_name').val();
        if (supplier_name === '') {
            $('.box-search').addClass('cancel');
            return;
        }
        $('.box-search-ul').html("");
        $('.box-search').removeClass('cancel');
        $.ajax({
            type:"GET",
            url:"?r=search/get-supplier",
            data:{supplier_name:supplier_name},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){

                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="select($(this))">' + res.data[i] + '</li>';
                    }
                    if (li) {console.log(li);
                        $('.box-search-ul').append(li);
                    } else {
                        $('.box-search').addClass('cancel');
                    }
                }
            }
        });
    });
    //选择供应商
    function select(obj){
        $("#inquiry-supplier_name").val(obj.html());
        $('.box-search').addClass('cancel');
    }


    //零件B搜索
    $("#inquiry-goods_number_b").bind('input propertychange', function (e) {
        var good_number_b = $('#inquiry-goods_number_b').val();
        if (good_number_b === '') {
            $('.box-search-goods_number_b').addClass('cancel-goods_number_b');
            return;
        }
        $('.box-search-ul-goods_number_b').html("");
        $('.box-search-goods_number_b').removeClass('cancel-goods_number_b');
        $.ajax({
            type:"GET",
            url:"?r=search/get-new-good-number-b",
            data:{good_number_b:good_number_b},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="selectGoodsB($(this), ' + res.data[i]['id'] + ')" data-goods_number="' + res.data[i]['goods_number'] + '">' + res.data[i]['goods_number_b'] + '</li>';
                    }
                    if (li) {
                        $('.box-search-ul-goods_number_b').append(li);
                    } else {
                        $('.box-search-goods_number_b').addClass('cancel-goods_number_b');
                    }
                }
            }
        })
    });
    //选择零件B
    function selectGoodsB(obj, goods_id){
        $("#inquiry-good_id").val(goods_id);
        $("#inquiry-goods_number_b").val($.trim(obj.html()));
        $("#inquiry-goods_number").val($.trim(obj.data('goods_number')));
        $('.box-search-goods_number_b').addClass('cancel-goods_number_b');
    }

    //零件A搜索
    $("#inquiry-goods_number").bind('input propertychange', function (e) {
        var good_number = $('#inquiry-goods_number').val();
        if (good_number === '') {
            $('.box-search-goods_number').addClass('cancel-goods_number');
            return;
        }
        $('.box-search-ul-goods_number').html("");
        $('.box-search-goods_number').removeClass('cancel-goods_number');
        $.ajax({
            type:"GET",
            url:"?r=search/get-new-good-number",
            data:{good_number:good_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="selectGoodsA($(this), ' + res.data[i]['id'] + ')" data-goods_number_b="' + res.data[i]['goods_number_b'] + '">' + res.data[i]['goods_number'] + '</li>';
                    }
                    if (li) {
                        $('.box-search-ul-goods_number').append(li);
                    } else {
                        $('.box-search-goods_number').addClass('cancel-goods_number');
                    }
                }
            }
        })
    });
    //选择零件A
    function selectGoodsA(obj, goods_id){
        $("#inquiry-good_id").val(goods_id);
        $("#inquiry-goods_number").val($.trim(obj.html()));
        $("#inquiry-goods_number_b").val($.trim(obj.data('goods_number_b')));
        $('.box-search-goods_number').addClass('cancel-goods_number');
    }

</script>
