<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\{Supplier, AuthAssignment};

/* @var $this yii\web\View */
/* @var $model app\models\Supplier */
/* @var $form yii\widgets\ActiveForm */
$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$userId = Yii::$app->user->identity->id;
?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'contacts')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'grade')->dropDownList(Supplier::$grade) ?>

        <?= $form->field($model, 'grade_reason')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'advantage_product')->textInput(['maxlength' => true]) ?>
        <?php if (!in_array($userId, $adminIds)):?>
            <?= $form->field($model, 'is_confirm')->radioList(Supplier::$confirm, ['class' => 'radio']) ?>
        <?php endif;?>
    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success' : 'btn btn-primary',
                'name'  => 'submit-button']
        )?>
        <?php if (!in_array($userId, $adminIds)):?>
            <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
                'class' => 'btn btn-default btn-flat',
            ])?>
        <?php endif;?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
