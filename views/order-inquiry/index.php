<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\OrderInquiry;
use app\models\Admin;
use app\models\AuthAssignment;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderInquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价单列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}

?>
<div class="box table-responsive">
    <div class="box-body">
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
            'id',
            [
                'attribute' => 'order_sn',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'order_sn',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        return Html::a($model->order->order_sn, Url::to(['order/view', 'id' => $model->order_id]));
                    } else {
                        return '';
                    }
                }
            ],
            'inquiry_sn',
            [
                'attribute' => 'end_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderInquirySearch[end_date]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['end_date'],
                ])
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderInquirySearch[created_at]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['created_at'],
                ])
            ],
            [
                'attribute' => 'is_inquiry',
                'format'    => 'raw',
                'filter'    => OrderInquiry::$Inquiry,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderInquiry::$Inquiry[$model->is_inquiry];
                }
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '询价员',
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
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'value'          => function ($model, $key, $index, $column){
                    return Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['view', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-xs btn-flat',
                        ]);
                }
            ],
        ],
    ]); ?>
    </div>
</div>
