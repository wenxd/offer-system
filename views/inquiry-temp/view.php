<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\InquiryTemp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inquiry Temps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="inquiry-temp-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'good_id',
            'supplier_id',
            'price',
            'tax_price',
            'tax_rate',
            'all_tax_price',
            'all_price',
            'number',
            'inquiry_datetime',
            'sort',
            'is_better',
            'is_newest',
            'is_priority',
            'is_deleted',
            'offer_date',
            'remark',
            'better_reason',
            'delivery_time:datetime',
            'admin_id',
            'order_id',
            'order_inquiry_id',
            'inquiry_goods_id',
            'updated_at',
            'created_at',
            'is_upload',
        ],
    ]) ?>

</div>
