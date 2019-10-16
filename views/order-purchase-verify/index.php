<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\OrderPayment;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderPaymentVerifySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;

$this->title = '采购审核列表';
$this->params['breadcrumbs'][] = $this->title;
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
                'attribute' => 'payment_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->payment_sn, Url::to(['order-payment/detail', 'id' => $model->id]));
                }
            ],
            [
                'attribute' => 'order_purchase_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
                }
            ],
            [
                'attribute' => 'admin_id',
                'label'     => '采购员',
                'filter'    => $admins,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->admin) {
                        return $model->admin->username;
                    }
                }
            ],
            [
                'attribute' => 'is_verify',
                'label'     => '审核',
                'format'    => 'raw',
                'filter'    => OrderPayment::$verify,
                'value'     => function ($model, $key, $index, $column) {
                    return OrderPayment::$verify[$model->is_verify];
                }
            ],
            [
                'attribute'      => '操作',
                'format'         => 'raw',
                'value'          => function ($model, $key, $index, $column){
                    $html = Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['detail', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-xs btn-flat',
                    ]);
                    if (!$model->is_verify) {
                        $html .= Html::a('<i class="fa fa-eye"></i> 审核', Url::to(['detail', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-xs btn-flat',
                        ]);
                    } else {
                        if (!$model->is_agreement) {
                            $html .= Html::a('<i class="fa fa-plus"></i> 生成支出合同', Url::to(['complete', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-primary btn-xs btn-flat',
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
