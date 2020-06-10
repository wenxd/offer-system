<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '询价列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
