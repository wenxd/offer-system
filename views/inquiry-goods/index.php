<?php

use app\models\Admin;
use app\models\AuthAssignment;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquiryGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '寻不出记录列表';
$this->params['breadcrumbs'][] = $this->title;


$use_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '询价员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
?>
<div class="box table-responsive">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'      => 'goods_number',
                'format'         => 'raw',
                'label'          => '零件号',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => 'goods_number_b',
                'format'         => 'raw',
                'label'          => '厂家号',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'goods_number_b',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number_b, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'inquiry_sn',
                'format'    => 'raw',
                'value'     => function($model) {
                    return Html::a($model->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $model->orderInquiry->id]));
                }
            ],
            [
                'attribute' => 'not_result_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquiryGoodsSearch[not_result_at]',
                    'value' => Yii::$app->request->get('InquiryGoodsSearch')['not_result_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->not_result_at, 0, 10);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
