<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Competitor */

$this->title = '创建竞争对手';
$this->params['breadcrumbs'][] = ['label' => '竞争对手列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competitor-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
