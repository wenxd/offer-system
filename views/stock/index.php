<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Goods;
use app\models\Stock;
use app\models\AuthAssignment;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库存管理列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '库管员'])->all();
$stock_adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$use_admin = AuthAssignment::find()->where(['item_name' => '库管员B'])->all();
$stock_b_adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId    = Yii::$app->user->identity->id;
$isShow    = in_array($userId, array_merge($stock_adminIds, $stock_b_adminIds));

if ($isShow) {
    $func = '{index}';
    if (in_array($userId, $stock_adminIds)) {
        $func = '{gen} {index}';
    }
    $operate = '{view}';
} else {
    $func = '{gen} {delete} {stock_in} {stock_out} {download} {index}';
    $operate = '{view} {update}';
}

?>
<style>
    .number {
        color : red;
    }
</style>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => $func,
            'buttons' => [
                'gen' => function () {
                    return Html::a('<i class="fa fa-chrome"></i> 批量移库', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat stock-move',
                    ]);
                },
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 导出', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
                    ]);
                },
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
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
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'contentOptions' => ['style'=>'min-width: 100px;'],
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($isShow) {
                    if ($model->goods) {
                        if ($isShow) {
                            return $model->goods->goods_number . ' ' . $model->goods->material_code;
                        } else {
                            return Html::a($model->goods->goods_number . ' ' . $model->goods->material_code, Url::to(['goods/view', 'id' => $model->goods->id]));
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'description',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description_en',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'description_en',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description_en;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'material_code',
                'format'    => 'raw',
                'label'     => '设备类别',
                'filter'    => Html::activeTextInput($searchModel, 'material_code', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($isShow) {
                    if ($model->goods) {
                        return $model->goods->material_code;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'part',
                'format'    => 'raw',
                'label'     => '所属部件',
                'filter'    => Html::activeTextInput($searchModel, 'part', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($isShow) {
                    if ($model->goods) {
                        return $model->goods->part;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'device_info',
                'format'    => 'raw',
                'label'     => '设备信息',
                'filter'    => Html::activeTextInput($searchModel, 'device_info', ['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($isShow) {
                    if ($model->goods) {
                        $text = '';
                        if ($model->goods->device_info) {
                            foreach (json_decode($model->goods->device_info, true) as $key => $device) {
                                $text .= $key . ':' . $device . '<br/>';
                            }
                        }
                        return $text;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'tax_rate',
                'visible'   => !$isShow,
            ],
            [
                'attribute' => 'price',
                'visible'   => !$isShow,
            ],
            [
                'attribute' => 'tax_price',
                'visible'   => !$isShow,
            ],
            [
                'attribute' => '含税总价',
                'visible'   => !$isShow,
                'value'     => function ($model, $key, $index, $column) {
                    return $model->number * $model->tax_price;
                }
            ],
            'position',
            [
                'attribute' => 'number',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->number > $model->high_number || $model->number < $model->low_number ) {
                        return '<span class="number">' . $model->number . '</span>';
                    } else {
                        return $model->number;
                    }
                }
            ],
            'suggest_number',
            'high_number',
            'low_number',
            [
                'attribute' => 'is_zero',
                'contentOptions'=>['style'=>'min-width: 60px;'],
                'filter'    => Stock::$zero,
                'value'     => function($model){
                    return $model->number ? '是' : '否';
                }
            ],
            [
                'attribute' => 'stock_low',
                'contentOptions'=>['style'=>'min-width: 60px;'],
                'filter'    => ['0' => '否', '1' => '是'],
                'label'     => '库存不足',
                'value'     => function($model){
                    return ($model->number < $model->low_number) ? '是' : '否';
                }
            ],
            [
                'attribute' => 'stock_high',
                'contentOptions'=>['style'=>'min-width: 60px;'],
                'filter'    => ['0' => '否', '1' => '是'],
                'label'     => '库存超量',
                'value'     => function($model){
                    return ($model->number > $model->high_number) ? '是' : '否';
                }
            ],
            [
                'class' => ActionColumn::className(),
                'visible'   => !$isShow,
                'contentOptions'=>['style'=>'min-width: 130px;'],
                'header' => '操作',
                'template' => $operate,
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
        <?php if (!$isShow) :?>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>库存总金额</th>
                    <th><?=Stock::getAllMoney();?></th>
                </tr>
            </thead>
        </table>
        <?php endif;?>
    </div>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    var list = [];
    $(document).ready(function () {
        $('.stock-move').click(function (event) {
            var flag = true;
            $("input[name='selection[]']").each(function (i, e) {
                if ($(e).is(':checked')) {
                    flag = false;
                    list.push($(e).val());
                }
            });
            if (flag) {
                layer.msg('请选择要移库的记录', {time:2000});
                return false;
            }

            layer.open({
                type: 1,
                title: '修改地址',
                skin: 'layui-layer-rim', //加上边框
                area: ['500px', '240px'], //宽高
                content: '<form class="form-horizontal">\n' +
                '  <div class="form-group">\n' +
                '    <label for="newAddress" class="col-sm-2 control-label">新库地址</label>\n' +
                '    <div class="col-sm-10">\n' +
                '      <input type="text" class="form-control" id="newAddress" placeholder="新库地址">\n' +
                '    </div>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <div class="col-sm-offset-2 col-sm-10">\n' +
                '      <a class="btn btn-default" href="javascript:void(0)" onclick="sure()">确定</a>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</form>'
            });
        });
    });

    function sure() {
        var address = $('#newAddress').val();
        if (!address) {
            layer.msg('请输入新库地址', {time:2000});
            return false;
        }
        $.ajax({
            type:"post",
            url:"?r=stock/address",
            data:{list:list, address:address},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200) {
                    window.location.reload();
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>
