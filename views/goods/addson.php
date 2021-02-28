<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
use app\models\Goods;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '添加子零件';
$this->params['breadcrumbs'][] = $this->title;
?>
<h3>零件号：<?="{$goods_info['goods_number']}    （{$goods_info['material_code']}）"?></h3>
<div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '<div class="box-body table-responsive no-padding">{items}</div><div class="box-footer clearfix"><div class="col-sm-5">{summary}</div><div class="col-sm-7">{pager}</div></div>',
        'pager'        => [
            'firstPageLabel' => '首页',
            'prevPageLabel'  => '上一页',
            'nextPageLabel'  => '下一页',
            'lastPageLabel'  => '尾页',
        ],
        'columns' => [
//            [
//                'class' => CheckboxColumn::className(),
//            ],
            'id',
            'material_code',
            'goods_number',
            [
                'attribute'      => '子零件数量',
                'contentOptions' => ['style'=>'min-width: 20px;'],
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    if (isset($model->son->number)) {
                        return Html::input('text', 'son_number', $model->son->number);
                    }
                    return Html::input('text', 'son_number');
                }
            ],
            'remark',
            'original_company',
            'goods_number_b',
            'description',
            'description_en',
            [
                'attribute' => '操作',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    if (isset($model->son->number)) {
                        return Html::button('<i class="fa fa-edit" ></i>', [
                                'class' => 'btn btn-warning btn-xs',
                                'onclick' => 'addson(' . $model->id . ', this)',
                                'name'  => 'submit-button']
                        );
                    }
                    return Html::button('<i class="fa fa-plus"></i>', [
                            'class' => 'btn btn-success btn-xs',
                            'onclick' => 'addson(' . $model->id . ', this)',
                            'name'  => 'submit-button']
                    );
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    var p_goods_id = <?=$_GET['id']?>;
    function addson(id, obj) {
        // var number = $(this).parent().parent().find('input[name="son_number"]').html();
        var number = $(obj).parent().parent().find('input[name="son_number"]').val();
        var preg = /[1-9]\d*/i;
        if (!preg.test(number)) {
            layer.msg('请输入正确的子零件数量', {icon:2});
            return;
        }
        var goods_list = [
            {"goods_id": id, "number": number}
        ];
        $.ajax({
            type:"POST",
            url:"?r=goods/do-addson",
            data:{p_goods_id:p_goods_id, goods_list:goods_list},
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

    //生成询价单
    $('.add_son').click(function () {
        var p_goods_id = '<?=$_GET['id'] ?? 0?>';
        if (!p_goods_id) {
            layer.msg('请从零件列表进入', {icon:1});
            return;
        }

        var flag = false;
        var goods_list = [];
        $("input[name='selection[]']").each(function (i, e) {
            if ($(e).prop('checked')) {
                var item = {};
                item.goods_id = $(e).val();
                var number = $(e).parent().parent().find('input[name="son_number"]').val();
                var preg = /[1-9]\d*/i;
                if (!preg.test(number)) {
                    flag = true;
                }
                item.number = number;
                goods_list.push(item);
            }
        });
        if (flag) {
            layer.msg('请输入正确的子零件数量', {icon:1});
            return;
        }

        if (!goods_list.length) {
            layer.msg('请选择要添加的子商品', {icon:1});
            return;
        }
        $.ajax({
            type:"POST",
            url:"?r=goods/do-addson",
            data:{p_goods_id:p_goods_id, goods_list:goods_list},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    location.replace("?r=goods/index");
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    });
</script>
