<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SystemNotice */

$this->title = 'Create System Notice';
$this->params['breadcrumbs'][] = ['label' => 'System Notices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-notice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
