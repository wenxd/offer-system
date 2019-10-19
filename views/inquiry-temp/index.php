<?php

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\InquirySearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use app\models\Inquiry;
use app\models\Goods;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquiryTempSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '询价记录列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
$admins[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;

?>
<div class="box table-responsive">
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'admin_id',
                    'format'    => 'raw',
                    'label'     => '询价员',
                    'contentOptions' =>['style'=>'min-width: 100px;'],
                    'filter'    => $admins,
                    'value'     => function ($model, $key, $index, $column) {
                        return $model->admin ? $model->admin->username : '';
                    }
                ],
                [
                    'attribute'      => 'goods_number_b',
                    'format'         => 'raw',
                    'label'          => '厂家号',
                    'contentOptions' =>['style'=>'min-width: 100px;'],
                    'filter'         => Html::activeTextInput($searchModel, 'goods_number_b',['class'=>'form-control']),
                    'value'          => function ($model, $key, $index, $column) {
                        if ($model->goods) {
                            return Html::a($model->goods->goods_number_b, Url::to(['goods/view', 'id' => $model->goods->id]));
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute'      => 'original_company',
                    'label'          => '原厂家',
                    'contentOptions' =>['style'=>'min-width: 100px;'],
                    'filter'         => Html::activeTextInput($searchModel, 'original_company',['class'=>'form-control']),
                    'value'          => function ($model, $key, $index, $column) {
                        if ($model->goods) {
                            return $model->goods->original_company;
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'supplier_name',
                    'format'    => 'raw',
                    'label'     => '供应商名称',
                    'filter'    => Html::activeTextInput($searchModel, 'supplier_name',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->supplier) {
                            return $model->supplier->name;
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'delivery_time',
                    'contentOptions'=>['style'=>'min-width: 80px;']
                ],
                'tax_price',
                [
                    'attribute' => 'unit',
                    'label'     => '单位',
                    'contentOptions'=>['style'=>'min-width: 70px;'],
                    'filter'    => Html::activeTextInput($searchModel, 'unit',['class'=>'form-control']),
                    'value'     => function ($model, $key, $index, $column) {
                        if ($model->goods) {
                            return $model->goods->unit;
                        } else {
                            return '';
                        }
                    }
                ],
                'number',
                'all_tax_price',
                'tax_rate',
                [
                    'attribute' => 'is_better',
                    'contentOptions' =>['style'=>'min-width: 80px;'],
                    'filter'    => Inquiry::$better,
                    'value'     => function ($model, $key, $index, $column) {
                        return Inquiry::$newest[$model->is_better];
                    }
                ],
                [
                    'attribute' => 'is_newest',
                    'contentOptions' =>['style'=>'min-width: 80px;'],
                    'filter'    => Inquiry::$newest,
                    'value'     => function ($model, $key, $index, $column) {
                        return Inquiry::$newest[$model->is_newest];
                    }
                ],
                [
                    'attribute' => 'inquiry_datetime',
                    'contentOptions'=>['style'=>'min-width: 150px;'],
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'InquiryTempSearch[inquiry_datetime]',
                        'value' => Yii::$app->request->get('InquiryTempSearch')['inquiry_datetime'] ?? '',
                    ]),
                    'value'     => function($model){
                        return substr($model->inquiry_datetime, 0, 10);
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    //上传导入逻辑
    //加载动画索引
    var index;
    //上传文件名称
    $.ajaxUploadSettings.name = 'FileName';

    //监听事件
    $('.upload').ajaxUploadPrompt({
        //上传地址
        url : '?r=inquiry/upload',
        //上传文件类型
        accept:'.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx',
        //上传前加载动画
        beforeSend : function () {
            layer.msg('上传中。。。', {
                icon: 16, shade: 0.01
            });
        },
        onprogress : function (e) {},
        error : function () {},
        success : function (data) {
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if(data.code == 200){
                //导入成功
                layer.msg(data.msg,{time:3000},function(){
                    window.location.reload();
                });
            }else{
                //失败提示
                layer.msg(data.msg,{icon:1});
            }
        }
    });
</script>
