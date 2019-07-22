<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SystemConfig */

$this->title = 'Create System Config';
$this->params['breadcrumbs'][] = ['label' => 'System Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
