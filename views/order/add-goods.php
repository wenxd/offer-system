<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Goods;
use app\models\SystemConfig;
$this->title = '订单添加零件';
$this->params['breadcrumbs'][] = $this->title;

//获取税率
$tax_rate = SystemConfig::find()->select('value')->where([
    'title'  => SystemConfig::TITLE_TAX,
    'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
?>
<style>
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
        margin-left: 56px;
        border: 1px solid black;
        z-index: 10;
        float: left;
    }
    .box-search li:hover {
        background-color: #84b5bc;
    }

    .box-search-b li {
        list-style: none;
        padding-left: 10px;
        line-height: 30px;
    }
    .box-search-b-ul {
        margin-left: -40px;
    }
    .box-search-b {
        width: 200px;
        margin-top: -10px;
        margin-left: 312px;
        border: 1px solid black;
        z-index: 10;
    }
    .box-search-b li:hover {
        background-color: #84b5bc;
    }

    .cancel {
        display: none;
    }
    .number {
        margin-left: 10px;
    }
</style>
<!-- Main content -->
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <?= Html::a('新增零件', Url::to(['goods/create']), [
                        'class' => 'btn btn-success',
                        'name'  => 'submit-button',
                        'target' => 'blank',
                ])?>
                <?= Html::a('下载模板', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
                ])?>
                <?= Html::button('导入零件', [
                        'class' => 'btn btn-success upload',
                        'name'  => 'submit-button',
                ])?>
            </div>
            <div class="box-header">
                <form class="form-inline" method="get" action="" id="form">
                    <div class="form-group">
                        <label for="good_number">零件号</label>
                        <input type="text" class="form-control" id="good_number"
                               placeholder="请输入零件号" name="good_number" autocomplete="off"
                               onkeydown="if(event.keyCode == 13){return false;}">

                    </div>
                    <div class="form-group good_number_b">
                        <label for="good_number_b">厂家号</label>
                        <input type="text" class="form-control" id="good_number_b"
                               placeholder="请输入厂家号" autocomplete="off"
                               onkeydown="if(event.keyCode == 13){return false;}">
                    </div>
                    <div class="form-group number">
                        <label for="number">数量</label>
                        <input type="text" class="form-control" id="number"
                               placeholder="请输入零件数量" autocomplete="off"
                               onkeydown="if(event.keyCode == 13){return false;}">
                    </div>
                    <div class="form-group serial">
                        <label for="serial">序号</label>
                        <input type="text" class="form-control" id="serial"
                               placeholder="请输入序号" autocomplete="off"
                               onkeydown="if(event.keyCode == 13){return false;}">
                    </div>
                    <button type="button" class="btn btn-primary add_goods" style="float: right">添加</button>
                </form>
            </div>
            <!-- /.box-header -->
            <div class="box-search cancel">
                <ul class="box-search-ul">

                </ul>
            </div>
            <div class="box-search-b cancel">
                <ul class="box-search-b-ul">

                </ul>
            </div>
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>零件号</th>
                        <th>厂家号</th>
                        <th>原厂家</th>
                        <th>单位</th>
                        <th>税率</th>
                        <th>总成</th>
                        <th>加工</th>
                        <th>特制</th>
                        <th>图片</th>
                        <th>数量</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody class="goods_list">
                        <?php foreach ($tempGoods as $item) :?>
                            <tr class="goods_id" data-id="<?=$item->goods->id?>">
                                <td class="serialNumber"><input type="text" style="width: 50px;" value="<?=$item->serial?>"></td>
                                <td><?=$item->goods->goods_number?></td>
                                <td><?=$item->goods->goods_number_b?></td>
                                <td><?=$item->goods->original_company?></td>
                                <td><?=$item->goods->unit?></td>
                                <td><?=$tax_rate?>></td>
                                <td><?=Goods::$assembly[$item->goods->is_assembly]?></td>
                                <td><?=Goods::$process[$item->goods->is_process]?></td>
                                <td><?=Goods::$special[$item->goods->is_special]?></td>
                                <td><img src="<?=printf('%s/%s', Yii::$app->params['img_url_prefix'], $item->goods->img_id)?>" width="50px"></td>
                                <td class="goodsNumber"><?=$item->number?></td>
                                <td><button type="button" class="btn btn-danger" onclick="deleted(this)">删除</button></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <?= Html::button('保存订单', [
                        'class' => 'btn btn-success order_save',
                        'name'  => 'button']
                )?>
                <?= Html::a('<i class="fa fa-reply"></i> 返回上一页', Url::to(['order/create']), [
                    'class' => 'btn btn-default btn-flat',
                ])?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    //零件搜索
    $("#good_number").bind('input propertychange', function (e) {
        var good_number = $('#good_number').val();
        if (good_number === '') {
            $('.box-search').addClass('cancel');
            return;
        }
        $('.box-search-ul').html("");
        $('.box-search').removeClass('cancel');
        $.ajax({
            type:"GET",
            url:"?r=search/get-good-number",
            data:{good_number:good_number},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="select($(this))">' + res.data[i] + '</li>';
                    }
                    if (li) {
                        $('.box-search-ul').append(li);
                    } else {
                        $('.box-search').addClass('cancel');
                    }
                }
            }
        });
    });

    //厂家号搜索
    $("#good_number_b").bind('input propertychange', function (e) {
        var good_number_b = $('#good_number_b').val();
        if (good_number_b === '') {
            $('.box-search-b').addClass('cancel');
            return;
        }
        $('.box-search-b-ul').html("");
        $('.box-search-b').removeClass('cancel');
        $.ajax({
            type:"GET",
            url:"?r=search/get-good-number-b",
            data:{good_number_b:good_number_b},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    var li = '';
                    for (var i in res.data) {
                        li += '<li onclick="selectB($(this))">' + res.data[i] + '</li>';
                    }
                    if (li) {
                        $('.box-search-b-ul').append(li);
                    } else {
                        $('.box-search-b').addClass('cancel');
                    }
                }
            }
        });
    });

    $("#good_number").focus(function (e) {
        $('#good_number_b').val('')
    });

    $("#good_number_b").focus(function (e) {
        $('#good_number').val('')
    });

    $('.add_goods').click(function(e){
        var goods_id   = $('#good_number').val();
        var goods_id_b = $('#good_number_b').val();
        if (goods_id === '' && goods_id_b === '') {
            layer.msg('输入厂家号或者厂家号', {time:2000});
            return false;
        }

        var number = $('#number').val();
        var reg = /^[0-9]*$/;
        if (!reg.test(number) || number <= 0) {
            layer.msg('数量请输入正整数', {time:2000});
            return false;
        }

        var serialNumber = $('#serial').val();
        if (serialNumber === '') {
            layer.msg('请输入序号', {time:2000});
            return false;
        }
        $.ajax({
            type:"post",
            url:"?r=order/add-goods",
            data:{goods_id:goods_id, goods_id_b:goods_id_b},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    //判断是否存在此零件
                    var goodsIds = $('.goods_list').find('.goods_id');
                    var open = false;
                    goodsIds.each(function (i, e) {
                        if ($(e).find('.serialNumber input').val() == serialNumber) {
                            open = true;
                        }
                    });
                    if (open) {
                        layer.msg('已添加了此序号', {time:2000});
                        return false;
                    }
                    //添加此零件
                    var tr = '<tr class="goods_id" data-id="' + res.data.id +'">';
                    tr += '<td class="serialNumber"><input type="text" style="width: 50px;" value="'+ serialNumber +'"/></td>';
                    tr += '<td>' + res.data.goods_number + '</td>';
                    tr += '<td>' + res.data.goods_number_b + '</td>';
                    tr += '<td>' + res.data.original_company + '</td>';
                    tr += '<td>' + res.data.unit + '</td>';
                    tr += '<td>' + '<?=$tax_rate?>' + '</td>';
                    tr += '<td>' + (res.data.is_assembly == 0 ? '否' : '是') + '</td>';
                    tr += '<td>' + (res.data.is_process == 0 ? '否' : '是') + '</td>';
                    tr += '<td>' + (res.data.is_special == 0 ? '否' : '是') + '</td>';
                    tr += '<td><img src="' + '<?=Yii::$app->params['img_url_prefix'] . '/'?>' + res.data.img_id + '" width="50px"></td>';
                    tr += '<td class="goodsNumber">' + number + '</td>';
                    tr += '<td><button type="button" class="btn btn-danger" onclick="deleted(this)">删除</button></td>';
                    tr += '</tr>';
                    $('.goods_list').append(tr);
                    if ($('.select_all').prop('checked')) {
                        $('.select_id').prop("checked",$('.select_all').prop('checked'));
                    }
                    $('#good_number').val('');
                    $('#number').val('');
                } else {
                    layer.msg(res.msg, {time:2000});
                }
            }
        });
    });

    function select(obj){
        $("#good_number").val(obj.html());
        $('.box-search').addClass('cancel');
    }
    function selectB(obj){
        $("#good_number_b").val(obj.html());
        $('.box-search-b').addClass('cancel');
    }
    $('.order_save').click(function (e) {
        var goods  = $('.goods_id');
        var goodsIds = [];
        var goodsInfo = [];
        goods.each(function (i, e) {
            var item = {};
            goodsIds.push($(e).data('id'));
            item.goods_id = $(e).data('id');
            item.number   = $(e).find('.goodsNumber').text();
            item.serial   = $(e).find('.serialNumber input').val();
            goodsInfo.push(item);
        });

        var url = location.search;
        url = url.substr(17);
        $.ajax({
            type:"post",
            url:'?r=order/save-order' + url,
            data:{goodsIds:goodsIds, goodsInfo:goodsInfo},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    location.replace("?r=order/index");
                }
            }
        });
    });

    //全选
    $('.select_all').click(function (e) {
        $('.select_id').prop("checked",$(this).prop("checked"));
    });

    //子选择
    $('.goods_list').on('click', '.select_id', function (e) {
        if ($('.select_id').length == $('.select_id:checked').length) {
            $('.select_all').prop("checked",true);
        } else {
            $('.select_all').prop("checked",false);
        }
    });

    function deleted(obj) {
        $(obj).parent().parent().remove();
    }
    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url : '?r=order/upload',
        //上传文件类型
        accept:'.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend : function () {
            layer.msg('上传中。。。', {
                icon: 16 ,shade: 0.01
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
                    var url = window.location.href;
                    url += '&token=' + data.data;
                    window.location.href = url
                });
            }else{
                //失败提示
                layer.msg(data.msg,{icon:1});
            }
        }
    });

</script>
