<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '客户管理列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'short_name',
            'mobile',
            'company_telephone',
            'company_fax',
            'company_address',
            'company_email',
            'company_contacts',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
