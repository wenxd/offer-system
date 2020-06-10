<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agreement Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="agreement-goods-view">

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
            'order_id',
            'order_agreement_id',
            'order_agreement_sn',
            'order_quote_id',
            'order_quote_sn',
            'serial',
            'goods_id',
            'type',
            'relevance_id',
            'tax_rate',
            'price',
            'tax_price',
            'all_price',
            'all_tax_price',
            'quote_price',
            'quote_tax_price',
            'quote_all_price',
            'quote_all_tax_price',
            'number',
            'is_agreement',
            'agreement_sn',
            'purchase_date',
            'agreement_date',
            'is_deleted',
            'updated_at',
            'created_at',
            'inquiry_admin_id',
            'is_out',
        ],
    ]) ?>

</div>
