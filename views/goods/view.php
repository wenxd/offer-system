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
            'original_company',
            'original_company_remark',
            [
                'attribute' => 'is_process',
                'value'     => function ($model) {
                    return Goods::$process[$model->is_process];
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
                'attribute' => 'is_nameplate',
                'value'     => function ($model) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'value'     => function ($model) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'nameplate_img_id',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->nameplate_img_url, ['width' => 300]);
                }
            ],
            'technique_remark',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
