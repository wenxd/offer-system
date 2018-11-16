<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Agreements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-agreement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Order Agreement', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'agreement_sn',
            'order_id',
            'order_quote_id',
            'order_quote_sn',
            //'goods_info',
            //'agreement_date',
            //'is_quote',
            //'admin_id',
            //'is_deleted',
            //'updated_at',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
