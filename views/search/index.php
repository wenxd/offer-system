<?php
use yii\helpers\Html;
$this->title = '新建报价单/询价单';
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
                            <label for="good_id">零件号</label>
                            <input type="text" class="form-control" id="good_id"
                                   placeholder="请输入零件号，如：1001" name="good_id" autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary" style="float: right">查询</button>
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-search cancel">
                    <ul class="box-search-ul">

                    </ul>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript">
    $("#good_id").bind('input propertychange', function (e) {
        var good_number = $('#good_id').val();
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
        })
    });
    $('#form').submit(function(e){
        e.preventDefault();
        var form = $(this).serializeArray();
        var parameter = '';
        $.each(form, function() {
            parameter += this.name + '=' + this.value + '&';
        });
        parameter += 'type=1';
        window.location.href = location.href.split("?")[0] + "?r=search/search&" + parameter;
    });

    function select(obj){
        $("#good_id").val(obj.html());
        $('.box-search').addClass('cancel');
    }
</script>