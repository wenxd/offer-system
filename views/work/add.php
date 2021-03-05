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

$this->title = '添加';
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
<table class="table table-bordered" id="items">
    <tr>
        <th>
            序号
        </th>
        <th v-for="(title, index) in titles">
            <input type="text" class="form-control"  v-model="titles[index]" v-if="index < 1">
            <div class="row" v-if="index >= 1">
                <div class="col-md-9">
                    <input type="text" v-model="titles[index]" class="form-control">
                </div>
                <div class="col-md-3">
                    <button title="删除本列" type="button" class="btn btn-danger btn-sm" @click.stop="del_col(index)">
                        <i class="fa fa-trash" ></i>
                    </button>
                </div>
            </div>
        </th>
        <th>
            <!--添加列-->
            <button title="添加一列" type="button" class="btn btn-primary     btn-sm" onclick="add_col()">
                <i class="fa fa-plus" > 列</i>
            </button>
        </th>

    </tr>
    <tr v-for="(item, index) in items">
        <td>{{ 1+index }}</td>
        <td v-for="(item_1, index_1) in item">
            <textarea class="form-control" v-model="items[index][index_1]"  rows="<?=$rows?>"></textarea>
        </td>
        <td v-if="index == 0">
            <button title="添加一行" type="button"class="btn btn-success btn-sm" onclick="add_row()">
                <i class="fa fa-plus" > 行</i>
            </button>
        </td>
        <td v-if="index >= 1">
            <button title="删除本行" type="button" class="btn btn-danger btn-sm"  @click.stop="del_row(index)">
                <i class="fa fa-trash" ></i>
            </button>
        </td>
    </tr>
</table>
<button type="button" style="width: 40%" class="btn btn-success btn-sm"  onclick="save()">保存</button>
<script>
    var items = <?=json_encode($items)?>;
    var titles = <?=json_encode($titles)?>;
    var example1 = new Vue({
        el: '#items',
        data: {
            items: items,
            titles: titles
        },
        methods: {
            clickMe(index){
                console.log(index)
            },
        }
    })

    // 添加列
    function add_col() {
        titles.push('');
        for (i in items) {
            this.items[i].push('');
        }
        console.log(titles, items)
    }

    // 删除列
    function del_col(id) {
        titles.splice(id,1);
        for (i in items) {
            this.items[i].splice(id,1);
        }
        console.log(titles, items)
    }

    // 添加行
    function add_row() {
        var item = [];
        for (i in titles) {
            item.push('');
        }
        items.push(item);
        console.log(titles, items)
    }

    // 删除行
    function del_row(id) {
        items.splice(id,1);
        console.log(titles, items)
    }
    
    // 添加或保存数据
    function save() {
        console.log(titles, items)
        $.ajax({
            type:"post",
            url:'?r=work/add',
            data:{titles:titles, items:items},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>