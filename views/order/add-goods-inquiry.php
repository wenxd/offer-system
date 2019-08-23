<?php

use app\models\SystemConfig;
use app\models\Goods;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = '非项目订单添加零件';
$this->params['breadcrumbs'][] = $this->title;

//获取税率
$tax_rate = SystemConfig::find()->select('value')->where([
    'title'  => SystemConfig::TITLE_TAX,
    'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
?>
<!-- Main content -->
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>零件号</th>
                            <th>厂家号</th>
                            <th>原厂家</th>
                            <th>单位</th>
                            <th>税率</th>
                            <th>加工</th>
                            <th>特制</th>
                            <th>图片</th>
                            <th>数量</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($goodsList as $key => $goods):?>
                            <tr class="goods_list" data-id="<?=$goods->id?>">
                                <td class="serialNumber"><input type="text" style="width: 50px;" value="<?=$key+1?>"/></td>
                                <td><?=$goods['goods_number']?></td>
                                <td><?=$goods['goods_number_b']?></td>
                                <td><?=$goods['original_company']?></td>
                                <td><?=$goods['unit']?></td>
                                <td><?=$tax_rate?></td>
                                <td><?=Goods::$process[$goods->is_process]?></td>
                                <td><?=Goods::$special[$goods->is_special]?></td>
                                <td><img src="<?=printf('%s/%s', Yii::$app->params['img_url_prefix'], $goods->img_id)?>" width="50px"></td>
                                <td><input type="text" value="1" class="number"></td>
                                <td><button type="button" class="btn btn-danger" onclick="deleted(this)">删除</button></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <?= Html::button('保存订单', [
                        'class' => 'btn btn-success order_save',
                        'name'  => 'button']
                )?>
                <?= Html::a('<i class="fa fa-reply"></i> 返回上一页', Url::to(['order/create', 'temp_id' => $_GET['temp_id'] ?? '']), [
                    'class' => 'btn btn-default btn-flat',
                ])?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">

    function deleted(obj) {
        $(obj).parent().parent().remove();
    }

    $('.order_save').click(function (e) {
        var goods     = $('.goods_list');
        var goodsIds  = [];
        var goodsInfo = [];
        goods.each(function (i, e) {
            var item = {};
            goodsIds.push($(e).data('id'));
            item.serial   = $(e).find('.serialNumber input').val();
            item.goods_id = $(e).data('id');
            item.number   = $(e).find('.number').val();
            goodsInfo.push(item);
        });

        var order_sn    = "<?=$_GET['order_sn']?>";
        var customer_id = "<?=$_GET['customer_id']?>";
        var manage_name = "<?=$_GET['manage_name']?>";
        var created_at  = "<?=$_GET['created_at']?>";

        $.ajax({
            type:"post",
            url:'?r=order/save-inquiry-order',
            data:{goodsIds:goodsIds, goodsInfo:goodsInfo, order_sn:order_sn, customer_id:customer_id,
                manage_name:manage_name, created_at:created_at},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    location.replace("?r=order/index");
                } else {
                    layer.msg(res.msg, {time:2000});
                    return false;
                }
            }
        });
    });

</script>
