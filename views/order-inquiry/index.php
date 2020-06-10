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
$userId   = Yii::$app->user->identity->id;
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
                'visible'   => !in_array($userId, $adminIds),
                'format'    => 'raw',
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
                'attribute' => 'inquiry_sn',
                'format'    => 'raw',
                'value'     => function($model) {
                    return Html::a($model->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $model->id]));
                }
            ],
            [
                'attribute' => 'end_date',
                'contentOptions'=>['style'=>'min-width: 150px', 'class' => 'end_date'],
                'filter'    => DateRangePicker::widget([
                    'name' => 'OrderInquirySearch[end_date]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['end_date'],
                ]),
                'value'     => function($model) {
                    return substr($model->end_date, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'OrderInquirySearch[created_at]',
                    'value' => Yii::$app->request->get('OrderInquirySearch')['created_at'],
                ]),
                'value'     => function($model) {
                    return substr($model->created_at, 0, 10);
                }
            ],
            [
                'attribute' => 'is_inquiry',
                'contentOptions'=>['class'=>'is_inquiry'],
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
        ],
    ]); ?>
    </div>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $('tr').each(function (i, e) {
        var end_date = $(e).find('.end_date').text();
        var inquiry = $(e).find('.is_inquiry').text();
        if (end_date && inquiry == '否') {
            end_date = new Date(end_date);
            end_date.setDate(end_date.getDate() - 1);
        }
    });
</script>
