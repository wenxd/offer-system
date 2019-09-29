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

$this->title = '零件列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {delete} {download} {upload} {inquiry}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载模板', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload' => function () {
                    return Html::a('<i class="fa fa-upload"></i> 上传导入', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat upload',
                    ]);
                },
                'inquiry' => function () {
                    return Html::a('<i class="fa fa-plus-circle"></i> 生成非项目订单', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat add_inquiry',
                    ]);
                },
            ]
        ])?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'        => [
            'firstPageLabel' => '首页',
            'prevPageLabel'  => '上一页',
            'nextPageLabel'  => '下一页',
            'lastPageLabel'  => '尾页',
        ],
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            [
                'class'         => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 10px;'],
                'header'        => '操作',
                'template'      => '{update}',
            ],
            'id',
            [
                'attribute' => 'goods_number',
                'format'         => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->goods_number, Url::to(['view', 'id' => $model->id]));
                }
            ],
            'goods_number_b',
            'original_company',
            'description',
            'description_en',
            'publish_tax_price',
            'publish_delivery_time',
            [
                'attribute' => 'material',
                'contentOptions'=>['style'=>'min-width: 100px;'],
            ],
            'original_company_remark',
            [
                'attribute' => 'is_process',
                'filter'    => Goods::$process,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'is_tz',
                'filter'    => Goods::$tz,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$tz[$model->is_tz];
                }
            ],
            [
                'attribute' => 'is_standard',
                'filter'    => Goods::$standard,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$standard[$model->is_standard];
                }
            ],
            [
                'attribute' => 'is_import',
                'filter'    => Goods::$import,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$import[$model->is_import];
                }
            ],
            [
                'attribute' => 'is_repair',
                'filter'    => Goods::$repair,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$repair[$model->is_repair];
                }
            ],
            [
                'attribute' => 'is_special',
                'filter'    => Goods::$special,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$special[$model->is_special];
                }
            ],
            [
                'attribute' => 'is_nameplate',
                'filter'    => Goods::$nameplate,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'filter'    => Goods::$emerg,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'is_assembly',
                'filter'    => Goods::$assembly,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$assembly[$model->is_assembly];
                }
            ],
            [
                'attribute' => 'part',
                'contentOptions'=>['style'=>'min-width: 100px;'],
            ],
            [
                'attribute' => 'technique_remark',
                'contentOptions'=>['style'=>'min-width: 100px;'],
            ],
            [
                'attribute' => 'remark',
                'contentOptions'=>['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'device_info',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 200px;'],
                'value'          => function($model){
                    $text = '';
                    if ($model->device_info) {
                        foreach (json_decode($model->device_info, true) as $key => $device) {
                            $text .= $key . ':' . $device . '<br/>';
                        }
                    }
                    return $text;
                }
            ],
            [
                'attribute' => 'is_inquiry',
                'label'     => '是否询价',
                'format'    => 'raw',
                'filter'    => ['0' => '否', '1' => '是'],
                'value'     => function($model){
                    if ($model->inquiry) {
                        return '是';
                    } else {
                        return '否';
                    }
                }
            ],
            [
                'attribute' => 'inquiry_number',
                'label'     => '询价数量',
                'format'    => 'raw',
                'value'     => function($model){
                    return $model->inquiryNumber;
                }
            ],
            [
                'attribute' => 'is_inquiry_better',
                'label'     => '是否优选',
                'format'    => 'raw',
                'filter'    => ['0' => '否', '1' => '是'],
                'value'     => function($model){
                    if ($model->inquiryBetter) {
                        $v = Yii::$app->request->get('GoodsSearch')['is_inquiry_better'];
                        if ($v === '0' || $v === '1') {
                            return $v ? '是' : '否';
                        } else {
                            return '是';
                        }
                    } else {
                        return '否';
                    }
                }
            ],
            [
                'attribute' => 'is_stock',
                'label'     => '是否有库存',
                'format'    => 'raw',
                'filter'    => ['0' => '否', '1' => '是'],
                'value'     => function($model){
                    if ($model->stockNumber) {
                        return '是';
                    } else {
                        return '否';
                    }
                }
            ],
            [
                'attribute' => 'stock_number',
                'label'     => '库存数量',
                'format'    => 'raw',
                'value'     => function($model){
                    if ($model->stock) {
                        return $model->stock->number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'suggest_number',
                'label'     => '建议库存',
                'format'    => 'raw',
                'value'     => function($model){
                    if ($model->stock) {
                        return $model->stock->suggest_number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'high_number',
                'label'     => '高储',
                'format'    => 'raw',
                'value'     => function($model){
                    if ($model->stock) {
                        return $model->stock->high_number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'low_number',
                'label'     => '低储',
                'format'    => 'raw',
                'value'     => function($model){
                    if ($model->stock) {
                        return $model->stock->low_number;
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'stock_low',
                'label'     => '库存不足',
                'format'    => 'raw',
                'filter'    => ['0' => '否', '1' => '是'],
                'value'     => function($model){
                    if ($model->stock) {
                        return ($model->stock->number < $model->stock->low_number) ? '是' : '否';
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'stock_high',
                'label'     => '库存超量',
                'format'    => 'raw',
                'filter'    => ['0' => '否', '1' => '是'],
                'value'     => function($model){
                    if ($model->stock) {
                        return ($model->stock->number > $model->stock->high_number) ? '是' : '否';
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[updated_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['updated_at'],
                ]),
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'GoodsSearch[created_at]',
                    'value' => Yii::$app->request->get('GoodsSearch')['created_at'],
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url : '?r=goods/upload',
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
                layer.msg(data.msg,{time:2000},function(){
                    window.location.reload();
                });
            }else{
                //失败提示
                layer.msg(data.msg,{icon:1});
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
            layer.msg('请选择要询价的商品', {icon:1});
            return;
        }
        $.ajax({
            type:"POST",
            url:"?r=goods/inquiry-order",
            data:{goods_ids:goods_ids},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    location.replace("?r=order/create&temp_id=" + res.data);
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    });
</script>
