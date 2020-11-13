<?php

use app\extend\widgets\Bar;
use app\models\Helper;
use app\models\Supplier;
use app\models\OrderPayment;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use app\models\AuthAssignment;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '杂项支出合同管理';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['收款财务', '付款财务', '系统管理员']])->all();
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');

$use_admin = AuthAssignment::find()->where(['item_name' => ['采购员']])->all();
$purchaseAdminIds = ArrayHelper::getColumn($use_admin, 'user_id');

$userId = Yii::$app->user->identity->id;
?>
<div class="box">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{index}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index2']), [
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
            'id',
            [
                'attribute' => 'payment_sn',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($userId, $purchaseAdminIds) {
                    return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->id]));
                    if (in_array($userId, $purchaseAdminIds) && $model->is_complete) {
                        return $model->payment_sn;
                    } else {
                    }
                }
            ],

            [
                'attribute' => 'agreement_at',
                'contentOptions' => ['style' => 'min-width: 150px;'],
                'filter' => DateRangePicker::widget([
                    'name' => 'OrderPaymentSearch[agreement_at]',
                    'value' => Yii::$app->request->get('OrderPaymentSearch')['agreement_at'],
                ]),
                'value' => function ($model, $key, $index, $column) {
                    return substr($model->agreement_at, 0, 10);
                }
            ],
            [
                'attribute' => 'is_reim',
                'filter' => OrderPayment::$complete,
                'value' => function ($model, $key, $index, $column) {
                    return OrderPayment::$complete[$model['is_reim']] ?? '否';
                }
            ],

            'payment_price',

            [
                'attribute' => 'reim_price',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $text = '';
                    foreach (json_decode($model->reim_info, true) ?? [] as $item) {
                        $text .= $item['reim_price'] . "<br />";
                    }
                    return $text;
                }
            ],
            [
                'attribute' => 'reim_ratio',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $text = '';
                    foreach (json_decode($model->reim_info, true) ?? [] as $item) {
                        $text .= $item['reim_ratio'] . "<br />";
                    }
                    return $text;
                }
            ],
            [
                'attribute' => 'reim_time',
                'format' => 'raw',
                'contentOptions' => ['style' => 'min-width: 150px;'],
                'value' => function ($model, $key, $index, $column) {
                    $text = '';
                    foreach (json_decode($model->reim_info, true) ?? [] as $item) {
                        $text .= date('Y-m-d H:i:s', $item['reim_time']) . "<br />";
                    }
                    return $text;
                }
            ],

            [
                'attribute' => 'order_sn',
                'label' => '订单编号',
                'format' => 'raw',
//                'visible'   => !in_array($userId, array_merge($adminIds, $purchaseAdminIds)),
                'visible' => in_array($userId, $adminIds),
                'filter' => Html::activeTextInput($searchModel, 'order_sn', ['class' => 'form-control']),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'order_purchase_sn',
                'format' => 'raw',
                'visible' => in_array($userId, $adminIds),
                'value' => function ($model, $key, $index, $column) use ($userId, $purchaseAdminIds) {
                    if (in_array($userId, $purchaseAdminIds) && $model->is_complete) {
                        return $model->order_purchase_sn;
                    } else {
                        return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                    }
                }
            ],

            [
                'attribute' => 'supplier_id',
                'filter' => Supplier::getAllDropDown(),
                'filterType' => GridView::FILTER_SELECT2,
                'value' => function ($model, $key, $index, $column) {
                    if ($model->supplier) {
                        return $model->supplier->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'admin_id',
                'label' => '采购员',
                'filter' => in_array($userId, $purchaseAdminIds) ? [$userId => Yii::$app->user->identity->username] : Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员']),
                'value' => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员'])[$model->admin_id])) {
                        return Helper::getAdminList(['系统管理员', '订单管理员', '采购员', '询价员'])[$model->admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'financial_admin_id',
                'label' => '财务',
                'filter' => Helper::getAdminList(['系统管理员', '订单管理员', '收款财务']),
                'value' => function ($model, $key, $index, $column) {
                    if (isset(Helper::getAdminList(['系统管理员', '订单管理员', '收款财务'])[$model->financial_admin_id])) {
                        return Helper::getAdminList(['系统管理员', '订单管理员', '收款财务'])[$model->financial_admin_id];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => '操作',
                'format' => 'raw',
//                'visible'        => !in_array($userId, $adminIds),
                'value' => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    $html = '';
                    if (in_array($userId, $adminIds) && $model->is_reim == 0) {
                        // 计算可报销金额
                        $max_price = $model->payment_price;
                        foreach (json_decode($model->reim_info, true) ?? [] as $item) {
                            $max_price = bcsub($max_price, $item['reim_price'], 2);
                        }
                        $html .= Html::button('报销', ['class' => 'btn btn-success btn-xs  btn-flat', 'onclick' => "add_reim({$model->payment_price}, {$max_price}, {$model->id})",]);
//                        $html .= Html::a('<i class="fa fa-eye"></i> 报销', Url::to(['reim', 'id' => $model['id']]), [
//                            'data-pjax' => '0',
//                            'class' => 'btn btn-success btn-xs btn-flat',
//                        ]);
                    }
                    $html .= Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-xs btn-flat',
                    ]);
                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script>
    // 报销弹窗
    function add_reim(payment_price, max_price, order_id) {
        var ratio = toPercent(max_price / payment_price);
        layer.open({
            type: 1,
            title: '报销',
            skin: 'layui-layer-rim', //加上边框
            area: ['500px', '400px'], //宽高
            content: '<form class="form-horizontal">\n' +
                '  <div class="form-group">\n' +
                '    <label for="reason" class="col-sm-4 control-label">合同金额</label>\n' +
                '    <div class="col-sm-6 payment_price">\n' + payment_price + '</div>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <label for="reason" class="col-sm-4 control-label">可报销金额</label>\n' +
                '    <div class="col-sm-6 sure_price">\n' + max_price + '</div>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <label for="reason" class="col-sm-4 control-label">金额</label>\n' +
                '    <div class="col-sm-6">\n' +
                '      <input type="number" class="form-control reim_price" oninput="exit_reim()" value="' + max_price + '" max="' + max_price + '" >\n' +
                '    </div>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <label for="reason" class="col-sm-4 control-label">比例</label>\n' +
                '    <div class="col-sm-6 ratio" order_id=' + order_id + '>' + ratio + '</div>\n' +
                '  <div class="form-group">\n' +
                '    <div class="col-sm-offset-2 col-sm-10">\n' +
                '    <label for="reason" class="col-sm-4 control-label"></label>\n' +
                '      <a class="btn btn-success btn_reim" href="javascript:void(0)" onclick="add_reim_success()">确定</a>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</form>'
        });
    }

    // 修改报销金额
    function exit_reim() {
        var reim_price = parseFloat($('.reim_price').val());
        reim_price = Math.floor(reim_price * 100) / 100;
        var sure_price = parseFloat($('.sure_price').text());
        // 判断
        if (reim_price <= 0) {
            layer.msg('报销金额必须大于0元', {time: 2000});
            reim_price = sure_price;
        }
        if (reim_price > sure_price) {
            layer.msg('报销金额不能大于可报销金额', {time: 2000});
            reim_price = sure_price;
        }
        $('.reim_price').val(reim_price);
        $('.ratio').text(toPercent(reim_price / sure_price));
    }

    // 报销
    function add_reim_success() {
        var reim_price = parseFloat($('.reim_price').val());
        var payment_price = parseFloat($('.payment_price').text());
        var ratio_price = $('.ratio').text();
        var order_id = $('.ratio').attr('order_id');
        // $(".btn_reim").attr("disabled", true).addClass("disabled");
        $.ajax({
            type: "post",
            url: "?r=order-payment/reim",
            data: {reim_price: reim_price, reim_ratio: ratio_price, order_id: order_id, payment_price: payment_price},
            dataType: 'JSON',
            success: function (res) {
                if (res && res.code == 200) {
                    layer.msg(res.msg, {time: 2000});
                    window.location.reload();
                } else {
                    $(".btn_sure").removeAttr("disabled").removeClass("disabled");
                    layer.msg(res.msg, {time: 2000});
                    return false;
                }
            }
        });
    }

    function toPercent(point) {
        var str = Number(point * 100).toFixed(2);
        str += "%";
        return str;
    }
</script>
