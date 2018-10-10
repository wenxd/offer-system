<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = '询价单详情';
$this->params['breadcrumbs'][] = ['label' => '询价单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content">
<div class="box table-responsive">
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>询价单号</th>
                <th>订单号</th>
                <th>零件号</th>
                <th>是否询价</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiryGoods as $item):?>
                <tr>
                    <td><?=$orderInquiry->inquiry_sn?></td>
                    <td><?=$orderInquiry->order->order_sn?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <td><?=$item::$Inquiry[$item->is_inquiry]?></td>
                    <td>
                        <a class="btn btn-success btn-xs btn-flat" href="?r=order-inquiry/confirm&id=<?=$item->id?>" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
</div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">

</script>
