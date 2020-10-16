<?php

use app\extend\widgets\Bar;
use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\Helper;
use app\models\InquiryGoods;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InquiryGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '澄清记录列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');

$userId = Yii::$app->user->identity->id;
?>
<div class="box">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{index}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ]) ?>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'clarify_id',
            [
                'attribute' => 'goods_number',
                'format' => 'raw',
                'label' => '零件号',
                'visible' => !in_array($userId, $adminIds),
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'goods_number', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number . ' ' . $model->goods->material_code, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'goods_number_b',
                'format' => 'raw',
                'label' => '厂家号',
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'goods_number_b', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if ($model->goods) {
                        if (in_array($userId, $adminIds)) {
                            return $model->goods->goods_number_b;
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
                'format' => 'raw',
                'label' => '中文描述',
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'description', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'description_en',
                'format' => 'raw',
                'label' => '英文描述',
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'description_en', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->description_en;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'original_company',
                'format' => 'raw',
                'label' => '原厂家',
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'original_company', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->original_company;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'admin_id',
                'label' => '询价员',
                'filter' => Helper::getAdminList(['系统管理员', '询价员']),
                'filterType' => GridView::FILTER_SELECT2,
                'value' => function ($model, $key, $index, $column) {
                    $adminList = Helper::getAdminList(['系统管理员', '询价员']);
                    return (isset($adminList[$model->admin_id]) ? $adminList[$model->admin_id] : '');
                }
            ],
            [
                'attribute' => 'inquiry_sn',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $model->orderInquiry->id]));
                }
            ],
            [
                'attribute' => 'order_sn',
                'format' => 'raw',
                'label' => '订单号',
                'visible' => !in_array($userId, $adminIds),
                'contentOptions' => ['style' => 'min-width: 100px;'],
                'filter' => Html::activeTextInput($searchModel, 'order_sn', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            'reason',
            [
                'attribute' => 'clarify',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return $model->clarify ?? '';
                }
            ],
            [
                'attribute' => 'is_inquiry',
                'format' => 'raw',
                'filter' => InquiryGoods::$Inquiry,
                'value' => function ($model, $key, $index, $column) {
                    return InquiryGoods::$Inquiry[$model->is_inquiry];
                }
            ],
            [
                'attribute' => 'not_result_at',
                'contentOptions' => ['style' => 'min-width: 150px;'],
                'filter' => DateRangePicker::widget([
                    'name' => 'InquiryGoodsSearch[not_result_at]',
                    'value' => Yii::$app->request->get('InquiryGoodsSearch')['not_result_at'] ?? '',
                ]),
                'value' => function ($model) {
                    return substr($model->not_result_at, 0, 10);
                }
            ],
            [
                'attribute' => '操作',
                'format' => 'raw',
                'visible' => !in_array($userId, $adminIds),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->clarify) {
                        return '';
                    }
                    return Html::button('澄清', ['class' => 'btn btn-success btn-xs', 'onclick' => "clarify({$model->clarify_id})"]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script>
    function clarify(id) {
        console.log(id);
        layer.open({
            type: 1,
            title: '澄清回复',
            skin: 'layui-layer-rim', //加上边框
            area: ['500px', '240px'], //宽高
            content: '<form class="form-horizontal">\n' +
                '  <div class="form-group">\n' +
                '    <label for="reason" class="col-sm-2 control-label">回复</label>\n' +
                '    <div class="col-sm-10">\n' +
                '      <input type="text" class="form-control" id="reason">\n' +
                '    </div>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <div class="col-sm-offset-2 col-sm-10">\n' +
                '      <a class="btn btn-default" href="javascript:void(0)" onclick="sure(' + id + ')">确定</a>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</form>'
        });
    }
    function sure(id) {
        var reason = $('#reason').val();
        if (!reason) {
            layer.msg('请输入原因', {time:2000});
            return false;
        }
        $.ajax({
            type:"post",
            url:"?r=inquiry-goods/clarify",
            data:{id:id, reason:reason},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200) {
                    layer.msg(res.msg, {time:2000});
                    window.location.reload();
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>
