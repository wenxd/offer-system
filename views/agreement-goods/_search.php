<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgreementGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agreement-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_agreement_id') ?>

    <?= $form->field($model, 'order_agreement_sn') ?>

    <?= $form->field($model, 'order_quote_id') ?>

    <?php // echo $form->field($model, 'order_quote_sn') ?>

    <?php // echo $form->field($model, 'serial') ?>

    <?php // echo $form->field($model, 'goods_id') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'relevance_id') ?>

    <?php // echo $form->field($model, 'tax_rate') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'tax_price') ?>

    <?php // echo $form->field($model, 'all_price') ?>

    <?php // echo $form->field($model, 'all_tax_price') ?>

    <?php // echo $form->field($model, 'quote_price') ?>

    <?php // echo $form->field($model, 'quote_tax_price') ?>

    <?php // echo $form->field($model, 'quote_all_price') ?>

    <?php // echo $form->field($model, 'quote_all_tax_price') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'is_agreement') ?>

    <?php // echo $form->field($model, 'agreement_sn') ?>

    <?php // echo $form->field($model, 'purchase_date') ?>

    <?php // echo $form->field($model, 'agreement_date') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'inquiry_admin_id') ?>

    <?php // echo $form->field($model, 'is_out') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
