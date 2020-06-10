<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = '创建库存';
$this->params['breadcrumbs'][] = ['label' => '库存列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
