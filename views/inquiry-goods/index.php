<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\Helper;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquiryGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '寻不出记录列表';
$this->params['breadcrumbs'][] = $this->title;
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
                'attribute'  => 'admin_id',
                'label'      => '询价员',
                'filter'     => Helper::getAdminList(['系统管理员', '询价员']),
                'filterType' => GridView::FILTER_SELECT2,
                'value'     => function ($model, $key, $index, $column) {
                    $adminList = Helper::getAdminList(['系统管理员', '询价员']);
                    return (isset($adminList[$model->admin_id]) ? $adminList[$model->admin_id] : '');
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
