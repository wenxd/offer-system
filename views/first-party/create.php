<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FirstParty */

$this->title = '创建甲方采办人';
$this->params['breadcrumbs'][] = ['label' => 'First Parties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="first-party-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
