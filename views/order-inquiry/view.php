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
                <th width="300px">操作</th>
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
                        <?php if (!$item->is_inquiry):?>
                            <a class="btn btn-success btn-xs btn-flat confirm" data-id="<?=$item->id?>" href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                        <?php endif;?>
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
    $(document).ready(function () {
        $('.confirm').click(function (e) {
            var id = $(this).data('id');
            $.ajax({
                type:"get",
                url:'?r=order-inquiry/confirm',
                data:{id:id},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:1000}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
