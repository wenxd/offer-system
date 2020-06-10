<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Brand */

$this->title = '创建品牌';
$this->params['breadcrumbs'][] = ['label' => '品牌列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
