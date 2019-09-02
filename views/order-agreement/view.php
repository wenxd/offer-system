<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '收入合同详情';
$this->params['breadcrumbs'][] = $this->title;

$model->agreement_date = substr($model->agreement_date, 0, 10);

$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;

?>
<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_quote_id="<?=$_GET['id']?>">
            <tr>
                <?php if(!in_array($userId, $adminIds)):?>
                    <th>零件号</th>
                <?php endif;?>
                <th>厂家号</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>单位</th>
                <th>技术备注</th>
                <th>加工</th>
                <th>特制</th>
                <th>铭牌</th>
                <th>图片</th>
                <th>供应商</th>
                <th>税率</th>
                <th>未税单价</th>
                <th>含率单价</th>
                <th>未税总价</th>
                <th>含率总价</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($agreementGoods as $item):?>
                <tr class="order_quote_list">
                    <?php if(!in_array($userId, $adminIds)):?>
                        <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td class="goods_id" data-goods_id="<?=$item->goods_id?>" data-goods_type="<?=$item->type?>"
                        data-relevance_id="<?=$item->relevance_id?>" data-quote_goods_id="<?=$item->id?>">
                        <?=$item->goods->description?>
                    </td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->goods->technique_remark?></td>
                    <td><?=Goods::$process[$item->goods->is_process]?></td>
                    <td><?=Goods::$special[$item->goods->is_special]?></td>
                    <td><?=Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?=Html::img($item->goods->img_url, ['width' => '50px'])?></td>
                    <td><?=$item->type ? $item->stock->supplier->name : $item->inquiry->supplier->name?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->quote_price?></td>
                    <td class="tax_price"><?=$item->quote_tax_price?></td>
                    <td class="all_price"><?=$item->quote_all_price?></td>
                    <td class="all_tax_price"><?=$item->quote_all_tax_price?></td>
                    <td class="afterNumber"><?=$item->number?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'agreement_sn')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'agreement_date')->widget(DateTimePicker::className(), [
            'removeButton'  => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView'   => 2,  //最大选择范围（年）
                'minView'   => 2,  //最小选择范围（年）
            ]
        ])->textInput(['readonly' => true]);?>

    </div>
    <?php ActiveForm::end(); ?>
</div>
