<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */

$this->title = '零件详情';
$this->params['breadcrumbs'][] = ['label' => '零件管理列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_number',
            'goods_number_b',
            'description',
            'description_en',
            'publish_tax_price',
            'publish_delivery_time',
            'material',
            'original_company',
            'original_company_remark',
            'estimate_publish_price',
            'material_code',
            [
                'attribute' => 'is_process',
                'value'     => function ($model) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'is_tz',
                'value'     => function ($model) {
                    return Goods::$tz[$model->is_tz];
                }
            ],
            [
                'attribute' => 'is_standard',
                'value'     => function ($model) {
                    return Goods::$standard[$model->is_standard];
                }
            ],
            [
                'attribute' => 'is_import',
                'value'     => function ($model) {
                    return Goods::$import[$model->is_import];
                }
            ],
            [
                'attribute' => 'is_repair',
                'value'     => function ($model) {
                    return Goods::$repair[$model->is_repair];
                }
            ],
            [
                'attribute' => 'img_id',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->img_url, ['width' => 300]);
                }
            ],
            [
                'attribute' => 'is_special',
                'value'     => function ($model) {
                    return Goods::$special[$model->is_special];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'value'     => function ($model) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'is_assembly',
                'value'     => function ($model) {
                    return Goods::$assembly[$model->is_assembly];
                }
            ],
            [
                'attribute' => 'is_nameplate',
                'value'     => function ($model) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'nameplate_img_id',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->nameplate_img_url, ['width' => 300]);
                }
            ],
            'part',
            'technique_remark',
            'remark',
            [
                'attribute'      => 'device_info',
                'format'         => 'raw',
                'value'          => function($model){
                    $text = '';
                    if ($model->device_info) {
                        foreach (json_decode($model->device_info, true) as $key => $device) {
                            $text .= $key . ':' . $device . '<br/>';
                        }
                    }
                    return $text;
                }
            ],
            [
                'attribute' => 'created_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
        ],
    ]) ?>

</div>
