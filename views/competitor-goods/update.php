<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompetitorGoods */

$this->title = '更新竞争对手价格关系';
$this->params['breadcrumbs'][] = ['label' => '竞争对手价格列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competitor-goods-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
