<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AgreementStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agreement Stocks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agreement-stock-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Agreement Stock', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'order_agreement_id',
            'order_agreement_sn',
            'order_purchase_id',
            //'order_purchase_sn',
            //'order_payment_id',
            //'order_payment_sn',
            //'goods_id',
            //'price',
            //'tax_price',
            //'use_number',
            //'all_price',
            //'all_tax_price',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
