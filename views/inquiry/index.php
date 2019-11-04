<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use app\models\Inquiry;
use app\models\Goods;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InquirySearch */
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
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{index}',
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {delete} {index}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载模板', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload' => function () {
                    return Html::a('<i class="fa fa-upload"></i> 上传导入', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat upload',
                    ]);
                },
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-info btn-flat',
                    ]);
                }
            ]
        ])?>
    </div>
    <div class="box-body">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => CheckboxColumn::className(),
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'header' => '操作',
                'template' => '{view} {update}',
            ],
            'id',
            [
                'attribute' => 'admin_id',
                'format'    => 'raw',
                'visible'   => !in_array($userId, $adminIds),
                'label'     => '询价员',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'    => $admins,
                'value'     => function ($model, $key, $index, $column) {
                    return $model->admin ? $model->admin->username : '';
                }
            ],
            [
                'attribute'      => 'inquiry_sn',
                'format'         => 'raw',
                'label'          => '询价单号',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'inquiry_sn',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->orderInquiry) {
                        return Html::a($model->orderInquiry->inquiry_sn, Url::to(['order-inquiry/view', 'id' => $model->orderInquiry->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => 'goods_number',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Html::a($model->goods->goods_number, Url::to(['goods/view', 'id' => $model->goods->id]));
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute'      => 'goods_number_b',
                'format'         => 'raw',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'goods_number_b',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) use ($userId, $adminIds) {
                    if ($model->goods) {
                        if (in_array($userId, $adminIds)) {
                            return $model->goods->goods_number_b;
                        } else {
                            return Html::a($model->goods->goods_number_b, Url::to(['goods/view', 'id' => $model->goods->id]));
                        }
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
                'attribute'      => 'original_company_remark',
                'label'          => '原厂家备注',
                'contentOptions' =>['style'=>'min-width: 100px;'],
                'filter'         => Html::activeTextInput($searchModel, 'original_company_remark',['class'=>'form-control']),
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->original_company_remark;
                    } else {
                        return '';
                    }
                }
            ],
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
            [
                'attribute'      => 'is_process',
                'label'          => '加工',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'         => Goods::$process,
                'value'          => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return Goods::$process[$model->goods->is_process];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'is_better',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'    => Inquiry::$better,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$better[$model->is_better];
                }
            ],
            [
                'attribute' => 'is_purchase',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'    => Inquiry::$purchase,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$purchase[$model->is_purchase];
                }
            ],
            [
                'attribute' => 'is_confirm_better',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'    => Inquiry::$better,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$better[$model->is_confirm_better];
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
                'attribute' => 'is_upload',
                'contentOptions' =>['style'=>'min-width: 80px;'],
                'filter'    => Inquiry::$upload,
                'value'     => function ($model, $key, $index, $column) {
                    return Inquiry::$upload[$model->is_upload];
                }
            ],
            [
                'attribute' => 'supplier_name',
                'format'    => 'raw',
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
                'attribute'      => 'tax_rate',
                'contentOptions' => ['style'=>'min-width: 80px;']
            ],
            'price',
            'tax_price',
            'number',
            'all_price',
            'all_tax_price',
            [
                'attribute' => 'delivery_time',
                'contentOptions'=>['style'=>'min-width: 80px;']
            ],
            [
                'attribute' => 'inquiry_datetime',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[inquiry_datetime]',
                    'value' => Yii::$app->request->get('InquirySearch')['inquiry_datetime'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->inquiry_datetime, 0, 10);
                }
            ],
            'remark',
            [
                'attribute' => 'updated_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[updated_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['updated_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->updated_at, 0, 10);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'InquirySearch[created_at]',
                    'value' => Yii::$app->request->get('InquirySearch')['created_at'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->created_at, 0, 10);
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
