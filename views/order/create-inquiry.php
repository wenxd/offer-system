<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
use app\models\Goods;

$this->title = '生成询价单';
$this->params['breadcrumbs'][] = $this->title;

$inquiryYes = [];
if ($orderInquiry) {
    foreach ($orderInquiry as $k => $item) {
        $goods_info = json_decode($item['goods_info'], true);
        foreach ($goods_info as $g) {
            if ($g['is_inquiry']) {
                $inquiryYes[] = $g['goods_id'];
            }
        }
    }
}

?>
<section class="content">
    <div class="box table-responsive">
        <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="select_all" class="select_all"></th>
                        <th>零件号</th>
                        <th>中文描述</th>
                        <th>英文描述</th>
                        <th>原厂家</th>
                        <th>原厂家备注</th>
                        <th>单位</th>
                        <th>是否加工</th>
                        <th>是否特制</th>
                        <th>是否铭牌</th>
                        <th>更新时间</th>
                        <th>创建时间</th>
                        <th>技术备注</th>
                        <th>是否有询价单</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goods as $key => $good):?>
                    <tr>
                        <td><?= in_array($good->id, $inquiryYes) ? '' : "<input type='checkbox' name='select_id' value={$good->id} class='select_id'>" ?></td>
                        <td><?= $good->goods_number?></td>
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
                        <td><?= in_array($good->id, $inquiryYes) ? $good->inquirySn->inquiry_sn : ''?></td></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</section>
