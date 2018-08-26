<?php
use app\models\Inquiry;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
$this->title = '报价单详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .but button{
        float: right;
    }
    .but a{
        float: right;
        margin-right: 10px;
    }
    .offer {
        margin-top: 10px;
    }
</style>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>零件号</th>
                        <th>是否最新</th>
                        <th>是否优选</th>
                        <th>商品类型</th>
                        <th>价格</th>
                        <th>库存数量</th>
                        <th>询价时间</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>购买数量</th>
                        <th>金额</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="11" style="text-align: center;">最新询价记录</td>
                </tr>
                <?php foreach ($inquiryNewest as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['inquiry_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['inquiry_price'] * $value['number'], 2, '.', '')?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="11" style="text-align: center;">优选记录</td>
                </tr>
                <?php foreach ($inquiryBetter as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['inquiry_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['inquiry_price'] * $value['number'], 2, '.', '')?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="11" style="text-align: center;">本地库存零件记录</td>
                </tr>
                <?php foreach ($stockList as $key => $value):?>
                    <tr>
                        <td><?=$value['good_id']?></td>
                        <td>无</td>
                        <td>无</td>
                        <td>库存商品</td>
                        <td><?=$value['price']?></td>
                        <td><?=$value['number']?></td>
                        <td>无</td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_name']?></td>
                        <td><?=$value['number']?></td>
                        <td class="money">
                            <?=number_format($value['price'] * $value['number'], 2, '.', '')?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="10" style="text-align: right;"><b>金额合计</b></td>
                    <td class="all_money"></td>
                </tr>
                </tbody>
            </table>

            <?= $form->field($model, 'order_id')->textInput(['readonly' => 'true']) ?>

            <?= $form->field($model, 'description')->textInput(['readonly' => 'true']) ?>

            <?= $form->field($model, 'provide_date')->widget(DateTimePicker::className(), [
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd hh:ii:00',
                    'startView' =>2,  //其实范围（0：日  1：天 2：年）
                    'maxView'   =>2,  //最大选择范围（年）
                    'minView'   =>2,  //最小选择范围（年）
                ]
            ])->textInput(['readonly' => 'true']);?>

            <?= $form->field($model, 'quote_price')->textInput(['readonly' => 'true']) ?>

            <?= $form->field($model, 'remark')->textInput(['readonly' => 'true']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>
<script type="text/javascript">
    window.onload = function() {
        var allMoney = 0;
        $('.money').each(function (index, element){
            allMoney += parseFloat(element.innerText)
        });
        $('.all_money').html(allMoney);
    }
</script>