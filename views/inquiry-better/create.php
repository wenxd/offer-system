<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */

$this->title = '创建询价单';
$this->params['breadcrumbs'][] = ['label' => '询价列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
