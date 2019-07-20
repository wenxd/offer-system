<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = '订单添加零件';
$this->params['breadcrumbs'][] = $this->title;
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
    }
    .box-search li:hover {
        background-color: #84b5bc;
    }
    .cancel {
        display: none;
    }
</style>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <form class="form-inline" method="get" action="" id="form">
                        <div class="form-group">
                            <label for="good_number">零件号</label>
                            <input type="text" class="form-control" id="good_number"
                                   placeholder="请输入零件号，如：1001" name="good_number" autocomplete="off"
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
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" class="select_all"></th>
                            <th>零件号</th>
                            <th>原厂家</th>
                            <th>单位</th>
                            <th>数量</th>
                            <th>供应商</th>
                            <th>税率</th>
                            <th>未税单价</th>
                            <th>含税单价</th>
                            <th>货期</th>
                            <th>询价状态</th>
                            <th>询价员/采购员</th>
                            <th>未税总价</th>
                            <th>含税总价</th>
                            <th>是否加工</th>
                            <th>是否特制</th>
                            <th>图片</th>
                        </tr>
                        </thead>
                        <tbody class="goods_list">

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
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
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
    $('.add_goods').click(function(e){
        var goods_id = $('#good_number').val();
        if (goods_id === '') {
            layer.msg('零件号不能为空', {time:2000});
            return false;
        }
        $.ajax({
            type:"post",
            url:"?r=order/add-goods",
            data:{goods_id:goods_id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    //判断是否存在此零件
                    var goodsIds = $('.goods_list').find('tr').children(':first-child').children();
                    var length = goodsIds.length;
                    for(var i = 0; i < length; i++) {
                        var goods = goodsIds[i];
                        if (res.data.id == goods.value) {
                            return false;
                        }
                    }
                    //添加此零件
                    var tr = '<tr>';
                    tr += '<td><input type="checkbox" name="select_id" value="' + res.data.id + '" class="select_id"></td>';
                    tr += '<td>' + res.data.goods_number + '</td>';
                    tr += '<td>' + res.data.original_company + '</td>';
                    tr += '<td>' + res.data.unit + '</td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td>16%</td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td></td>';
                    tr += '<td>' + (res.data.is_process == 0 ? '否' : '是') + '</td>';
                    tr += '<td>' + (res.data.is_special == 0 ? '否' : '是') + '</td>';
                    tr += '<td><img src="' + '<?=Yii::$app->params['img_url_prefix'] . '/'?>' + res.data.img_id + '" width="50px"></td>';
                    tr += '</tr>';
                    $('.goods_list').append(tr);
                    if ($('.select_all').prop('checked')) {
                        $('.select_id').prop("checked",$('.select_all').prop('checked'));
                    }
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

    $('.order_save').click(function (e) {
        var select_length = $('.select_id:checked').length;
        if (!select_length) {
            layer.msg('请最少选择一个零件', {time:2000});
            return false;
        }

        var goods = $('.goods_list').find('tr').children(':first-child').children();
        var length = goods.length;
        var goodsIds = [];
        for(var i = 0; i < length; i++) {
            if (goods[i].checked) {
                goodsIds.push(goods[i].value);
            }
        }

        var url = location.search;
        url = url.substr(17);
        $.ajax({
            type:"post",
            url:'?r=order/save-order' + url,
            data:{goodsIds:goodsIds},
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

</script>
