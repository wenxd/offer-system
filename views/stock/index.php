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
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId    = Yii::$app->user->identity->id;
$isShow    = in_array($userId, $adminIds);
?>
<style>
    .number {
        color : red;
    }
</style>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {gen} {delete}',
            'buttons' => [
                'gen' => function () {
                    return Html::a('<i class="fa fa-chrome"></i> 批量移库', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat stock-move',
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
            'good_id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'StockSearch[updated_at]',
                    'value' => Yii::$app->request->get('StockSearch')['updated_at'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'StockSearch[created_at]',
                    'value' => Yii::$app->request->get('StockSearch')['created_at'],
                ])
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
                'class' => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 200px;'],
                'header' => '操作',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
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
