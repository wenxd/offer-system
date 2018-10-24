<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\Goods;

$this->title = '生成最终订单';
$this->params['breadcrumbs'][] = $this->title;

$inquiry_goods_ids = ArrayHelper::getColumn($finalGoods, 'goods_id');
$goods_id = ArrayHelper::getColumn($goods, 'id');

?>
<section class="content">
    <div class="box table-responsive">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr class="goods" data-goods_ids="<?=json_encode($goods_id)?>" data-order_id="<?=$_GET['id']?>" data-key="<?=$_GET['key']?>">
                        <th>零件号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>原厂家备注</th>
                        <th>单位</th>
                        <th>加工</th>
                        <th>特制</th>
                        <th>铭牌</th>
                        <th>更新时间</th>
                        <th>创建时间</th>
                        <th>技术备注</th>
                        <th>关联询价记录</th>
                        <th>询价ID</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goods as $key => $good):?>
                    <tr>
                        <td><?= Html::a($good->goods_number, Url::to(['inquiry/search', 'goods_id' => $good->id, 'order_id' => ($_GET['id'] ?? ''), 'key' => ($_GET['key'] ?? '')]));?></td>
                        <td><?= $good->description?></td>
                        <td><?= $good->description_en?></td>
                        <td><?= $good->original_company?></td>
                        <td><?= $good->original_company_remark?></td>
                        <td><?= $good->unit?></td>
                        <td><?= Goods::$process[$good->is_process]?></td>
                        <td><?= Goods::$special[$good->is_special]?></td>
                        <td><?= Goods::$nameplate[$good->is_nameplate]?></td>
                        <td><?= $good->updated_at?></td>
                        <td><?= $good->created_at?></td>
                        <td><?= $good->technique_remark?></td>
                        <td class="relevance"><?= in_array($good->id, $inquiry_goods_ids) ? '是' : '否'?></td>
                        <td><?= isset($finalGoods[$good->id]) ? Html::a($finalGoods[$good->id]['relevance_id'], Url::to(['inquiry/view', 'id' => $finalGoods[$good->id]['relevance_id']])) : ''?></td>
                        <td><?= Html::a('<i class="fa fa-paper-plane-o"></i> 关联询价记录',
                                Url::to(['inquiry/search', 'goods_id' => $good->id, 'order_id' => ($_GET['id'] ?? ''), 'key' => ($_GET['key'] ?? '')]),
                                ['class' => 'btn btn-primary btn-xs btn-flat']
                            );?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="box-footer">
            <?= Html::button('保存最终订单', [
                    'class' => 'btn btn-success final_save',
                    'name'  => 'submit-button']
            )?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //保存最终订单
        $('.final_save').click(function (e) {
            var flag = false;
            $('.relevance').each(function (i, element) {
                if ($(element).text() == '否') {
                    flag = true;
                }
            });
            if (flag) {
                layer.msg('所有的零件需关联询价', {time:2000});
                return false;
            }

            var goods_ids = $('.goods').data('goods_ids');
            var order_id  = $('.goods').data('order_id');
            var key       = $('.goods').data('key');

            $.ajax({
                type:"post",
                url:'?r=order-final/save-order',
                data:{order_id:order_id, goods_ids:goods_ids, key:key},
                dataType:'JSON',
                success:function(res){
                    if (res && res.code == 200){
                        layer.msg(res.msg, {time:2000});
                        location.replace("?r=order-final/index");
                    } else {
                        layer.msg(res.msg, {time:2000});
                        return false;
                    }
                }
            });
        });
    });
</script>
