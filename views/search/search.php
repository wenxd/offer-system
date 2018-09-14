<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Goods;

$this->title = '搜索结果列表';
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
            <span style="text-align: center; display: block;"><b>零件基础信息</b></span>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>零件号</th>
                    <th>描述</th>
                    <th>原厂家</th>
                    <th>原厂家备注</th>
                    <th>单位</th>
                    <th>技术备注</th>
                    <th>是否加工</th>
                    <th>更新时间</th>
                    <th>创建时间</th>
                </tr>
                </thead>
                <?php if ($goods):?>
                <tbody>
                    <tr>
                        <td><?=$goods->goods_number?></td>
                        <td><?=$goods->description?></td>
                        <td><?=$goods->original_company?></td>
                        <td><?=$goods->original_company_remark?></td>
                        <td><?=$goods->unit?></td>
                        <td><?=$goods->technique_remark?></td>
                        <td><?=Goods::$process[$goods->is_process]?></td>
                        <td><?=$goods->updated_at?></td>
                        <td><?=$goods->created_at?></td>
                    </tr>
                </tbody>
                <?php endif; ?>
            </table>
            <br>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>零件号</th>
                        <th>是否最新</th>
                        <th>是否优选</th>
                        <th>商品类型</th>
                        <th>税率</th>
                        <th>未税价格</th>
                        <th>含税价格</th>
                        <th>库存数量</th>
                        <th>询价时间</th>
                        <th>供应商ID</th>
                        <th>供应商名称</th>
                        <th>购买数量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="13" style="text-align: center;">最新询价记录</td>
                </tr>
                <?php foreach ($inquiryNewest as $key => $value):?>
                    <tr>
                        <td><?=$value->goods->goods_number?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['tax_rate']?></td>
                        <td><?=$value['price']?></td>
                        <td><?=$value['tax_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=$value['supplier_id'] ? Supplier::getAllDropDown()[$value['supplier_id']] : ''?></td>
                        <td><input type="text" class="number"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-flat" href="javascript:void(0);" onclick="addList($(this))"
                            data-inquiry-id="<?=$value['id']?>" data-type="1" data-goods_id="<?=$value['good_id']?>"
                               data-quotation_price="<?=$value['tax_price']?>"><i class="fa fa-inbox"></i> 加入报价单</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="13" style="text-align: center;">优选记录</td>
                </tr>
                <?php foreach ($inquiryBetter as $key => $value):?>
                    <tr>
                        <td><?=$value->goods->goods_number?></td>
                        <td><?=Inquiry::$newest[$value['is_newest']]?></td>
                        <td><?=Inquiry::$better[$value['is_better']]?></td>
                        <td>询价商品</td>
                        <td><?=$value['tax_rate']?></td>
                        <td><?=$value['price']?></td>
                        <td><?=$value['tax_price']?></td>
                        <td>无限多</td>
                        <td><?=$value['inquiry_datetime']?></td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=Supplier::getAllDropDown()[$value['supplier_id']]?></td>
                        <td><input type="text" class="number"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-flat" href="javascript:void(0);" onclick="addList($(this))"
                               data-inquiry-id="<?=$value['id']?>" data-type="2" data-goods_id="<?=$value['good_id']?>"
                               data-quotation_price="<?=$value['tax_price']?>"><i class="fa fa-inbox"></i> 加入报价单</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="13" style="text-align: center;">本地库存零件记录</td>
                </tr>
                <?php foreach ($stockList as $key => $value):?>
                    <tr>
                        <td><?=$value->goods->goods_number?></td>
                        <td>无</td>
                        <td>无</td>
                        <td>库存商品</td>
                        <td><?=$value['tax_rate']?></td>
                        <td><?=$value['price']?></td>
                        <td><?=$value['tax_price']?></td>
                        <td><?=$value['number']?></td>
                        <td>无</td>
                        <td><?=$value['supplier_id']?></td>
                        <td><?=Supplier::getAllDropDown()[$value['supplier_id']]?></td>
                        <td><input type="text" class="number"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-flat" href="javascript:void(0);" onclick="addList($(this))"
                               data-inquiry-id="<?=$value['id']?>" data-type="3" data-goods_id="<?=$value['good_id']?>"
                               data-quotation_price="<?=$value['tax_price']?>"><i class="fa fa-inbox"></i> 加入报价单</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <?= LinkPager::widget([
                'pagination' => $pages,
                'firstPageLabel' => '首页',
                'lastPageLabel' => '尾页',
                'nextPageLabel' => '下一页',
                'prevPageLabel' => '上一页',
                'maxButtonCount' => 10,
            ]); ?>
        </div>
        <div class="box-footer but">
            <a class="btn btn-success" href="?r=cart/list">查看报价单</a>
            <a class="btn btn-default btn-flat" href="?r=search/index"><i class="fa fa-reply"></i> 继续添加</a>
        </div>
    </div>
</section>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    function addList(obj){
        var goods_id        = obj.data("goods_id");
        var quotation_price = obj.data("quotation_price");
        var inquiryId       = obj.data("inquiry-id");
        var number          = obj.parent().parent().find('.number').val();
        var type            = obj.data("type");

        var reg = /^[0-9]*$/;
        if (!reg.test(number) || number <= 0) {
            layer.msg('数量请输入正整数', {time:2000});
            return false;
        }

        $.ajax({
            type:"post",
            url:"?r=cart/add-list",
            data:{inquiryId:inquiryId, type:type, number:number,goods_id:goods_id,quotation_price:quotation_price},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg('加入成功', {time:2000});
                } else {
                    layer.msg(res.msg, {time:2000});
                }
            }
        })

    }
</script>
