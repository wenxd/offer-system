<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $model app\models\Goods */
$goods_number = $agreementGoods->goods->goods_number;
$this->title = "总成组装";
$this->params['breadcrumbs'][] = $this->title;
$number = $agreementGoods->strategy_number - $agreementGoods->assemble_number;
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
                            <td class="position"><?= $item['stock_position'] ?></td>
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
                        <th>策略采购数量</th>
                        <th>已总成数量</th>
                        <th>库存数量</th>
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
                        <td><?= $agreementGoods->strategy_number ?></td>
                        <td><?= $agreementGoods->assemble_number ?></td>
                        <td><?= $agreementGoods->stock ? $agreementGoods->stock->number : 0 ?></td>
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
                            <input type="number" size="4" class="number" min="0" max="<?= $mix_number ?>" style="width: 100px;display: none" value="0">
                        <?= $number ?>
                        </td>
                    </tr>
                    <tr>
                        <th>库存位置</th>
                        <td><?php
                            $position = $agreementGoods->stock ? $agreementGoods->stock->position : '';
//                            $position = 0;
                            if ($position) :
                            ?>
                                <input type="text" class="goods_position" style="width: 100px;" disabled="disabled" value="<?=$position?>">
                            <?php else:;?>
                                <input type="text" class="goods_position" style="width: 100px;">
                            <?php endif;?>
                        </td>
                    </tr>
                    </thead>
                </table>
                <p></p>

            </div>
        </div>
        <?php
        if ($mix_number > 0) {
            echo Html::button('总成组装', ['class' => 'btn btn-success assembly_save', 'name' => 'submit-button']);
        }
        ?>
    </div>
</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var number = <?=$number?>;
        var mix_number = <?=$mix_number?>;
        // $(".assembly_save").attr("disabled", true);
        //计算数量
        function count_number() {
            if (number < 1) {
                $(".assembly_save").attr("disabled", true);
                layer.msg('总成数量 < 1,不允许组装', {time: 1000});
                return false;
            }
            // var number = $('.number').val();
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
            // var number = $('.number').val();
            var goods_position = $('.goods_position').val();
            console.log(goods_position);
            if (!goods_position) {
                layer.msg('库存位置不可为空', {time:2000});
                return false;
            }
            var son_info = [];
            $('.goods_id').each(function (index, element) {
                var goods_id = $(element).html();
                var max_number = $(element).parent().find('.max_number').html();
                var position = $(element).parent().find('.position').html();
                son_info.push({goods_id: goods_id, number: max_number, position: position});
            });
            var info = {
                order_id: "<?=$agreementGoods->order_id?>",
                order_sn: "<?=$agreementGoods->order->order_sn?>",
                order_agreement_id: "<?=$agreementGoods->order_agreement_id?>",
                order_agreement_sn: "<?=$agreementGoods->order_agreement_sn?>",
                goods_id: "<?=$agreementGoods->goods_id?>",
                goods_number: "<?=$agreementGoods->goods->goods_number?>",
                goods_position: goods_position,
                number: number,
                son_info: son_info
            };
            console.log(info);
            $.ajax({
                type:"post",
                url:'<?=$_SERVER['REQUEST_URI']?>',
                data:{info:info},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        window.history.back();
                    } else {
                        layer.msg(res.msg, {time:2000});
                    }
                }
            });
        });
        count_number();
    });
</script>


