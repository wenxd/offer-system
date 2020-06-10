<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use app\models\AgreementGoodsSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AgreementGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收入合同记录列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
            'quote_tax_price',
            'number',
            'tax_rate',
            'quote_all_tax_price',
            'quote_delivery_time',
            [
                'attribute'  => 'customer_id',
                'label'      => '客户名称',
                'filter'     => \app\models\Customer::getAllDropDown(),
                'filterType' => GridView::FILTER_SELECT2,
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->order) {
                        if($model->order->customer) {
                            return $model->order->customer->name;
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'sign_date',
                'label'     => '签订时间',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'AgreementGoodsSearch[sign_date]',
                    'value' => Yii::$app->request->get('AgreementGoodsSearch')['sign_date'],
                ]),
                'value'     => function ($model, $key, $index, $column) {
                    return substr($model->orderAgreement->sign_date, 0, 10);
                }
            ],
            [
                'attribute' => 'order_agreement_sn',
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a($model->order_agreement_sn, Url::to(['order-agreement/view', 'id' => $model->order_agreement_id]));
                }
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
