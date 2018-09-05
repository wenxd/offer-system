<?php
use yii\helpers\Url;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\QuoteRecord;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
$this->title = '询价单详情';
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
                        <th>零件ID</th>
                        <th>零件号</th>
                        <th>是否最新</th>
                        <th>是否优选</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>商品类型</th>
                        <th>询价时间</th>
                        <th>税率</th>
                        <th>未税价格</th>
                        <th>含税价格</th>
                        <th>交货期</th>
                        <th>询价备注</th>
                        <th>金额</th>
                        <th>是否询价</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($inquiryList as $key => $value):?>
                    <tr>
                        <td><?=$value->goods_id?></td>
                        <td><?=$value->goods->goods_number?></td>
                        <td>
                            <?php
                            if ($value->type == 1 || $value->type == 2) {
                                echo Inquiry::$newest[$value->inquiry->is_newest];
                            } else {
                                echo '否';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($value->type == 1 || $value->type == 2) {
                                echo Inquiry::$newest[$value->inquiry->is_better];
                            } else {
                                echo '否';
                            }
                            ?>
                        </td>
                        <td><?=$value->type == 3 ? $value->stock->supplier_id : $value->inquiry->supplier_id?></td>
                        <td><?=$value->type == 3 ? Supplier::getAllDropDown()[$value->stock->supplier_id] : Supplier::getAllDropDown()[$value->inquiry->supplier_id]?></td>
                        <td><?=$value->type == 3 ? '库存商品' : '询价商品'?></td>
                        <td><?=$value->type == 3 ? '无' : $value->inquiry->inquiry_datetime?></td>
                        <td><?=$value->type == 3 ? $value->stock->tax_rate : $value->inquiry->tax_rate?></td>
                        <td class="price"><?=$value->type == 3 ? $value->stock->price : $value->inquiry->price?></td>
                        <td><?=$value->type == 3 ? $value->stock->tax_price : $value->inquiry->tax_price?></td>
                        <td><?=$value->offer_date?></td>
                        <td><?=$value->remark?></td>
                        <td class="money">
                            <?=number_format($value['quote_price'] * $value['number'], 2, '.', '')?>
                        </td>
                        <td><?=QuoteRecord::$status[$value->status]?></td>
                        <td>
                            <?php if (!$value->status):?>
                            <a class="btn btn-primary btn-xs btn-flat" href="<?=Url::to(['quote-record/update', 'id' => $value->id])?>"><i class="fa fa-inbox"></i> 询价</a>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="13" style="text-align: right;"><b>金额合计</b></td>
                    <td class="all_money"></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
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