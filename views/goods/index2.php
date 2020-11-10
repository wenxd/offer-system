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

$this->title = '零件发行价记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
    </div>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => [
                'firstPageLabel' => '首页',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'lastPageLabel' => '尾页',
            ],
            'columns' => [
//                ['class' => CheckboxColumn::className(),],
                'id',
                'material_code',
                [
                    'attribute' => 'goods_number',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::a($model->goods_number, Url::to(['view', 'id' => $model->id]));
                    }
                ],
                'original_company',
                'goods_number_b',
                [
                    'attribute' => 'publish_tax_price',
                    'value' => function ($model) {
                        return $model->publish_tax_price != 0 ? $model->publish_tax_price : '';
                    }
                ],
                [
                    'attribute' => 'estimate_publish_price',
                    'value' => function ($model) {
                        return $model->estimate_publish_price != 0 ? $model->estimate_publish_price : '';
                    }
                ],
                [
                    'attribute' => 'factory_price',
                    'value' => function ($model) {
                        return $model->factory_price != 0 ? $model->factory_price : '';
                    }
                ],
                [
                    'attribute' => 'publish_tax',
                    'value' => function ($model) {
                        return $model->publish_tax != 0 ? $model->publish_tax : '';
                    }
                ],
                [
                    'attribute' => 'publish_type',
                    'value' => function ($model) {
                        return $model->publish_type ?? '';
                    }
                ],
                [
                    'attribute' => 'is_publish_accuracy',
                    'filter' => Goods::$standard,
                    'value' => function ($model) {
                        return Goods::$standard[$model->is_publish_accuracy];
                    }
                ],
                [
                    'attribute' => 'is_price',
                    'filter' => Goods::$standard,
                    'value' => function ($model) {
                        return Goods::$standard[$model->is_price];
                    }
                ],
//                [
//                    'attribute' => 'updated_at',
//                    'format' => 'raw',
//                    'value' => function ($model) {
//                        return substr($model->updated_at, 0, 10);
//                    }
//                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return substr($model->created_at, 0, 10);
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    function locking(id) {
        $.ajax({
            type:"get",
            url:'?r=goods/locking',
            data:{id:id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    window.location.reload();
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload',
        //上传文件类型
        accept: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend: function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress: function (e) {
        },
        error: function () {
        },
        success: function (data) {
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                //导入成功
                layer.msg(data.msg, {icon: 1});
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    $('.upload-son').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload-son',
        //上传文件类型
        accept: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend: function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress: function (e) {
        },
        error: function () {
        },
        success: function (data) {
            console.log(data);
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                //导入成功
                layer.msg(data.msg, {time: 5000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    $('.upload-check').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload-check',
        //上传文件类型
        accept: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend: function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress: function (e) {
        },
        error: function () {
        },
        success: function (data) {
            console.log(data);
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                window.location.href = '?r=goods/upload-check';
                //导入成功
                layer.msg(data.msg, {time: 5000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    //生成询价单
    $('.add_inquiry').click(function () {
        var goods_ids = [];
        $("input[name='selection[]']").each(function (i, e) {
            if ($(e).prop('checked')) {
                goods_ids.push($(e).val())
            }
        });
        if (!goods_ids.length) {
            layer.msg('请选择要询价的商品', {icon: 1});
            return;
        }
        $.ajax({
            type: "POST",
            url: "?r=goods/inquiry-order",
            data: {goods_ids: goods_ids},
            dataType: 'JSON',
            success: function (res) {
                if (res && res.code == 200) {
                    location.replace("?r=order/create&temp_id=" + res.data);
                } else {
                    layer.msg(res.msg, {time: 2000});
                    return false;
                }
            }
        });
    });
</script>
