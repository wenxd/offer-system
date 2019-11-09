<?php

use app\extend\widgets\Bar;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Order;
use app\models\Customer;
use app\models\StockLog;
use app\models\SystemConfig;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StockOutLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '出库记录';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['库管员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
?>
<div class="box table-responsive">
    <?php if (!in_array($userId, $adminIds)):?>
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{download}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 导出数据', 'javascript:void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat output',
                    ]);
                }
            ]
        ])?>
    </div>
    <?php endif;?>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                [
                    'attribute' => 'order_sn',
                    'label'     => '订单号',
                    'format'    => 'raw',
                    'visible'   => !in_array($userId, $adminIds),
                    'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->order) {
                            return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'agreement_sn',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                        if (in_array($userId, $adminIds)) {
                            return $model->agreement_sn;
                        } else {
                            return Html::a($model->agreement_sn, Url::to(['order-agreement/view', 'id' => $model->order_agreement_id]));
                        }
                    }
                ],
                [
                    'attribute' => 'goods_number',
                    'format'    => 'raw',
                    'label'     => '零件号',
                    'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->goods) {
                            return $model->goods->goods_number;
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'number',
                    'label'     => '出库数量',
                    'visible'   => !in_array($userId, $adminIds),
                ],
                [
                    'attribute' => 'price',
                    'label'     => '价格',
                    'visible'   => !in_array($userId, $adminIds),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->stock) {
                            return $model->stock->price;
                        }
                    }
                ],
                [
                    'attribute' => 'admin_id',
                    'label'     => '采购员',
                    'filter'    => \app\models\Helper::getAdminList(['系统管理员', '采购员']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->admin) {
                            return $model->admin->username;
                        }
                    }
                ],
                [
                    'attribute' => 'operate_time',
                    'format'    => 'raw',
                    'label'     => '出库时间',
                    'filter'    => DateRangePicker::widget([
                        'name' => 'StockInLogSearch[operate_time]',
                        'value' => Yii::$app->request->get('StockInLogSearch')['operate_time'],
                    ])
                ],
                [
                    'attribute' => 'is_manual',
                    'filter'    => StockLog::$manual,
                    'value'     => function ($model, $key, $index, $column) {
                        return $model->is_manual ? StockLog::$manual[$model->is_manual] : '否';
                    }
                ],
                [
                    'attribute' => 'order_type',
                    'label'     => '订单类型',
                    'filter'    => Order::$orderType,
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->order) {
                            return Order::$orderType[$model->order->order_type];
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'customer_id',
                    'filter'    => Customer::getSelectDropDown(),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->customer) {
                            return $model->customer->name;
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'region',
                    'value'     => function ($model, $key, $index, $column) {
                        return  $model->region;
                    }
                ],
                [
                    'attribute' => 'plat_name',
                    'value'     => function ($model, $key, $index, $column) {
                        return  $model->plat_name;
                    }
                ],
                [
                    'attribute' => 'direction',
                    'value'     => function ($model, $key, $index, $column) {
                        return  $model->direction;
                    }
                ],
                'remark',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.output').click(function (e) {
            var url = decodeURI(window.location.search);
            var theRequest = new Object();
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                strs = str.split("&");
                for(var i = 0; i < strs.length; i ++) {
                    theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                }
            }
            var parameter = '';
            for(var j in theRequest){
                if (j != 'r') {
                    parameter += j + '=' + theRequest[j] + '&';
                }
            }
            window.location.href = '?r=stock-out-log/download&' + parameter;
        });
    });
</script>
