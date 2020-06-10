<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Inquiry */

$this->title = '添加询价记录';
$this->params['breadcrumbs'][] = ['label' => '询价记录列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inquiry-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
