<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\Customer;
use app\models\CompetitorGoods;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CompetitorGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '竞争对手价格记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {delete} {download} {upload} {index}',
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
                        'class'     => 'btn btn-success btn-flat',
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
//            'goods_id',
            [
                'attribute' => 'goods_number',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'goods_number',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->goods) {
                        return $model->goods->goods_number;
                    } else {
                        return '';
                    }
                }
            ],
//            'competitor_id',
            [
                'attribute' => 'competitor_name',
                'format'    => 'raw',
                'filter'    => Html::activeTextInput($searchModel, 'competitor_name',['class'=>'form-control']),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->competitor) {
                        return $model->competitor->name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'customer',
                'contentOptions'=>['style'=>'min-width: 100px;'],
                'label'     => '针对客户',
                'format'    => 'raw',
                'filter'    => Customer::getSelectDropDown(),
                'value'     => function ($model, $key, $index, $column) {
                    if ($model->customer && $model->customers) {
                        return $model->customers->name;
                    } else {
                        return '';
                    }
                }
            ],
            'tax_rate',
            'price',
            'tax_price',
            [
                'attribute' => 'is_issue',
                'filter'    => CompetitorGoods::$issue,
                'value'     => function ($model, $key, $index, $column) {
                    return CompetitorGoods::$issue[$model->is_issue];
                }
            ],
            'number',
            'all_price',
            'all_tax_price',
            'delivery_time',
            'stock_number',
            [
                'attribute' => 'offer_date',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'CompetitorGoodsSearch[offer_date]',
                    'value' => Yii::$app->request->get('CompetitorGoodsSearch')['offer_date'] ?? '',
                ]),
                'value'     => function($model){
                    return substr($model->offer_date, 0, 10);
                }
            ],
            'remark',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
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
        url : '?r=competitor-goods/upload',
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
                layer.msg(data.msg,{icon:1, time:0, closeBtn: 1});
            }
        }
    });
</script>