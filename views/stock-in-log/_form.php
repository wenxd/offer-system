<?php

use app\models\AuthAssignment;use app\models\SystemConfig;
use yii\helpers\ArrayHelper;use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StockLog */
/* @var $form yii\widgets\ActiveForm */

$use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '库管员B']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
?>
<style>
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
</style>

<div class="box">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>
    <?php if (!in_array($userId, $adminIds)):?>
        <div class="box-header">
            <?= Html::a('下载模板', Url::to(['download']), [
                'data-pjax' => '0',
                'class'     => 'btn btn-primary btn-flat',
            ])?>
            <?= Html::button('批量入库', [
                'class' => 'btn btn-success upload',
                'name'  => 'submit-button',
            ])?>
        </div>
    <?php endif;?>
    <div class="box-body">

        <?= $form->field($model, 'goods_id')->textInput()->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'goods_number')->textInput()->label('零件号') ?>

        <div class="box-search-goods_number cancel-goods_number">
            <ul class="box-search-ul-goods_number">

            </ul>
        </div>

        <?= $form->field($model, 'number')->textInput()->label('入库数量') ?>

        <?= $form->field($model, 'position')->textInput() ?>

        <?= $form->field($model, 'source')->dropDownList(SystemConfig::getList(SystemConfig::TITLE_STOCK_SOURCE))->label('来源')?>

        <?= $form->field($model, 'type')->dropDownList(['1' => '入库'])->label('入库') ?>

        <?= $form->field($model, 'remark')->textInput() ?>

    </div>

    <div class="box-footer">
        <?= Html::Button($model->isNewRecord ? '添加' :  '更新', [
                'class' => 'btn btn-success stock-created',
                'name'  => 'submit-button']
        )?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    //零件A搜索
    $("#stocklog-goods_number").blur(function () {
        var good_number = $('#stocklog-goods_number').val();
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
                        li += '<li onclick="selectGoodsA($(this))" data-goods_id="' + res.data[i].id +
                            '" data-position="' + res.data[i].stock_position +
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
        $("#stocklog-goods_id").val(obj.data('goods_id'));
        $("#stocklog-goods_number").val($.trim(obj.html()));
        $('.box-search-goods_number').addClass('cancel-goods_number');
        $('#stocklog-position').val(obj.data('position'));
    }

    $('.stock-created').click(function (e) {
        //防止双击
        $(".stock-created").attr("disabled", true).addClass("disabled");
        var goods_id = $('#stocklog-goods_id').val();
        if (!goods_id) {
            layer.msg('请输入零件号', {time:2000});
            $(".stock-created").removeAttr("disabled").removeClass("disabled");
            return false;
        }

        var number = $('#stocklog-number').val();
        var reg = /^\+?[1-9][0-9]*$/;
        if (!reg.test(number)) {
            layer.msg('请输入正整数', {time:2000});
            $(".stock-created").removeAttr("disabled").removeClass("disabled");
            return false;
        }

        var position = $('#stocklog-position').val();
        if (!position) {
            layer.msg('请输入库存位置', {time:2000});
            $(".stock-created").removeAttr("disabled").removeClass("disabled");
            return false;
        }

        var source = $('#stocklog-source').find("option:selected").text();
        if (!source) {
            layer.msg('请输入来源', {time:2000});
            $(".stock-created").removeAttr("disabled").removeClass("disabled");
            return false;
        }

        var remark = $('#stocklog-remark').val();
        // if (!remark) {
        //     layer.msg('请输入备注说明', {time:2000});
        //     return false;
        // }

        $.ajax({
            type:"POST",
            url:"?r=stock-in-log/add",
            data:{goods_id:goods_id, number:number, position:position, source:source, remark:remark},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    location.replace("?r=stock-in-log");
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    });

    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url : '?r=stock-in-log/upload',
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