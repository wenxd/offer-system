<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
$goods_number = $agreementGoods->goods->goods_number;
$this->title = "出库零件总成";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8">
        <div class="goods-view box">
            <div class="box-body">
                <h3>子零件</h3>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>零件ID</th>
                        <th>零件号</th>
                        <th>厂家号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>单位</th>
                        <th>库存数量</th>
                        <th>库存位置</th>
                        <th>总成需要数量(单)</th>
                        <th>总成需要数量(总)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($goods_son as $item): ?>
                        <tr>
                            <td class="goods_id"><?= $item['goods_id'] ?></td>
                            <td><?= $item['goods_number'] ?></td>
                            <td><?= $item['goods_number_b'] ?></td>
                            <td><?= $item['description'] ?></td>
                            <td><?= $item['description_en'] ?></td>
                            <td><?= $item['original_company'] ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= $item['stock_number'] ?></td>
                            <td><?= $item['stock_position'] ?></td>
                            <td class="min_number"><?= $item['number'] ?></td>
                            <td class="max_number">0</td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="goods-view box">
            <div class="box-body">
                <h3>总成信息</h3>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>收入合同单号</th>
                        <th>订单号</th>
                        <th>零件号</th>
                        <th>厂家号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>单位</th>
                        <th>合同数量</th>
                        <th>库存数量</th>
                        <th>库存位置</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>

                        <td><?= $agreementGoods->order_agreement_sn ?></td>
                        <td><?= $agreementGoods->order->order_sn ?></td>
                        <td><?= $agreementGoods->goods->goods_number ?></td>
                        <td><?= $agreementGoods->goods->goods_number_b ?></td>
                        <td><?= $agreementGoods->goods->description ?></td>
                        <td><?= $agreementGoods->goods->description_en ?></td>
                        <td><?= $agreementGoods->goods->original_company ?></td>
                        <td><?= $agreementGoods->goods->unit ?></td>
                        <td><?= $agreementGoods->order_number ?></td>
                        <td><?= $agreementGoods->stock ? $agreementGoods->stock->number : 0 ?></td>
                        <td><?= $agreementGoods->stock ? $agreementGoods->stock->position : '' ?></td>
                    </tr>
                    </tbody>
                    <thead>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <th>最大可总成数量</th>
                        <td><?= $mix_number ?></td>
                    </tr>
                    <tr>
                        <th>总成数量</th>
                        <td>
                            <input type="number" size="4" class="number" min="0" max="<?= $mix_number ?>"
                                   style="width: 100px;" value="2">
                        </td>
                    </tr>
                    </thead>
                </table>
                <p></p>

            </div>
        </div>
        <?php
        if ($mix_number > 0) {
            echo Html::button('保存总成', ['class' => 'btn btn-success assembly_save', 'name' => 'submit-button']);
        }
        ?>
    </div>
</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var mix_number = <?=$mix_number?>;
        // $(".assembly_save").attr("disabled", true);
        //计算数量
        function count_number() {
            var number = $('.number').val();
            if (number > mix_number) {
                layer.msg('不能超过最大可总成数量：' + mix_number, {time: 1000});
                $(this).val(mix_number);
                number = mix_number;
            }

            if (number > 0) {
                $(".assembly_save").attr("disabled", false);
            } else {
                $(".assembly_save").attr("disabled", true);
                $(this).val(0);
                number = 0;
            }
            // 计算子零件需要数量
            $('.goods_id').each(function (index, element) {
                var min_number = $(element).parent().find('.min_number').html();

                $(element).parent().find('.max_number').html(min_number * number);
            });
        }

        //输入数量
        $(".number").bind('input propertychange', function (e) {
            count_number();
        });

        //保存
        $('.assembly_save').click(function (e) {
            var number = $('.number').val();
            var son_info = [];
            $('.goods_id').each(function (index, element) {
                var goods_id = $(element).html();
                var max_number = $(element).parent().find('.max_number').html();
                son_info.push({goods_id: goods_id, max_number: max_number});
            });
            var info = [];
            info['order_id'] = "<?=$agreementGoods->order_id?>";
            info['order_sn'] = "<?=$agreementGoods->order->order_sn?>";
            info['order_agreement_id'] = "<?=$agreementGoods->order_agreement_id?>";
            info['order_agreement_sn'] = "<?=$agreementGoods->order_agreement_sn?>";
            info['goods_id'] = "<?=$agreementGoods->goods_id?>";
            info['goods_number'] = "<?=$agreementGoods->goods->goods_number?>";
            info['number'] = number;
            info['son_info'] = son_info;
            console.log(info);
            console.log(number);
            console.log(son_info);
            $.ajax({
                type:"post",
                url:'<?=$_SERVER['REQUEST_URI']?>',
                data:{info:info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                    } else {
                        layer.msg(res.msg, {time:2000});
                    }
                }
            });
        });
        count_number();
    });
</script>


