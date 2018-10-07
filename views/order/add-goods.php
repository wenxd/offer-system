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
                                   placeholder="请输入零件号，如：1001" name="good_number" autocomplete="off">
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
                            <th>是否最新</th>
                            <th>是否优选</th>
                            <th>商品类型</th>
                            <th style="width: 150px;">报价金额</th>
                            <th>库存数量</th>
                            <th style="width: 150px;">询价时间</th>
                            <th>供应商ID</th>
                            <th>供应商名称</th>
                            <th>购买数量</th>
                            <th>金额</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="box-footer">
                    <?= Html::button('保存订单', [
                            'class' => 'btn btn-success order_save',
                            'name'  => 'submit-button']
                    )?>
                    <?= Html::a('<i class="fa fa-reply"></i> 返回上一页', Url::to(['search/index']), [
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


                    var table = '';
                    table += '<table id="example2" class="table table-bordered table-hover">';

                    for (var i in res.data) {
                        li += '<li onclick="select($(this))">' + res.data[i] + '</li>';
                    }

                    if (li) {
                        $('.box-search-ul').append(li);
                    } else {
                        $('.box-search').addClass('cancel');
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
</script>
