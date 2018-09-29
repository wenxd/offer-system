<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Inquiry;
use app\models\Supplier;
use app\models\Goods;

$this->title = '零件管理';
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
                    <td><?=$goods ? $goods->goods_number : ''?></td>
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
                    <th>零件号</th>
                    <th>单位</th>
                    <th>供应商</th>
                    <th style="width: 100px;">数量</th>
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
                </tr>
                <tr>
                    <td>价格最优</td>
                    <td><?= $inquiryBetter ? $inquiryBetter->goods->goods_number : '' ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->goods->unit : '' ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->supplier->name : '' ?></td>
                    <td><input type="text" class="number" style="width: 80px;"></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->tax_rate : 0 ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->price : 0 ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->tax_price : 0 ?></td>
                    <td><?= $inquiryBetter ? $inquiryBetter->offer_date : 0 ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>同期最短</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>最新报价</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>优选记录</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

