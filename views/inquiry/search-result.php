<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Goods;
use app\models\Stock;

$this->title = '零件询价记录';
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
</style>
<section class="content">
    <div class="box table-responsive">
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="2">零件基础数据</th>
                    <th>零件号</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>是否加工</th>
                    <th>是否特制</th>
                    <th>是否铭牌</th>
                    <th>技术备注</th>
                    <th>更新时间</th>
                    <th>创建时间</th>
                </tr>
                <tr>
                    <td class="data" data-goods_id="<?=$goods ? $goods->id : ''?>" data-order_id="<?=$_GET['order_id'] ?? ''?>" data-key="<?=$_GET['key'] ?? ''?>">
                        <?=$goods ? $goods->goods_number : ''?>
                    </td>
                    <td><?=$goods ? $goods->description : ''?></td>
                    <td><?=$goods ? $goods->description_en : ''?></td>
                    <td><?=$goods ? $goods->original_company : ''?></td>
                    <td><?=$goods ? $goods->original_company_remark : ''?></td>
                    <td><?=$goods ? Goods::$process[$goods->is_process] : ''?></td>
                    <td><?=$goods ? Goods::$special[$goods->is_special] : ''?></td>
                    <td><?=$goods ? Goods::$nameplate[$goods->is_nameplate] : ''?></td>
                    <td><?=$goods ? $goods->technique_remark : ''?></td>
                    <td><?=$goods ? $goods->updated_at : ''?></td>
                    <td><?=$goods ? $goods->created_at : ''?></td>
                </tr>
                </thead>
            </table>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="5">询价记录</th>
                    <th></th>
                    <th>类型</th>
                    <th>零件号</th>
                    <th>单位</th>
                    <th>供应商</th>
                    <th>数量</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>货期</th>
                    <th>询价员</th>
                    <th>询价时间</th>
                    <th>是否优选</th>
                    <th>优选理由</th>
                    <th>备注</th>
                    <th>订单号</th>
                    <th>询价单号</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                    <th>操作</th>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryPrice ? $inquiryPrice->id : ''?>"></td>
                    <td>价格最优</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->supplier->name : '' ?></td>
                    <td class="number">4</td>
                    <td><?= $inquiryPrice ? $inquiryPrice->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryPrice ? $inquiryPrice->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryPrice ? $inquiryPrice->tax_price : 0 ?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->delivery_time : 0 ?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->admin_id ? $inquiryPrice->admin->username : '') : '' ?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->inquiry_datetime : '' ?></td>
                    <td><?= $inquiryPrice ? Inquiry::$better[$inquiryPrice->is_better] : ''?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->better_reason : ''?></td>
                    <td><?= $inquiryPrice ? $inquiryPrice->remark : ''?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->order_id ? $inquiryPrice->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryPrice ? ($inquiryPrice->order_inquiry_id ? $inquiryPrice->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryTime ? $inquiryTime->id : ''?>"></td>
                    <td>同期最短</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryTime ? $inquiryTime->supplier->name : '' ?></td>
                    <td class="number">4</td>
                    <td><?= $inquiryTime ? $inquiryTime->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryTime ? $inquiryTime->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryTime ? $inquiryTime->tax_price : 0 ?></td>
                    <td><?= $inquiryTime ? $inquiryTime->delivery_time : 0 ?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->admin_id ? $inquiryTime->admin->username : '') : '' ?></td>
                    <td><?= $inquiryTime ? $inquiryTime->inquiry_datetime : '' ?></td>
                    <td><?= $inquiryTime ? Inquiry::$better[$inquiryTime->is_better] : ''?></td>
                    <td><?= $inquiryTime ? $inquiryTime->better_reason : ''?></td>
                    <td><?= $inquiryTime ? $inquiryTime->remark : ''?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->order_id ? $inquiryTime->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryTime ? ($inquiryTime->order_inquiry_id ? $inquiryTime->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryNew ? $inquiryNew->id : ''?>"></td>
                    <td>最新报价</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryNew ? $inquiryNew->supplier->name : '' ?></td>
                    <td class="number">4</td>
                    <td><?= $inquiryNew ? $inquiryNew->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryNew ? $inquiryNew->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryNew ? $inquiryNew->tax_price : 0 ?></td>
                    <td><?= $inquiryNew ? $inquiryNew->delivery_time : 0 ?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->admin_id ? $inquiryNew->admin->username : '') : '' ?></td>
                    <td><?= $inquiryNew ? $inquiryNew->inquiry_datetime : '' ?></td>
                    <td><?= $inquiryNew ? Inquiry::$better[$inquiryNew->is_better] : ''?></td>
                    <td><?= $inquiryNew ? $inquiryNew->better_reason : ''?></td>
                    <td><?= $inquiryNew ? $inquiryNew->remark : ''?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->order_id ? $inquiryNew->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryNew ? ($inquiryNew->order_inquiry_id ? $inquiryNew->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="0" data-select_id="<?=$inquiryBetter ? $inquiryBetter->id : ''?>"></td>
                    <td>优选记录</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->supplier->name : '' ?></td>
                    <td class="number">4</td>
                    <td><?= $inquiryBetter ? $inquiryBetter->tax_rate : 0 ?></td>
                    <td class="price"><?= $inquiryBetter ? $inquiryBetter->price : 0 ?></td>
                    <td class="tax_price"><?= $inquiryBetter ? $inquiryBetter->tax_price : 0 ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->delivery_time : 0 ?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->admin_id ? $inquiryBetter->admin->username : '') : '' ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->inquiry_datetime : '' ?></td>
                    <td><?= $inquiryBetter ? Inquiry::$better[$inquiryBetter->is_better] : ''?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->better_reason : ''?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->remark : ''?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->order_id ? $inquiryBetter->order->order_sn : '') : '' ?></td>
                    <td><?= $inquiryBetter ? ($inquiryBetter->order_inquiry_id ? $inquiryBetter->orderInquiry->inquiry_sn : '') : '' ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$goods ? $goods->id : ''?>"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                </thead>
            </table>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th rowspan="2">库存记录</th>
                    <th></th>
                    <th>类型</th>
                    <th>零件号</th>
                    <th>单位</th>
                    <th>供应商</th>
                    <th>数量</th>
                    <th>税率</th>
                    <th>未税单价</th>
                    <th>含税单价</th>
                    <th>库存位置</th>
                    <th>是否紧急</th>
                    <th>建议库存</th>
                    <th>高储</th>
                    <th>低储</th>
                    <th>未税总价</th>
                    <th>含税总价</th>
                    <th>操作</th>
                </tr>
                <tr class="inquiry_list">
                    <td><input type="radio" name="relevance" class="relevance" data-type="1" data-select_id="<?=$stock ? $stock->id : ''?>"></td>
                    <td>库存记录</td>
                    <td><?= $goods ? $goods->goods_number : '' ?></td>
                    <td><?= $goods ? $goods->unit : '' ?></td>
                    <td><?= $stock ? $stock->supplier->name : '' ?></td>
                    <td class="number"><?= $stock ? $stock->number : 0 ?></td>
                    <td><?= $stock ? $stock->tax_rate : 0 ?></td>
                    <td class="price"><?= $stock ? $stock->price : 0 ?></td>
                    <td class="tax_price"><?= $stock ? $stock->tax_price : 0 ?></td>
                    <td><?= $stock ? $stock->position : 0 ?></td>
                    <td><?= $stock ? Stock::$emerg[$stock->is_emerg] : '' ?></td>
                    <td><?= $stock ? $stock->suggest_number : 0 ?></td>
                    <td><?= $stock ? $stock->high_number : 0 ?></td>
                    <td><?= $stock ? $stock->low_number : 0 ?></td>
                    <td class="all_price"></td>
                    <td class="all_tax_price"></td>
                    <td>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=stock/create&goods_id=<?=$goods ? $goods->id : ''?>"><i class="fa fa-plus"></i>添加记录</a>
                    </td>
                </tr>
                </thead>
            </table>
        </div>
        <div class="box-footer">
            <?= Html::button('关联最终订单', [
                    'class' => 'btn btn-success relevance_save',
                    'name'  => 'submit-button']
            )?>
        </div>
    </div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.inquiry_list').each(function (i, e) {
            var number = $(e).find('.number').text();
            var price = $(e).find('.price').text();
            var tax_price = $(e).find('.tax_price').text();
            $(e).find('.all_price').text(number * price);
            $(e).find('.all_tax_price').text(number * tax_price);
        });

        $('.relevance_save').click(function (e) {
            var a = $("[name=relevance]:checked").val();
            if (!a) {
                layer.msg('请选择一个记录关联', {time:2000});
                return false;
            }
            var type      = '';
            var select_id = 0;
            $('.relevance').each(function (i, element) {
                if ($(this).is(":checked")) {
                    type      = $(element).data("type");
                    select_id = $(element).data("select_id");
                }
            });
            if (!select_id) {
                layer.msg('请先添加记录', {time:2000});
                return false;
            }
            var goods_id = $('.data').data('goods_id');
            var order_id = $('.data').data('order_id');
            var key      = $('.data').data('key');

            $.ajax({
                type:"post",
                url:'?r=order-final/relevance',
                data:{type:type, select_id:select_id, goods_id:goods_id, order_id:order_id, key:key},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order/create-final&id=" + order_id + '&key=' + key);
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });

    });
</script>

