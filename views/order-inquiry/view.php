<?php

use app\extend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use app\models\AuthAssignment;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInquiry */

$this->title = '询价单详情';
$this->params['breadcrumbs'][] = ['label' => '询价单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');
$userId = Yii::$app->user->identity->id;

$super_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '订单管理员']])->all();
$super_adminIds = ArrayHelper::getColumn($super_admin, 'user_id');
?>
<style>
    .alarm {
        background-color: #ffacac;
    }
</style>

<section class="content">
    <div class="box table-responsive">
        <div class="box-header">
            <div class="col-md-6">
                <?= Html::a('<i class="fa fa-download"></i> 询价单导出', Url::to(['download', 'id' => $orderInquiry->id]), [
                    'data-pjax' => '0',
                    'class' => 'btn btn-primary btn-flat',
                ]); ?>
                <?= Html::button('询价导入', [
                        'class' => 'btn btn-success upload',
                        'name' => 'submit-button']
                ) ?>
                <?php if (in_array($userId, $super_adminIds)): ?>
                    <?= Html::button('批量确认询价', [
                            'class' => 'btn btn-info all_confirm',
                            'name' => 'submit-button']
                    ) ?>
                <?php else: ?>
                    <?= Html::button('批量确认询价', [
                            'class' => 'btn btn-info all_confirm_inquiry',
                            'style' => 'display: none',
                            'name' => 'submit-button']
                    ) ?>
                <?php endif; ?>
                <?php if (!$orderInquiry->is_inquiry): ?>
                    <?= Html::button('批量退回', [
                            'class' => 'btn btn-danger',
                            'onclick' => "redistribution_all({$orderInquiry->id})",
                            'name' => 'submit-button']
                    ) ?>
                <?php endif; ?>
            </div>

            <?php $form = ActiveForm::begin(['method' => 'get']); ?>
            <div class="col-md-2">
                <?= $form->field($model, 'is_inquiry')->dropDownList([0 => '未询价', 1 => '已询价'], ['prompt' => '询价状态'])->label(false) ?>
            </div>
            <?= Html::submitButton('搜索', [
                    'class' => 'btn btn-success',
                    'name' => 'submit-button']
            ); ?>
            <?php ActiveForm::end(); ?>
        </div>

        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover" style="width: 2000px; table-layout: auto">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>询价单号</th>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <th>订单号</th>
                        <th>品牌</th>
                        <th>零件号</th>
                    <?php endif; ?>
                    <th>厂家号</th>
                    <th>原厂家</th>
                    <th>技术备注</th>
                    <th>中文描述</th>
                    <th>英文描述</th>
                    <th>单位</th>
                    <th>数量</th>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <th>主零件</th>
                    <?php endif; ?>
                    <th>询价</th>
                    <th>总询价条目</th>
                    <th>我的询价条目</th>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <th>Ta的询价条目</th>
                    <?php endif; ?>
                    <th width="10%">澄清问题</th>
                    <th width="10%">澄清回复</th>
                    <th>原厂家备注</th>
                    <th>推荐供应商</th>
                    <th>特别说明</th>
                    <th>铭牌照片</th>
                    <th>加工照片</th>
                    <th width="300px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($inquiryGoods as $item): ?>
                    <?php if (!in_array($userId, $adminIds)): ?>
                        <tr class="order_inquiry_list" data-id="<?= $item->id ?>"
                            <?= (!$item->is_inquiry && !$orderInquiry->is_inquiry && (strtotime($item->orderInquiry->end_date) - time()) < 3600 * 24) ? 'class="alarm"' : '' ?>>
                            <td><?= $item->serial ?></td>
                            <td><?= $orderInquiry->inquiry_sn ?></td>
                            <?php if (!in_array($userId, $adminIds)): ?>
                                <td><?= $orderInquiry->order->order_sn ?></td>
                                <td><?= $item->goods->material_code ?></td>
                                <td><?= $item->goods->goods_number ?><?= Html::a(' 询价记录', Url::to(['inquiry/index', 'InquirySearch[goods_number]' => $item->goods->goods_number])) ?></td>
                            <?php endif; ?>
                            <td><?= $item->goods->goods_number_b ?><?= Html::a(' 询价记录', Url::to(['inquiry-temp/inquiry', 'id' => $item->goods_id])) ?></td>
                            <td><?= $item->goods->original_company ?></td>
                            <td><?= $item->goods->technique_remark ?></td>
                            <td><?= $item->goods->description ?></td>
                            <td><?= $item->goods->description_en ?></td>
                            <td><?= $item->goods->unit ?></td>
                            <td><?= $item->number ?></td>
                            <?php if (!in_array($userId, $adminIds)): ?>
                                <td><?php
                                    $text = '';
                                    if (!empty($item->belong_to)) {
                                        foreach (json_decode($item->belong_to, true) as $key => $device) {
                                            $text .= $key . ':' . $device . '<br/>';
                                        }
                                    }
                                    echo $text;
                                    ?>
                                </td>
                            <?php endif; ?>

                            <td><?= isset($inquiryList[$item->goods_id]) ? '是' : '否' ?></td>
                            <td class="inquiry_number_all"><?= isset($inquiryList[$item->goods_id]) ? count($inquiryList[$item->goods_id]) : 0 ?></td>
                            <?php $inquiry_number = isset($inquiryMyList[$item->goods_id]) ? count($inquiryMyList[$item->goods_id]) : 0; ?>
                            <td class="inquiry_number"><?= $inquiry_number ?></td>
                            <?php if (!in_array($userId, $adminIds)): ?>
                                <?php $inquiry_ta_number = isset($user_inquiry_count[$item->goods_id]) ? count($user_inquiry_count[$item->goods_id]) : 0; ?>
                                <td class="inquiry_ta_number"><?= $inquiry_ta_number ?></td>
                            <?php endif; ?>
                            <td>
                                <?php
                                if ($item->clarify) {
                                    $text = '';
                                    if (!empty($item->clarify)) {
                                        foreach ($item->clarify as $device) {
                                            $text .= $device->reason . '<br/>';
                                        }
                                    }
                                    echo $text;
                                } else {
                                    echo $item->reason;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($item->clarify) {
                                    $text = '';
                                    if (!empty($item->clarify)) {
                                        foreach ($item->clarify as $device) {
                                            $text .= $device->clarify . '<br/>';
                                        }
                                    }
                                    echo $text;
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td><?= $item->goods->original_company_remark ?></td>
                            <td><?= $item->supplier ? $item->supplier->name : '' ?></td>
                            <td><?= $item->remark ?></td>
                            <td><a href="<?= $item->goods->nameplate_img_url ?>"
                                   target="_blank"><?= Html::img($item->goods->nameplate_img_url, ['width' => '100px']) ?></a>
                            </td>
                            <td><a href="<?= $item->goods->img_url ?>"
                                   target="_blank"><?= Html::img($item->goods->img_url, ['width' => '100px']) ?></a>
                            </td>
                            <td>
                                <a class="btn btn-success btn-xs btn-flat adminConfirm" data-id="<?= $item->id ?>"
                                   href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i> 确认询价完成</a>
                                <a class="btn btn-primary btn-xs btn-flat"
                                   href="?r=inquiry/add&goods_id=<?= $item->goods_id ?>&inquiry_goods_id=<?= $item->id ?>"
                                   target="_blank" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
                                <?php if (!isset($inquiryList[$item->goods_id]) || !$item->is_inquiry): ?>

                                <?php endif; ?>
                                <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)"
                                   onclick="reasons(this)" data-id="<?= $item->id ?>"><i class="fa fa-question"></i>
                                    澄清问题</a>
                                <?php if (!$item->is_inquiry && !$item->is_result): ?>

                                <?php endif; ?>
                                <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)"
                                   onclick="redistribution(this)" data-id="<?= $item->id ?>">重新派送</a>
                                <?php if (!$inquiry_number) : ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <!--if (!$item->is_inquiry)-->
                        <?php if (1): ?>
                            <tr class="order_inquiry_list" data-id="<?= $item->id ?>"
                                <?= (!$item->is_inquiry && !$orderInquiry->is_inquiry && (strtotime($item->orderInquiry->end_date) - time()) < 3600 * 24) ? 'class="alarm"' : '' ?>>
                                <td><?= $item->serial ?></td>
                                <td><?= $orderInquiry->inquiry_sn ?></td>
                                <?php if (!in_array($userId, $adminIds)): ?>
                                    <td><?= $orderInquiry->order->order_sn ?></td>
                                    <td><?= $item->goods->goods_number ?><?= Html::a(' 询价记录', Url::to(['inquiry/index', 'InquirySearch[goods_number]' => $item->goods->goods_number])) ?></td>
                                <?php endif; ?>
                                <td><?= $item->goods->goods_number_b ?><?= Html::a(' 询价记录', Url::to(['inquiry-temp/inquiry', 'id' => $item->goods_id])) ?></td>
                                <td><?= $item->goods->original_company ?></td>
                                <td><?= $item->goods->technique_remark ?></td>
                                <td><?= $item->goods->description ?></td>
                                <td><?= $item->goods->description_en ?></td>
                                <td><?= $item->goods->unit ?></td>

                                <td><?= $item->number ?></td>
                                <?php if (!in_array($userId, $adminIds)): ?>
                                    <td><?php
                                        $text = '';
                                        if (!empty($item->belong_to)) {
                                            foreach (json_decode($item->belong_to, true) as $key => $device) {
                                                $text .= $key . ':' . $device . '<br/>';
                                            }
                                        }
                                        echo $text;
                                        ?>
                                    </td>
                                <?php endif; ?>
                                <td><?= isset($inquiryList[$item->goods_id]) ? '是' : '否' ?></td>
                                <td class="inquiry_number_all"><?= isset($inquiryList[$item->goods_id]) ? count($inquiryList[$item->goods_id]) : 0 ?></td>
                                <?php $inquiry_number = isset($inquiryMyList[$item->goods_id]) ? count($inquiryMyList[$item->goods_id]) : 0; ?>
                                <td class="inquiry_number"><?= $inquiry_number ?></td>
                                <?php if (!in_array($userId, $adminIds)): ?>
                                    <?php $inquiry_ta_number = isset($user_inquiry_count[$item->goods_id]) ? count($user_inquiry_count[$item->goods_id]) : 0; ?>
                                    <td class="inquiry_ta_number"><?= $inquiry_ta_number ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php
                                    if ($item->clarify) {
                                        $text = '';
                                        if (!empty($item->clarify)) {
                                            foreach ($item->clarify as $device) {
                                                $text .= $device->reason . '<br/>';
                                            }
                                        }
                                        echo $text;
                                    } else {
                                        echo $item->reason;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($item->clarify) {
                                        $text = '';
                                        if (!empty($item->clarify)) {
                                            foreach ($item->clarify as $device) {
                                                $text .= $device->clarify . '<br/>';
                                            }
                                        }
                                        echo $text;
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <td><?= $item->goods->original_company_remark ?></td>
                                <td><?= $item->supplier ? $item->supplier->name : '' ?></td>
                                <td><?= $item->remark ?></td>
                                <td><a href="<?= $item->goods->nameplate_img_url ?>"
                                       target="_blank"><?= Html::img($item->goods->nameplate_img_url, ['width' => '100px']) ?></a>
                                </td>
                                <td><a href="<?= $item->goods->img_url ?>"
                                       target="_blank"><?= Html::img($item->goods->img_url, ['width' => '100px']) ?></a>
                                </td>
                                <td>

                                    <a class="btn btn-primary btn-xs btn-flat"
                                       href="?r=inquiry/add&goods_id=<?= $item->goods_id ?>&inquiry_goods_id=<?= $item->id ?>"
                                       target="_blank" data-pjax="0"><i class="fa fa-plus"></i> 添加询价记录</a>
                                    <?php if (!isset($inquiryList[$item->goods_id]) || !$item->is_inquiry): ?>
                                        <a class="btn btn-success btn-xs btn-flat confirm" data-id="<?= $item->id ?>"
                                           href="javascript:void(0);" data-pjax="0"><i class="fa fa-hand-pointer-o"></i>
                                            确认询价完成</a>
                                        <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)"
                                           onclick="redistribution(this)" data-id="<?= $item->id ?>">重新派送</a>
                                    <?php endif; ?>
                                    <a class="btn btn-info btn-xs btn-flat" href="javascript:void(0)"
                                       onclick="reasons(this)" data-id="<?= $item->id ?>"><i class="fa fa-question"></i>
                                        澄清问题</a>
                                    <?php if (!$item->is_inquiry && !$item->is_result): ?>

                                    <?php endif; ?>

                                    <?php if (!$inquiry_number) : ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
</section>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //批量确认询价(超管)
        $('.all_confirm').click(function (e) {
            var all_conform = false;
            var ids = [];
            $('.order_inquiry_list').each(function (i, e) {
                var inquiry_number_all = parseInt($(e).find('.inquiry_number_all').text());
                if (!inquiry_number_all) {
                    all_conform = true;
                }
                var id = $(e).data('id');
                ids.push(id);
            });
            if (all_conform) {
                layer.msg('请先添加询价记录', {time: 2000});
                return;
            }
            $.ajax({
                type: "POST",
                url: '?r=order-inquiry/confirm-all',
                data: {ids: ids},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 1000}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        });

        //批量询价(询价员)
        $('.all_confirm_inquiry').click(function (e) {
            var all_conform = false;
            var ids = [];
            $('.order_inquiry_list').each(function (i, e) {
                var inquiry_number = parseInt($(e).find('.inquiry_number').text());
                if (!inquiry_number) {
                    all_conform = true;
                }
                var id = $(e).data('id');
                ids.push(id);
            });
            if (all_conform) {
                layer.msg('请先添加询价记录', {time: 2000});
                return;
            }
            $.ajax({
                type: "POST",
                url: '?r=order-inquiry/confirm-all',
                data: {ids: ids},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 1000}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        });

        //询价员
        $('.confirm').click(function (e) {
            var inquiry_number = $(this).parent().parent().find('.inquiry_number').text();
            if (0 == Number(inquiry_number)) {
                layer.msg('请先添加询价记录', {time: 2000});
                return;
            }
            var id = $(this).data('id');
            $.ajax({
                type: "get",
                url: '?r=order-inquiry/confirm',
                data: {id: id},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 1000}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        });

        //超级管理员
        $('.adminConfirm').click(function (e) {
            var inquiry_number = $(this).parent().parent().find('.inquiry_number_all').text();
            if (0 == Number(inquiry_number)) {
                layer.msg('请先添加询价记录', {time: 2000});
                return;
            }
            var id = $(this).data('id');
            $.ajax({
                type: "get",
                url: '?r=order-inquiry/confirm',
                data: {id: id},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 1000}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {time: 2000});
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
            title: '澄清问题原因',
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
                '      <a class="btn btn-default btn_sure" href="javascript:void(0)" onclick="sure(' + id + ')">确定</a>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</form>'
        });
    }

    function redistribution(obj) {
        var id = $(obj).data('id');
        $.ajax({
            type: "post",
            url: "?r=order-inquiry/redistribution",
            data: {id: id},
            dataType: 'JSON',
            success: function (res) {
                if (res && res.code == 200) {
                    window.location.href = '?r=order-inquiry';
                } else {
                    layer.msg(res.msg, {time: 2000});
                    return false;
                }
            }
        });
    }

    function redistribution_all(id) {
        layer.confirm('确认重新派送吗？', {
            btn: ['确认', '取消'] //按钮
        }, function () {
            $.ajax({
                type: "post",
                url: "?r=order-inquiry/redistribution-all",
                data: {id: id},
                dataType: 'JSON',
                success: function (res) {
                    layer.msg(res.msg, {time: 2000});
                    if (res && res.code == 200) {
                        window.location.href = "javascript:history.go(-1)";
                    }
                }
            });
        }, function () {
            layer.closeAll();
        });
    }

    function sure(id) {
        var reason = $('#reason').val();
        if (!reason) {
            layer.msg('请输入原因', {time: 2000});
            return false;
        }
        $(".btn_sure").attr("disabled", true).addClass("disabled");
        $.ajax({
            type: "post",
            url: "?r=order-inquiry/add-reason",
            data: {id: id, reason: reason},
            dataType: 'JSON',
            success: function (res) {
                if (res && res.code == 200) {
                    window.location.reload();
                } else {
                    $(".btn_sure").removeAttr("disabled").removeClass("disabled");
                    layer.msg(res.msg, {time: 2000});
                    return false;
                }
            }
        });
    }

    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url: '?r=order-inquiry/upload',
        //上传文件类型
        accept: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend: function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress: function (e) {
        },
        error: function () {
        },
        success: function (data) {
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                //导入成功
                layer.msg(data.msg, {time: 6000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 2});
            }
        }
    });
</script>
