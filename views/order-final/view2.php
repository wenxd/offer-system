<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Goods;

$this->title = '成本单详情';
$this->params['breadcrumbs'][] = $this->title;

?>
<section class="content">
    <div class="box table-responsive">
        <div class="box-header">
            <?= Html::a('<i class="fa fa-download"></i> 没有发行价的零件导出', Url::to(['/order/goods', 'id' => $_GET['id']]), [
                'data-pjax' => '0',
                'class'     => 'btn btn-primary btn-flat',
            ]);?>
            <?= Html::a('<i class="fa fa-download"></i> 成本单导出', Url::to(['order-final/view', 'id' => $_GET['id'], 'key' => $_GET['key'], 'download' => true]), [
                'data-pjax' => '0',
                'class'     => 'btn btn-primary btn-flat',
            ]);?>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover" style="width: 2000px;">
                <thead>
                <tr class="goods">
                    <th>序号</th>
                    <th>零件号</th>
                    <th style="width: 200px;">中文描述</th>
                    <th style="width: 200px;">英文描述</th>
                    <th>原厂家</th>
                    <th>厂家号</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>供应商</th>
                    <th>询价员</th>
                    <th>税率</th>
                    <th>发行含税单价</th>
                    <th>发行含税总价</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                    <th>货期</th>
                    <th>更新时间</th>
                    <th>创建时间</th>
                    <th>关联询价记录</th>
                    <th>询价ID</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($finalGoods as $key => $item):?>
                    <tr class="goods_list">
                        <td><?= $item->serial?></td>
                        <td><?= Html::a($item->goods->goods_number . ' ' . $item->goods->material_code, Url::to(['goods/search-result', 'good_number' => $item->goods->goods_number, 'type' => 1]));?></td>
                        <td><?= $item->goods->description?></td>
                        <td><?= $item->goods->description_en?></td>
                        <td><?= $item->goods->original_company?></td>
                        <td><?= $item->goods->goods_number_b?></td>
                        <td><?= $item->goods->unit?></td>
                        <td class="number"><?=$item->number?></td>
                        <td><?= $item->inquiry->supplier->name?></td>
                        <td><?= $item->inquiry->admin->username?></td>
                        <td><?= $item->tax?></td>
                        <?php
                            $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax/100), 2, '.', '');
                        ?>
                        <td><?= $publish_tax_price ?></td>
                        <td class="publish_tax_price"><?= $publish_tax_price * $item->number?></td>
                        <td class="price"><?=$item->price?></td>
                        <td class="tax_price"><?=$item->tax_price?></td>
                        <td class="all_price"></td>
                        <td class="all_tax_price"></td>
                        <td class="delivery_time"><?=$item->delivery_time?></td>
                        <td><?= substr($item->goods->updated_at, 0 , 10)?></td>
                        <td><?= substr($item->goods->created_at, 0 , 10)?></td>
                        <td class="relevance"><?= $item->inquiry ? '是' : '否'?></td>
                        <td><?= Html::a($item->relevance_id, Url::to(['inquiry/view', 'id' => $item->relevance_id]))?></td>
                        <td><?= Html::a('<i class="fa fa-paper-plane-o"></i> 关联询价记录',
                                Url::to(['inquiry/search', 'goods_id' => $item->goods_id, 'order_id' => $item->order_id, 'serial' => $item->serial, 'key' => $item->key, 'final_goods_id' => $item->id]),
                                ['class' => 'btn btn-primary btn-xs btn-flat']
                            );?></td>
                    </tr>
                <?php endforeach;?>
                <tr style="background-color: #acccb9">
                    <td colspan="13">汇总统计</td>
                    <td class="sta_all_publish_tax_price"></td>
                    <td></td>
                    <td></td>
                    <td class="sta_all_price"></td>
                    <td class="sta_all_tax_price"></td>
                    <td class="mostLongTime"></td>
                    <td colspan="4"></td>
                </tr>
                </tbody>
            </table>
            <?= $form->field($model, 'final_sn')->textInput(['readonly' => true]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var sta_all_price               = 0;
        var sta_all_tax_price           = 0;
        var mostLongTime                = 0;
        var sta_all_publish_tax_price   = 0;
        $('.goods_list').each(function (i, e) {
            var delivery_time = parseFloat($(e).find('.delivery_time').text());
            var number = $(e).find('.number').text();
            var price = $(e).find('.price').text();
            var tax_price = $(e).find('.tax_price').text();
            var all_price = number * price;
            var all_tax_price = number * tax_price;
            sta_all_price += all_price;
            sta_all_tax_price += all_tax_price;
            if (delivery_time > mostLongTime) {
                mostLongTime = delivery_time;
            }
            $(e).find('.all_price').text(all_price.toFixed(2));
            $(e).find('.all_tax_price').text(all_tax_price.toFixed(2));
            var publish_tax_price = parseFloat($(e).find('.publish_tax_price').text());
            if (publish_tax_price) {
                sta_all_publish_tax_price += publish_tax_price;
            }
        });
        $('.sta_all_publish_tax_price').text(sta_all_publish_tax_price.toFixed(2));
        $('.sta_all_price').text(sta_all_price.toFixed(2));
        $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        $('.mostLongTime').text(mostLongTime);
    });
</script>
