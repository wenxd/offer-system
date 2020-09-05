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
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {index} {addson}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                },
                'addson' => function () {
                    return Html::a('<i class="fa fa-add"></i> 添加子零件', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat add_son',
                    ]);
                }
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
            'id',
            [
                'attribute'      => 'material_code',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'goods_number',
                'contentOptions' => ['style'=>'min-width: 100px;'],
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->goods_number, Url::to(['view', 'id' => $model->id]));
                }
            ],
            [
                'attribute'      => '子零件数量',
                'contentOptions' => ['style'=>'min-width: 100px;'],
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::input('text', 'son_number');
                }
            ],
            [
                'attribute'      => 'remark',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'original_company',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'goods_number_b',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'description',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'description_en',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'publish_price',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'factory_price',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'publish_delivery_time',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'material',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'unit',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'original_company_remark',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'import_mark',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute' => 'is_process',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$process,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'is_tz',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$tz,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$tz[$model->is_tz];
                }
            ],
            [
                'attribute' => 'is_standard',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$standard,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$standard[$model->is_standard];
                }
            ],
            [
                'attribute' => 'is_import',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$import,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$import[$model->is_import];
                }
            ],
            [
                'attribute' => 'is_repair',
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$nameplate,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$emerg,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'is_assembly',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'filter'    => Goods::$assembly,
                'value'     => function ($model, $key, $index, $column) {
                    return Goods::$assembly[$model->is_assembly];
                }
            ],
            [
                'attribute'      => 'part',
                'contentOptions' => ['style'=>'min-width: 100px;'],
            ],
            [
                'attribute'      => 'device_info',
                'format'         => 'raw',
                'contentOptions' => ['style'=>'min-width: 200px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'label'     => '询价条目',
                'contentOptions' => ['style'=>'min-width: 80px;'],
                'format'    => 'raw',
                'value'     => function($model){
                    return $model->inquiryNumber;
                }
            ],
            [
                'attribute' => 'is_inquiry_better',
                'label'     => '是否优选',
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
                'contentOptions' => ['style'=>'min-width: 80px;'],
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
