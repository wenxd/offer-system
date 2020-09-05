<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */

$this->title = '零件详情';
$this->params['breadcrumbs'][] = ['label' => '零件管理列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_number',
            'goods_number_b',
            'description',
            'description_en',
            'publish_tax',
            'publish_price',
            'publish_tax_price',
            'publish_delivery_time',
            'material',
            'original_company',
            'original_company_remark',
            'estimate_publish_price',
            'material_code',
            'import_mark',
            [
                'attribute' => 'is_process',
                'value'     => function ($model) {
                    return Goods::$process[$model->is_process];
                }
            ],
            [
                'attribute' => 'is_tz',
                'value'     => function ($model) {
                    return Goods::$tz[$model->is_tz];
                }
            ],
            [
                'attribute' => 'is_standard',
                'value'     => function ($model) {
                    return Goods::$standard[$model->is_standard];
                }
            ],
            [
                'attribute' => 'is_import',
                'value'     => function ($model) {
                    return Goods::$import[$model->is_import];
                }
            ],
            [
                'attribute' => 'is_repair',
                'value'     => function ($model) {
                    return Goods::$repair[$model->is_repair];
                }
            ],
            [
                'attribute' => 'img_id',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->img_url, ['width' => 300]);
                }
            ],
            [
                'attribute' => 'is_special',
                'value'     => function ($model) {
                    return Goods::$special[$model->is_special];
                }
            ],
            [
                'attribute' => 'is_emerg',
                'value'     => function ($model) {
                    return Goods::$emerg[$model->is_emerg];
                }
            ],
            [
                'attribute' => 'is_assembly',
                'value'     => function ($model) {
                    return Goods::$assembly[$model->is_assembly];
                }
            ],
            [
                'attribute' => 'is_nameplate',
                'value'     => function ($model) {
                    return Goods::$nameplate[$model->is_nameplate];
                }
            ],
            [
                'attribute' => 'nameplate_img_id',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img($model->nameplate_img_url, ['width' => 300]);
                }
            ],
            'part',
            'technique_remark',
            'remark',
            [
                'attribute'      => 'device_info',
                'format'         => 'raw',
                'value'          => function($model){
                    $text = '';
                    if ($model->device_info) {
                        foreach (json_decode($model->device_info, true) as $key => $device) {
                            $text .= $key . ':' . $device . '<br/>';
                        }
                    }
                    return $text;
                }
            ],
            [
                'attribute' => 'created_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
        ],
    ]) ?>
    <div class="box-body">
        <table id="example2" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>是否总成</th>
                    <th>品牌</th>
                    <th>零件号</th>
                    <th>厂家号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>加工</th>
                    <th>特制</th>
                    <th>铭牌</th>
                    <th>技术备注</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($goodsList as $key => $item):?>
                <tr>
                    <td><?= Goods::$assembly[$item->goods->is_assembly]?></td>
                    <td><?= $item->goods->material_code?></td>
                    <td><?= $item->goods->goods_number?></td>
                    <td><?= $item->goods->goods_number_b?></td>
                    <td><?= $item->goods->description?></td>
                    <td><?= $item->goods->description_en?></td>
                    <td><?= $item->goods->original_company?></td>
                    <td><?= $item->goods->original_company_remark?></td>
                    <td><?= $item->goods->unit?></td>
                    <td class="number"><?= $item->number?></td>
                    <td class="addColor"><?= Goods::$process[$item->goods->is_process]?></td>
                    <td class="addColor"><?= Goods::$special[$item->goods->is_special]?></td>
                    <td class="addColor"><?= Goods::$nameplate[$item->goods->is_nameplate]?></td>
                    <td><?= $item->goods->technique_remark?></td>
                    <td><?=Html::a('<i class="fa fa-trash"></i> 删除', 'Javascript: void(0)', [
                            'data-pjax'    => '0',
                            'class' => 'btn btn-sm btn-danger btn-flat delete',
                        ])?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $('.delete').click(function (e) {
        var res = confirm("确认删除吗");
        if (res) {

        } else {

        }
    });
</script>