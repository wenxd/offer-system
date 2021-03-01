<?php

use app\extend\grid\ActionColumn;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use app\extend\widgets\Bar;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '零件总成';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>零件号：<?="{$goods_info['goods_number']}    （{$goods_info['material_code']}）"?>
    <button type="button" class="RoleOfadd btn btn-success btn-xs" onclick="add(<?=$goods_info['id']?>)" style="margin-right:15px;">
        <i class="fa fa-plus" ></i>
    </button>
</h3>

<table id="table"></table>
<br/>
<!--<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">-->
<link href="https://cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-treegrid/0.2.0/css/jquery.treegrid.min.css">
<script src="https://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-table/1.12.1/bootstrap-table.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-table/1.12.0/extensions/treegrid/bootstrap-table-treegrid.js"></script>
<script src="https://cdn.bootcss.com/jquery-treegrid/0.2.0/js/jquery.treegrid.min.js"></script>
<script type="text/javascript">
    var $table = $('#table');
    var data = <?=$data?>;

    $(function () {

        $table.bootstrapTable({
            data: data,
            idField: 'id',
            dataType: 'jsonp',
            columns: [
                {field: 'id', title: '序号'},
                {
                    field: 'goods_number', title: '零件号', cellStyle: {
                        css: {"width": "20%"}
                    }
                },
                {field: 'material_code', title: '品牌'},
                {field: 'description', title: '中文描述'},
                {field: 'description_en', title: '英文描述'},
                {field: 'number', title: '上级总成需求数量'},
                {field: 'original_company', title: '厂家'},
                {field: 'goods_number_b', title: '厂家号'},
                // {field: 'id', title: '编号', sortable: true, align: 'center'},
                // {field: 'pid', title: '所属上级'},
                // { field: 'status',  title: '状态', sortable: true,  align: 'center', formatter: 'statusFormatter'  },
                // { field: 'permissionValue', title: '权限值'  },
                {field: 'operate', title: '操作', align: 'center', events: operateEvents, formatter: 'operateFormatter'},
            ],

            // bootstrap-table-treegrid.js 插件配置 -- start

            //在哪一列展开树形
            treeShowField: 'goods_number',
            //指定父id列
            parentIdField: 'pid',

            onResetView: function (data) {
                //console.log('load');
                $table.treegrid({
                    initialState: 'collapsed',// 所有节点都折叠
                    // initialState: 'expanded',// 所有节点都展开，默认展开
                    treeColumn: 1,
                    // expanderExpandedClass: 'glyphicon glyphicon-minus',  //图标样式
                    // expanderCollapsedClass: 'glyphicon glyphicon-plus',
                    onChange: function () {
                        $table.bootstrapTable('resetWidth');
                    }
                });

                //只展开树形的第一级节点
                // $table.treegrid('getRootNodes').treegrid('expand');

            },
            onCheck: function (row) {
                var datas = $table.bootstrapTable('getData');
                // 勾选子类
                selectChilds(datas, row, "id", "pid", true);

                // 勾选父类
                selectParentChecked(datas, row, "id", "pid")

                // 刷新数据
                $table.bootstrapTable('load', datas);
            },

            onUncheck: function (row) {
                var datas = $table.bootstrapTable('getData');
                selectChilds(datas, row, "id", "pid", false);
                $table.bootstrapTable('load', datas);
            },
            // bootstrap-table-treetreegrid.js 插件配置 -- end
        });
    });

    // 格式化按钮
    function operateFormatter(value, row, index) {
        return [
            '<button type="button" class="RoleOfadd btn btn-success btn-xs" style="margin-right:15px;"><i class="fa fa-plus" ></i></button>',
            '<button type="button" class="RoleOfdelete btn btn-danger btn-xs"><i class="fa fa-trash-o" ></i></button>'
        ].join('');

    }

    // 格式化类型
    function typeFormatter(value, row, index) {
        if (value === 'menu') {
            return '菜单';
        }
        if (value === 'button') {
            return '按钮';
        }
        if (value === 'api') {
            return '接口';
        }
        return '-';
    }

    // 格式化状态
    function statusFormatter(value, row, index) {
        if (value === 1) {
            return '<span class="label label-success">正常</span>';
        } else {
            return '<span class="label label-default">锁定</span>';
        }
    }

    //初始化操作按钮的方法
    window.operateEvents = {
        'click .RoleOfadd': function (e, value, row, index) {
            add(row.id);
        },
        'click .RoleOfdelete': function (e, value, row, index) {
            console.log();
            del(row.relation_id);
        },
        'click .RoleOfedit': function (e, value, row, index) {
            update(row.id);
        }
    };
</script>
<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script>
    /**
     * 选中父项时，同时选中子项
     * @param datas 所有的数据
     * @param row 当前数据
     * @param id id 字段名
     * @param pid 父id字段名
     */
    function selectChilds(datas, row, id, pid, checked) {
        for (var i in datas) {
            if (datas[i][pid] == row[id]) {
                datas[i].check = checked;
                selectChilds(datas, datas[i], id, pid, checked);
            }
            ;
        }
    }

    function selectParentChecked(datas, row, id, pid) {
        for (var i in datas) {
            if (datas[i][id] == row[pid]) {
                datas[i].check = true;
                selectParentChecked(datas, datas[i], id, pid);
            }
            ;
        }
    }

    function test() {
        var selRows = $table.bootstrapTable("getSelections");
        if (selRows.length == 0) {
            alert("请至少选择一行");
            return;
        }

        var postData = "";
        $.each(selRows, function (i) {
            postData += this.id;
            if (i < selRows.length - 1) {
                postData += "， ";
            }
        });
        alert("你选中行的 id 为：" + postData);

    }

    function add(id) {
        layer.open({
            type: 2,
            title: '添加子零件页',
            shadeClose: true,
            shade: 0.8,
            area: ['90%', '90%'],
            content: '?r=goods/addson&id=' + id, //iframe的url
            end: function () {//无论是确认还是取消，只要层被销毁了，end都会执行，不携带任何参数。layer.open关闭事件
                location.reload();　　//layer.open关闭刷新
            }
        });
    }

    function del(id) {
        // alert("del 方法 , id = " + id);
        var ids = [];
        ids.push(id);
        $.ajax({
            type:"POST",
            url:"?r=goods/del-son",
            data:{ids:ids},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    location.reload();　　//layer.open关闭刷新
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>