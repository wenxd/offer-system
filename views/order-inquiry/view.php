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
$userId    = Yii::$app->user->identity->id;
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
                <th>序号</th>
                <th>询价单号</th>
                <?php if(!in_array($userId, $adminIds)):?>
                <th>订单号</th>
                <th>零件号</th>
                <?php endif;?>
                <th>厂家号</th>
                <th>原厂家</th>
                <th>原厂家备注</th>
                <th>中文描述</th>
                <th>英文描述</th>
                <th>单位</th>
                <th>铭牌照片</th>
                <th>加工照片</th>
                <th>数量</th>
                <th>询价</th>
                <th>询价数量</th>
                <th>寻不出原因</th>
                <th width="300px">操作</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiryGoods as $item):?>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <tr <?=(!$item->is_inquiry&& !$orderInquiry->is_inquiry && (strtotime($item->orderInquiry->end_date) - time()) < 3600 * 24) ? 'class="alarm"' : ''?>>
                            <td><?=$item->serial?></td>
                            <td><?=$orderInquiry->inquiry_sn?></td>
                            <?php if(!in_array($userId, $adminIds)):?>
                            <td><?=$orderInquiry->order->order_sn?></td>
                            <td><?=$item->goods->goods_number?><?=Html::a(' 询价记录', Url::to(['inquiry/index', 'InquirySearch[goods_number]' => $item->goods->goods_number]))?></td>
                            <?php endif;?>
                            <td><?=$item->goods->goods_number_b?></td>
                            <td><?=$item->goods->original_company?></td>
                            <td><?=$item->goods->original_company_remark?></td>
                            <td><?=$item->goods->description?></td>
                            <td><?=$item->goods->description_en?></td>
                            <td><?=$item->goods->unit?></td>
                            <td><?=Html::img($item->goods->nameplate_img_url, ['width' => '100px'])?></td>
                            <td><?=Html::img($item->goods->img_url, ['width' => '100px'])?></td>
                            <td><?=$item->number?></td>
                            <td><?=isset($inquiryList[$item->goods_id]) ? '是' : '否'?></td>
                            <td><?=isset($inquiryList[$item->goods_id]) ? count($inquiryList[$item->goods_id]) : 0?></td>
                            <td><?=$item->reason?></td>
                            <td>
                                <?php if (!isset($inquiryList[$item->goods_id]) || !$item->is_inquiry):?>
                                    <a class="btn btn-success btn-xs btn-flat confirm" data-id="<?=$item->id?>" href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                                    <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$item->goods_id?>&inquiry_goods_id=<?=$item->id?>" target="_blank" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
                                <?php endif;?>
                                <?php if (!isset($inquiryList[$item->goods_id]) && !$item->is_result):?>
                                    <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)" onclick="reasons(this)" data-id="<?=$item->id?>"><i class="fa fa-question"></i> 询不出</a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php if (!$item->is_inquiry):?>
                            <tr <?=(!$item->is_inquiry&& !$orderInquiry->is_inquiry && (strtotime($item->orderInquiry->end_date) - time()) < 3600 * 24) ? 'class="alarm"' : ''?>>
                                <td><?=$item->serial?></td>
                                <td><?=$orderInquiry->inquiry_sn?></td>
                                <?php if(!in_array($userId, $adminIds)):?>
                                    <td><?=$orderInquiry->order->order_sn?></td>
                                    <td><?=$item->goods->goods_number?><?=Html::a(' 询价记录', Url::to(['inquiry/index', 'InquirySearch[goods_number]' => $item->goods->goods_number]))?></td>
                                <?php endif;?>
                                <td><?=$item->goods->goods_number_b?></td>
                                <td><?=$item->goods->original_company?></td>
                                <td><?=$item->goods->original_company_remark?></td>
                                <td><?=$item->goods->description?></td>
                                <td><?=$item->goods->description_en?></td>
                                <td><?=$item->goods->unit?></td>
                                <td><?=Html::img($item->goods->nameplate_img_url, ['width' => '100px'])?></td>
                                <td><?=Html::img($item->goods->img_url, ['width' => '100px'])?></td>
                                <td><?=$item->number?></td>
                                <td><?=isset($inquiryList[$item->goods_id]) ? '是' : '否'?></td>
                                <td><?=isset($inquiryList[$item->goods_id]) ? count($inquiryList[$item->goods_id]) : 0?></td>
                                <td><?=$item->reason?></td>
                                <td>
                                    <?php if (!isset($inquiryList[$item->goods_id])):?>
                                        <a class="btn btn-success btn-xs btn-flat confirm" data-id="<?=$item->id?>" href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                                        <a class="btn btn-primary btn-xs btn-flat" href="?r=inquiry/create&goods_id=<?=$item->goods_id?>&inquiry_goods_id=<?=$item->id?>" target="_blank" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
                                    <?php endif;?>
                                    <?php if (!isset($inquiryList[$item->goods_id]) && !$item->is_result):?>
                                        <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)" onclick="reasons(this)" data-id="<?=$item->id?>"><i class="fa fa-question"></i> 询不出</a>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endif ;?>
                    <?php endif; ?>
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

    function reasons(obj) {
        var id = $(obj).data('id');
        layer.open({
            type: 1,
            title: '询不出原因',
            skin: 'layui-layer-rim', //加上边框
            area: ['500px', '240px'], //宽高
            content: '<form class="form-horizontal">\n' +
            '  <div class="form-group">\n' +
            '    <label for="reason" class="col-sm-2 control-label">原因</label>\n' +
            '    <div class="col-sm-10">\n' +
            '      <input type="text" class="form-control" id="reason">\n' +
            '    </div>\n' +
            '  </div>\n' +
            '  <div class="form-group">\n' +
            '    <div class="col-sm-offset-2 col-sm-10">\n' +
            '      <a class="btn btn-default" href="javascript:void(0)" onclick="sure(' + id + ')">确定</a>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</form>'
        });
    }

    function sure(id) {
        var reason = $('#reason').val();
        if (!reason) {
            layer.msg('请输入原因', {time:2000});
            return false;
        }
        $.ajax({
            type:"post",
            url:"?r=order-inquiry/add-reason",
            data:{id:id, reason:reason},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200) {
                    window.location.reload();
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    }
</script>
