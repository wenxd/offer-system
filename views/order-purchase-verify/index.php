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
$userId   = Yii::$app->user->identity->id;
// 获取超管
$admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
$admins  = ArrayHelper::getColumn($admin, 'user_id');
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
                'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if (in_array($userId, $adminIds) && $model->is_complete) {
                        return $model->payment_sn;
                    } else {
                        return Html::a($model->payment_sn, Url::to(['view', 'id' => $model->id]));
                    }
                }
            ],
            [
                'attribute' => 'order_purchase_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if (in_array($userId, $adminIds)) {
                        return $model->order_purchase_sn;
                    } else {
                        return Html::a($model->order_purchase_sn, Url::to(['order-purchase/detail', 'id' => $model->order_purchase_id]));
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
                'value'          => function ($model, $key, $index, $column) use ($userId, $admins){
                    $html = '';
//                    if (!in_array($userId, $adminIds)) {
//
//                    }
                    $html .= Html::a('<i class="fa fa-eye"></i> 查看', Url::to(['view', 'id' => $model['id']]), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-xs btn-flat',
                    ]);
//                    if (in_array($userId, $adminIds)) {
//                        if ($model->purchase_status == 1 && !$model->is_agreement) {
//                            $html .= Html::a('<i class="fa fa-plus"></i> 生成支出合同', Url::to(['complete', 'id' => $model['id']]), [
//                                'data-pjax' => '0',
//                                'class' => 'btn btn-primary btn-xs btn-flat',
//                            ]);
//                        }
//                    } else {
                        if (!$model->is_verify && in_array($userId, $admins)) {
                            if (!$model->order->order_type) {
                                $html .= Html::a('<i class="fa fa-eye"></i> 审核', Url::to(['detail', 'id' => $model['id']]), [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-info btn-xs btn-flat',
                                ]);
                            } else {
                                $res = OrderPayment::isConfirm($model->id);
                                if (!$res) {
                                    $html .= Html::a('<i class="fa fa-eye"></i> 审核', Url::to(['detail', 'id' => $model['id']]), [
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-info btn-xs btn-flat',
                                    ]);
                                }
                            }
                        } else {

                        }
                    if (!$model->is_agreement && $model->is_verify) {
                        if ($model->is_contract) {
                            $html .= Html::a('<i class="fa fa-plus"></i> 生成支出合同', Url::to(['complete', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-primary btn-xs btn-flat',
                            ]);
                        } else {
                            $html .= Html::a('<i class="fa fa-plus"></i> 生成杂项支出合同', Url::to(['complete', 'id' => $model['id']]), [
                                'data-pjax' => '0',
                                'class' => 'btn btn-primary btn-xs btn-flat',
                            ]);
                        }
                    }
//                    }

                    return $html;
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
