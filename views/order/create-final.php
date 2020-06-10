<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;

$this->title = '生成成本单';
$this->params['breadcrumbs'][] = $this->title;

$inquiry_goods_ids = ArrayHelper::getColumn($finalGoods, 'goods_id');
$goods_id = ArrayHelper::getColumn($goods, 'id');
$customer_name = $order->customer ? $order->customer->short_name : '';

$model->final_sn = 'C' . date('ymd_') . $customer_name . '_' . $number;
?>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr class="goods" data-goods_ids="<?=json_encode($goods_id)?>" data-order_id="<?=$_GET['id']?>" data-key="<?=$_GET['key']?>">
                        <th>序号</th>
                        <th>零件号</th>
                        <th>厂家号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>原厂家备注</th>
                        <th>单位</th>
                        <th>数量</th>
                        <th>税率</th>
                        <th>发行含税单价</th>
                        <th>发行含税总价</th>
                        <th>发行货期</th>
                        <th>未税单价</th>
                        <th>含税单价</th>
                        <th>未税总价</th>
                        <th>含税总价</th>
                        <th>货期</th>
                        <th>加工</th>
                        <th>特制</th>
                        <th>铭牌</th>
                        <th>更新时间</th>
                        <th>创建时间</th>
                        <th>技术备注</th>
                        <th>关联询价记录</th>
                        <th>询价ID</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderGoods as $key => $item):?>
                    <tr class="goods_list">
                        <td><?= $item->serial?></td>
                        <td><?= Html::a($item->goods->goods_number, Url::to(['inquiry/search', 'goods_id' => $item->goods_id, 'order_id' => ($_GET['id'] ?? ''), 'key' => ($_GET['key'] ?? '')]));?></td>
                        <td><?= $item->goods->goods_number_b?></td>
                        <td><?= $item->goods->description?></td>
                        <td><?= $item->goods->description_en?></td>
                        <td><?= $item->goods->original_company?></td>
                        <td><?= $item->goods->original_company_remark?></td>
                        <td><?= $item->goods->unit?></td>
                        <td class="number"><?= $item->number?></td>
                        <td><?= $item->finalGoods ? $item->finalGoods->inquiry->tax_rate : ''?></td>
                        <?php
                            $publish_tax_price = $item->goods->publish_tax_price ? $item->goods->publish_tax_price : $item->goods->publish_tax_price;
                        ?>
                        <td class="publish_tax_price"><?=$publish_tax_price?></td>
                        <td class="all_publish_tax_price"></td>
                        <td class="publish_delivery_time"><?=$item->goods->publish_delivery_time?></td>
                        <td class="price"><?= $item->finalGoods ? $item->finalGoods->inquiry->price : ''?></td>
                        <td class="tax_price"><?= $item->finalGoods ? $item->finalGoods->inquiry->tax_price : ''?></td>
                        <td class="all_price"></td>
                        <td class="all_tax_price"></td>
                        <td class="delivery_time"><?= $item->finalGoods ? ($item->finalGoods->type ? '' : $item->finalGoods->inquiry->delivery_time) : ''?></td>
                        <td><?= Goods::$process[$item->goods->is_process]?></td>
                        <td><?= Goods::$special[$item->goods->is_special]?></td>
                        <td><?= Goods::$nameplate[$item->goods->is_nameplate]?></td>
                        <td><?= substr($item->goods->updated_at, 0 , 10)?></td>
                        <td><?= substr($item->goods->created_at, 0 , 10)?></td>
                        <td><?= $item->goods->technique_remark?></td>
                        <td class="relevance"><?= in_array($item->goods_id, $inquiry_goods_ids) ? '是' : '否'?></td>
                        <td><?= isset($finalGoods[$item->goods_id]) ? Html::a($finalGoods[$item->goods_id]['relevance_id'], Url::to(['inquiry/view', 'id' => $finalGoods[$item->goods_id]['relevance_id']])) : ''?></td>
                        <td><?= Html::a('<i class="fa fa-paper-plane-o"></i> 关联询价记录',
                                Url::to(['inquiry/search', 'goods_id' => $item->goods_id, 'order_id' => ($_GET['id'] ?? ''), 'serial' => $item->serial, 'key' => ($_GET['key'] ?? '')]),
                                ['class' => 'btn btn-primary btn-xs btn-flat']
                            );?></td>
                    </tr>
                    <?php endforeach;?>
                    <tr style="background-color: #acccb9">
                        <td colspan="11">汇总统计</td>
                        <td class="sta_publish_tax_price"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="sta_all_tax_price"></td>
                        <td class="mostLongTime"></td>
                        <td colspan="9"></td>
                    </tr>
                </tbody>
            </table>

            <?= $form->field($model, 'final_sn')->textInput() ?>

        </div>
        <div class="box-footer">
            <?= Html::button('保存成本单', [
                    'class' => 'btn btn-success final_save',
                    'name'  => 'submit-button']
            )?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //保存成本订单
        $('.final_save').click(function (e) {
            var flag = false;
            $('.relevance').each(function (i, element) {
                if ($(element).text() == '否') {
                    flag = true;
                }
            });
            if (flag) {
                layer.msg('所有的零件需关联询价', {time:2000});
                return false;
            }

            var final_sn  = $('#orderfinal-final_sn').val();
            if (!final_sn) {
                layer.msg('请输入成本单号', {time:2000});
                return false;
            }

            var goods_ids = $('.goods').data('goods_ids');
            var order_id  = $('.goods').data('order_id');
            var key       = $('.goods').data('key');

            $.ajax({
                type:"post",
                url:'?r=order-final/save-order',
                data:{order_id:order_id, goods_ids:goods_ids, key:key, final_sn:final_sn},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-final/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
        var sta_all_price           = 0;
        var sta_all_tax_price       = 0;
        var mostLongTime            = 0;
        var sta_publish_tax_price   = 0;
        $('.goods_list').each(function (i, e) {
            var delivery_time       = parseFloat($(e).find('.delivery_time').text());
            var number              = $(e).find('.number').text();
            var price               = $(e).find('.price').text();
            var tax_price           = $(e).find('.tax_price').text();
            var publish_tax_price   = parseFloat($(e).find('.publish_tax_price').text());
            var all_price           = number * price;
            var all_tax_price       = number * tax_price;
            var all_publish_tax_price = number * publish_tax_price;
            sta_all_price += all_price;
            sta_all_tax_price += all_tax_price;
            if (delivery_time > mostLongTime) {
                mostLongTime = delivery_time;
            }

            sta_publish_tax_price += all_publish_tax_price;

            $(e).find('.all_price').text(all_price.toFixed(2));
            $(e).find('.all_tax_price').text(all_tax_price.toFixed(2));
            $(e).find('.all_publish_tax_price').text(all_publish_tax_price.toFixed(2));

        });

        $('.sta_publish_tax_price').text(sta_publish_tax_price);
        $('.sta_all_price').text(sta_all_price.toFixed(2));
        $('.sta_all_tax_price').text(sta_all_tax_price.toFixed(2));
        $('.mostLongTime').text(mostLongTime);

    });
</script>
