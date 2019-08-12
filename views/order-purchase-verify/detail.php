<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;
use app\models\Admin;
use app\models\AuthAssignment;

$this->title = '采购单审核详情';
$this->params['breadcrumbs'][] = $this->title;



$use_admin = AuthAssignment::find()->where(['item_name' => '采购员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;

$userId = Yii::$app->user->identity->id;
?>

<div class="box table-responsive">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead class="data" data-order_purchase_id="<?=$_GET['id']?>">
                <tr>
                    <th>序号</th>
                    <th>零件号A</th>
                    <th>零件号B</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>技术备注</th>
                    <th>加工</th>
                    <th>特制</th>
                    <th>铭牌</th>
                    <th>供应商</th>
                    <th>供应商缩写</th>
                    <th>货期(天)</th>
                    <th>税率</th>
                    <th>未率单价</th>
                    <th>含率单价</th>
                    <th>未率总价</th>
                    <th>含率总价</th>
                    <th>数量</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentGoods as $item):?>
                <tr class="order_purchase_list">
                    <td><?=$item->serial?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=$item->goods->technique_remark?></td>
                    <td><?=Goods::$process[$item->goods->is_process]?></td>
                    <td><?=Goods::$special[$item->goods->is_special]?></td>
                    <td><?=Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?=$item->inquiry->supplier->name?></td>
                    <td><?=$item->inquiry->supplier->short_name?></td>
                    <td><?=$item->inquiry->delivery_time?></td>
                    <td class="tax"><?=$item->tax_rate?></td>
                    <td class="price"><?=$item->fixed_price?></td>
                    <td class="tax_price"><?=$item->fixed_tax_price?></td>
                    <td class="all_price"><?=$item->fixed_all_price?></td>
                    <td class="all_tax_price"><?=$item->fixed_all_tax_price?></td>
                    <td class="afterNumber"><?=$item->fixed_number?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?= $form->field($model, 'reason')->textInput(); ?>

    </div>
    <div class="box-footer">
        <?= Html::button('审核通过', [
                'class' => 'btn btn-success payment_save',
                'name'  => 'submit-button']
        )?>
        <?= Html::button('驳回', [
            'class' => 'btn btn-warning btn-flat',
        ])?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">

</script>
