<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\Admin;
use app\models\Customer;
use app\models\AuthAssignment;
use app\models\OrderQuote;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报价单列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '报价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;

?>
<div class="box table-responsive">
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'      => 'customer_id',
                'label'          => '客户名称',
                'contentOptions' =>['style'=>'min-width: 150px;'],
                'format'         => 'raw',
                'filter'         => Customer::getAllDropDown(),
                'filterType'     => GridView::FILTER_SELECT2,
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->customer) {
                        return $model->customer->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'order_final_sn',
                'format'    => 'raw',
                'label'     => '成本单号',
                'visible'   => !in_array($userId, $adminIds),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->orderFinal) {
                        return Html::a($model->orderFinal->final_sn, Url::to(['order-final/view', 'id' => $model->order_final_id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'quote_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->quote_sn, Url::to(['order-quote/view', 'id' => $model->id]));
                }
            ],
            [
                'attribute' => 'order_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) use($userId, $adminIds){
                    if ($model->order) {
                        if (in_array($userId, $adminIds)) {
                            return $model->order->order_sn;
                        } else {
                            return Html::a($model->order->order_sn, Url::to(['order/detail', 'id' => $model->order_id]));
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'quote_only_one',
                'label'     => '生成收入合同',
                'format'    => 'raw',
                'filter'    => ['1' => '否', '2' => '是'],
                'value'     => function ($model, $key, $index, $column) {
                    return $model->quote_only_one == 1 ? '否' : '是';
                }
            ],
            [
                'attribute' => 'is_quote',
                'format'    => 'raw',
                'filter'    => OrderQuote::$quote,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderQuote::$quote[$model->is_quote];
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderQuoteSearch[created_at]',
                    'value' => Yii::$app->request->get('OrderQuoteSearch')['created_at'],
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute' => 'quote_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderQuoteSearch[quote_at]',
                    'value' => Yii::$app->request->get('OrderQuoteSearch')['quote_at'],
                ]),
                'value'     => function($model){
                    return substr($model->quote_at, 0, 10);
                }
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '报价员',
                'filter'    => $admins,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    }
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'value'          => function ($model, $key, $index, $column){
                    $html = Html::a('<i class="fa fa-download"></i> 导出报价单', Url::to(['download', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-xs btn-flat',
                    ]);
                    if ($model->is_send) {
                        $html .= ' ' . Html::button('<i class="fa"></i> 已发送', [
                                'data-pjax' => '0',
                                'class' => 'btn btn-info btn-xs btn-flat',
                            ]);
                    } else {
                        $html .= ' ' . Html::a('<i class="fa fa-send"></i> 发送报价单', Url::to(['send', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-warning btn-xs btn-flat',
                            ]);
                    }
                    if ($model->quote_only_one) {
                        if ($model->quote_only_one == 1) {
                            $html .= ' ' . Html::a('<i class="fa fa-eye"></i> 生成收入合同', Url::to(['detail', 'id' => $model['id']]), [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-danger btn-xs btn-flat',
                                ]);
                        } else {
                            $html .= ' ' . Html::button('<i class="fa"></i> 已生成收入合同', [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-success btn-xs btn-flat',
                                ]);
                        }
                    }
                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
