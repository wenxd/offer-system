<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CompetitorGoods */

$this->title = '创建竞争对手零件信息';
$this->params['breadcrumbs'][] = ['label' => '竞争对手与零件列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competitor-goods-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
