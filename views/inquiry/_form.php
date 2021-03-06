<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Supplier;
use app\models\{Inquiry, Goods, Admin, AuthAssignment, SystemConfig, InquiryGoods};
/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */
/* @var $form yii\widgets\ActiveForm */
//获取税率
if ($model->isNewRecord) {
    $model->tax_rate = SystemConfig::find()->select('value')->where([
        'title'  => SystemConfig::TITLE_TAX,
        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
    if (isset($_GET['goods_id']) && $_GET['goods_id']) {
        $model->good_id = $_GET['goods_id'];
        $goods = Goods::findOne($_GET['goods_id']);
        $model->goods_number   = $goods->goods_number;
        $model->goods_number_b = $goods->goods_number_b;
    }
    $model->inquiry_datetime = date('Y-m-d');
    $model->delivery_time    = SystemConfig::find()->select('value')->where([
        'title'  => SystemConfig::TITLE_DELIVERY_TIME,
        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
    if (!isset($_GET['inquiry_goods_id'])) {
        $model->order_id         = 0;
        $model->order_inquiry_id = 0;   
    }
    $model->is_confirm_better = 0;
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
//通过inquiry_goods_id查询数量
if (isset($_GET['inquiry_goods_id'])) {
    $inquiryGoods = InquiryGoods::findOne($_GET['inquiry_goods_id']);
    $model->number           = $inquiryGoods->number;
    $model->order_id         = $inquiryGoods->order_id;
    $model->order_inquiry_id = $inquiryGoods->order_inquiry_id;
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

    /*零件号*/
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

    /*厂家号*/
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

    <div class="box-header">
        <?= Html::submitButton('保存', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
                'name'  => 'submit-button']
        )?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ])?>
        <?php if ($is_super) :?>
        <?= Html::a('下载模板', Url::to(['download']), [
            'data-pjax' => '0',
            'class'     => 'btn btn-primary btn-flat',
        ])?>
        <?= Html::button('批量询价', [
            'class' => 'btn btn-success upload',
            'name'  => 'submit-button',
        ])?>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?= $form->field($model, 'good_id')->textInput()->hiddenInput()->label(false) ?>

        <?php if ($is_super):?>
            <?= $form->field($model, 'goods_number')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('零件号') ?>
            <div class="box-search-goods_number cancel-goods_number">
                <ul class="box-search-ul-goods_number">

                </ul>
            </div>
        <?php endif;?>

        <div class="form-group field-inquiry-supplier_name">
            <label class="control-label" for="inquiry-supplier_name">供应商</label>
            <input type="text" id="inquiry-supplier_name" class="form-control" name="Inquiry[supplier_name]"
                   value="<?=$model->supplier_name?>" autocomplete="off" aria-invalid="false">
            <div class="help-block"></div>
        </div>

        <div class="box-search cancel">
            <ul class="box-search-ul">

            </ul>
        </div>

        <?= $form->field($model, 'tax_rate')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'tax_price')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'all_price')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'all_tax_price')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

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

        <?= $form->field($model, 'delivery_time')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('货期(周)');?>

        <?= $form->field($model, 'technique_remark')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        
        <?= $form->field($model, 'remark')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

        <?= $form->field($model, 'is_better')->dropDownList(Inquiry::$better) ?>

        <?= $form->field($model, 'better_reason')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

        <?= $form->field($model, 'admin_id')->dropDownList($admins)->label('选择询价员') ?>

        <?= $form->field($model, 'order_id')->textInput(['readonly' => true, 'autocomplete' => 'off'])->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'order_inquiry_id')->textInput(['readonly' => true, 'autocomplete' => 'off'])->hiddenInput()->label(false)  ?>

        <?= $form->field($model, 'is_confirm_better')->radioList(Inquiry::$better) ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton('保存', [
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
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    //禁用页面回车提交表单
    document.onkeypress = function(event){
        if(event.keyCode == 13) {
            return false;
        }
    };
    //实现税率自动转换
    //实现税率自动转换
    $("#inquiry-tax_rate").bind('input propertychange', function (e) {
        var tax   = $(this).val();
        $('#inquiry-tax_rate').val(tax);
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        var number = $('#inquiry-number').val() ? $('#inquiry-number').val() : 0;
        var all_price = price * number;
        var all_tax_price = tax_price * number;
        $("#inquiry-tax_price").attr("value", tax_price.toFixed(2));
        $("#inquiry-tax_price").val(tax_price.toFixed(2));
        $("#inquiry-all_price").val(all_price.toFixed(2));
        $("#inquiry-all_tax_price").val(all_tax_price.toFixed(2));
    });

    $("#inquiry-price").bind('input propertychange', function (e) {
        var tax = $('#inquiry-tax_rate').val();
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        var number = $('#inquiry-number').val() ? $('#inquiry-number').val() : 0;
        var all_price = price * number;
        var all_tax_price = tax_price * number;
        $("#inquiry-tax_price").attr("value",tax_price.toFixed(2));
        $("#inquiry-tax_price").val(tax_price.toFixed(2));
        $("#inquiry-all_price").val(all_price.toFixed(2));
        $("#inquiry-all_tax_price").val(all_tax_price.toFixed(2));
    });
    $("#inquiry-tax_price").bind('input propertychange', function (e) {
        var tax = $('#inquiry-tax_rate').val();
        var tax_price = $('#inquiry-tax_price').val();
        var price = tax_price / (1 + tax/100);
        var number = $('#inquiry-number').val() ? $('#inquiry-number').val() : 0;
        var all_price = price * number;
        var all_tax_price = tax_price * number;
        $("#inquiry-price").attr("value",price.toFixed(2));
        $("#inquiry-price").val(price.toFixed(2));
        $("#inquiry-all_price").val(all_price.toFixed(2));
        $("#inquiry-all_tax_price").val(all_tax_price.toFixed(2));
    });
    $("#inquiry-number").bind('input propertychange', function (e) {
        var tax = $('#inquiry-tax_rate').val();
        var price = $('#inquiry-price').val();
        var tax_price = price * (1 + tax/100);
        var number = $('#inquiry-number').val() ? $('#inquiry-number').val() : 0;
        var all_price = price * number;
        var all_tax_price = tax_price * number;
        $("#inquiry-all_price").val(all_price.toFixed(2));
        $("#inquiry-all_tax_price").val(all_tax_price.toFixed(2));
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
        var is_inquiry = '<?=$_GET['inquiry_goods_id'] ?? 0?>';
        if (is_inquiry) {
            var goods_id = '<?=$_GET['goods_id'] ?? 0?>';
            $('#inquiry-better_reason').val('原厂家');
        }
    }

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
            url:"?r=search/get-good-number",
            data:{good_number:good_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="selectGoodsA($(this))" data-goods_id="' + res.data[i].id +
                            '" data-goods_number="' + res.data[i].goods_number + '">' +
                            res.data[i].goods_number + ' ' + res.data[i].material_code + '</li>';
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
    function selectGoodsA(obj){
        $("#inquiry-good_id").val(obj.data('goods_id'));
        $("#inquiry-goods_number").val($.trim(obj.html()));
        $('.box-search-goods_number').addClass('cancel-goods_number');
    }

    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url : '?r=inquiry/upload',
        //上传文件类型
        accept:'.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend : function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress : function (e) {},
        error : function () {},
        success : function (data) {
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if(data.code == 200){
                //导入成功
                layer.msg(data.msg,{time:3000},function(){
                    window.location.reload();
                });
            }else{
                //失败提示
                layer.msg(data.msg,{icon:1});
            }
        }
    });
</script>
