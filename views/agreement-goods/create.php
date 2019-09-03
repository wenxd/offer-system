<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementGoods */

$this->title = 'Create Agreement Goods';
$this->params['breadcrumbs'][] = ['label' => 'Agreement Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agreement-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
