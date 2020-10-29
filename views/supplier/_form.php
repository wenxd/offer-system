<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\{Supplier, AuthAssignment, Helper};

/* @var $this yii\web\View */
/* @var $model app\models\Supplier */
/* @var $form yii\widgets\ActiveForm */
$use_admin = AuthAssignment::find()->where(['item_name' => ['询价员', '采购员']])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$admins = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
$userId = Yii::$app->user->identity->id;

if ($model->isNewRecord) {
    $model->is_confirm = Supplier::IS_CONFIRM_NO;
}

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
        <?php if (!$model->isNewRecord && !in_array($userId, $admins)):?>
            <?= $form->field($model, 'is_confirm')->radioList(Supplier::$confirm, ['class' => 'radio']) ?>
        <?php endif;?>
        <?php if (!in_array($userId, $adminIds)):?>
            <?= $form->field($model, 'admin_id')->dropDownList(Helper::getAdminListAll()) ?>
        <?php endif;?>
    </div>

    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? '创建' :  '更新', [
                'class' => $model->isNewRecord? 'btn btn-success submit_success' : 'btn btn-primary submit_success',
                'name'  => 'submit-button']
        )?>
        <?php if (!in_array($userId, $adminIds)):?>
            <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
                'class' => 'btn btn-default btn-flat',
            ])?>
        <?php endif;?>
    </div>


    <?php ActiveForm::end(); ?>
    <?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
    <script>
        var confirm = <?=$model->is_confirm?>;
        function is_confirm() {
            if (confirm == 1) {
                $(".submit_success").show()
            } else {
                $(".submit_success").hide();
            }
        }
        $(document).ready(function () {
            // is_confirm();
            $('#supplier-is_confirm').change(function (e) {
                confirm = confirm ? 0 : 1;
                is_confirm();
            });
        });

    </script>

</div>
