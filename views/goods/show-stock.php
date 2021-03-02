<?php

use yii\helpers\ArrayHelper;
use app\models\Goods;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = '零件库存搜索';
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'id')->widget(\kartik\select2\Select2::className(), [
            'data' => ArrayHelper::map(Goods::getGoodsCode(), 'goods_id', 'info'),
            'options' => ['placeholder' => '请输入输入零件号或者厂家号', 'class' => 'form-control'],
        ])->label(false) ?>
    </div>
    <div class="col-md-2">
        <?= Html::submitButton('搜索', [
                'class' => 'btn btn-primary created',
                'name' => 'submit-button']
        ) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php if ($goods_info): ?>
<div class="box-body">
    <h3>零件信息</h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>零件ID</th>
            <th>零件号</th>
            <th>厂家号</th>
            <th>零件备注</th>
            <th>中文描述</th>
            <th>英文描述</th>
            <th>原厂家</th>
            <th>单位</th>
            <th>库存数量</th>
            <th>库存位置</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= $goods_info['id'] ?></td>
            <td><?= $goods_info['goods_number'] ?></td>
            <td><?= $goods_info['goods_number_b'] ?></td>
            <td><?= $goods_info['remark'] ?></td>
            <td><?= $goods_info['description'] ?></td>
            <td><?= $goods_info['description_en'] ?></td>
            <td><?= $goods_info['original_company'] ?></td>
            <td><?= $goods_info['unit'] ?></td>
            <td><?= $goods_info['number'] ?></td>
            <td><?= $goods_info['position'] ?></td>
        </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php if ($superior_goods_info): ?>
    <div class="box-body">
        <h3>上级零件信息</h3>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>零件ID</th>
                <th>零件号</th>
                <th>厂家号</th>
                <th>零件备注</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>原厂家</th>
                <th>单位</th>
                <th>库存数量</th>
                <th>库存位置</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($superior_goods_info as $sup_item) : ?>
                <tr>
                    <td><?= $sup_item['id'] ?></td>
                    <td><?= $sup_item['goods_number'] ?></td>
                    <td><?= $sup_item['goods_number_b'] ?></td>
                    <td><?= $sup_item['remark'] ?></td>
                    <td><?= $sup_item['description'] ?></td>
                    <td><?= $sup_item['description_en'] ?></td>
                    <td><?= $sup_item['original_company'] ?></td>
                    <td><?= $sup_item['unit'] ?></td>
                    <td><?= $sup_item['number'] ?></td>
                    <td><?= $sup_item['position'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($son_goods_info): ?>
<div class="box-body">
    <h3>子级零件信息(可组装：<?=$assemble_number?>)</h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>零件ID</th>
            <th>零件号</th>
            <th>厂家号</th>
            <th>零件备注</th>
            <th>中文描述</th>
            <th>英文描述</th>
            <th>原厂家</th>
            <th>单位</th>
            <th>库存数量</th>
            <th>库存位置</th>
            <th>组装需求数量</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($son_goods_info as $son_item) : ?>
            <tr>
                <td><?= $son_item['id'] ?></td>
                <td><?= $son_item['goods_number'] ?></td>
                <td><?= $son_item['goods_number_b'] ?></td>
                <td><?= $son_item['remark'] ?></td>
                <td><?= $son_item['description'] ?></td>
                <td><?= $son_item['description_en'] ?></td>
                <td><?= $son_item['original_company'] ?></td>
                <td><?= $son_item['unit'] ?></td>
                <td><?= $son_item['number'] ?></td>
                <td><?= $son_item['position'] ?></td>
                <td><?= $son_item['relation_number'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

