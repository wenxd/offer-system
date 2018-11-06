<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use app\models\AuthAssignment;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = '询价单详情';
$this->params['breadcrumbs'][] = ['label' => '询价单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId   = Yii::$app->user->identity->id;
?>
<style>
    .alarm {
        background-color: #ffacac;
    }
</style>

<section class="content">
<div class="box table-responsive">
    <div class="box-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>询价单号</th>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>订单号</th>
                <th>零件号A</th>
                <?php endif;?>
                <th>零件号B</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>单位</th>
                <th>铭牌照片</th>
                <th>加工照片</th>
                <th>数量</th>
                <th>询价</th>
                <th width="300px">操作</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiryGoods as $item):?>
                <tr <?=(!$item->is_inquiry&& !$orderInquiry->is_inquiry && (strtotime($item->orderInquiry->end_date) - time()) < 3600 * 24) ? 'class="alarm"' : ''?>>
                    <td><?=$orderInquiry->inquiry_sn?></td>
                    <?php if(!in_array($userId, $adminIds)):?>
                    <td><?=$orderInquiry->order->order_sn?></td>
                    <td><?=$item->goods->goods_number?></td>
                    <?php endif;?>
                    <td><?=$item->goods->goods_number_b?></td>
                    <td><?=$item->goods->original_company?></td>
                    <td><?=$item->goods->original_company_remark?></td>
                    <td><?=$item->goods->description?></td>
                    <td><?=$item->goods->description_en?></td>
                    <td><?=$item->goods->unit?></td>
                    <td><?=Html::img($item->goods->nameplate_img_url, ['width' => '100px'])?></td>
                    <td><?=Html::img($item->goods->img_url, ['width' => '100px'])?></td>
                    <td><?=$orderGoods[$item->goods_id]->number?></td>
                    <td><?=$item::$Inquiry[$item->is_inquiry]?></td>
                    <td>
                        <?php if (!$item->is_inquiry):?>
                            <a class="btn btn-success btn-xs btn-flat confirm" data-id="<?=$item->id?>" href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                        <?php endif;?>
                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$item->goods_id?>&order_inquiry=1" target="_blank" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
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
