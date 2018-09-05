<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = '编辑询价单' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '询价单列表', 'url' => ['detail', 'id' => $model->id]];
?>
<div class="order-inquiry-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
