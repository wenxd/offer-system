<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
use app\models\Goods;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '零件列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{create} {delete} {download} {upload} {inquiry} {download-son} {upload-son} {download-check} {upload-check} {download-check02} {upload-check02} {index}',
            'buttons' => [
                'download' => function () {
                    return Html::a('<i class="fa fa-download"></i> 下载模板', Url::to(['download']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload' => function () {
                    return Html::a('<i class="fa fa-upload" onclick="upload"></i> 上传导入', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'url' => '?r=goods/upload',
                        'class' => 'btn btn-info btn-flat upload',
                    ]);
                },
                'inquiry' => function () {
                    return Html::a('<i class="fa fa-plus-circle" ></i> 生成非项目订单', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-flat add_inquiry',
                    ]);
                },
                'download-son' => function () {
                    return Html::a('<i class="fa fa-download"></i> 模板(子)', Url::to(['download-son']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload-son' => function () {
                    return Html::a('<i class="fa fa-upload"></i> 导入(子)', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-flat upload-son',
                    ]);
                },
                'download-check' => function () {
                    return Html::a('<i class="fa fa-download"></i> 检测模板', Url::to(['download-check']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload-check' => function () {
                    return Html::a('<i class="fa fa-upload"></i> 检测', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-flat upload-check',
                    ]);
                },
                'download-check02' => function () {
                    return Html::a('<i class="fa fa-download"></i> 检测模板02', Url::to(['download-check02']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-primary btn-flat',
                    ]);
                },
                'upload-check02' => function () {
                    return Html::a('<i class="fa fa-upload"></i> 检测02', 'Javascript: void(0)', [
                        'data-pjax' => '0',
                        'class' => 'btn btn-info btn-flat upload-check02',
                    ]);
                },
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-success btn-flat',
                    ]);
                }
            ]
        ]) ?>
    </div>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => [
                'firstPageLabel' => '首页',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'lastPageLabel' => '尾页',
            ],
            'columns' => [
                [
                    'class' => CheckboxColumn::className(),
                ],
                [
                    'attribute' => '操作',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 150px;'],
                    'value' => function ($model, $key, $index, $column) {
                        $html = Html::a('<i class="fa fa-edit"></i> 修改', Url::to(['update', 'id' => $model['id']]), [
                            'data-pjax' => '0',
                            'class' => 'btn btn-warning btn-xs btn-flat',
                        ]);

//                    $html .= Html::a('<i class="fa fa-plus"></i> 添加子零件', Url::to(['addson', 'id' => $model['id']]), [
//                        'data-pjax' => '0',
//                        'class' => 'btn btn-success btn-xs btn-flat',
//                    ]);
                        if ($model->locking == 1) {
                            $html .= Html::button('<i class="fa fa-lock"></i> 已锁定', [
                                    'class' => 'btn btn-success btn-xs btn-flat',
                                    'data-toggle' => 'tooltip',
                                    'title' => '点击解锁',
                                    'onclick' => 'locking(' . $model->id . ')',
                                    'name'  => 'submit-button']
                            );
                        } else {
                            $html .= Html::button('<i class="fa  fa-unlock"></i> 未锁定', [
                                    'class' => 'btn btn-danger btn-xs btn-flat',
                                    'data-toggle' => 'tooltip',
                                    'title' => '点击锁定',
                                    'onclick' => 'locking(' . $model->id . ')',
                                    'name'  => 'submit-button']
                            );
                        }
                        return $html;
                    }
                ],
                [
                    'attribute' => 'is_assembly',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$assembly,
                    'value' => function ($model, $key, $index, $column) {
//                        if (!empty($model->sons)) {
//                            $text = '';
//                            foreach ($model->sons as $son) {
//                                $text .= $son->goods->goods_number . ':' . $son->number . PHP_EOL;
//                            }
//                            return $text;
//                        }
                        return Goods::$assembly[$model->is_assembly];
                    }
                ],
                'id',
                [
                    'attribute' => 'material_code',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'goods_number',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::a($model->goods_number, Url::to(['view', 'id' => $model->id]));
                    }
                ],
                [
                    'attribute' => 'remark',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'original_company',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'goods_number_b',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'description',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'description_en',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'publish_price',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'factory_price',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'publish_delivery_time',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'self_number',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                    'value' => function ($model, $key, $index, $column) {
                        return $model->self_number ?? '';
                    }
                ],
                [
                    'attribute' => 'material',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'unit',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'original_company_remark',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'import_mark',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                    'value' => function ($model, $key, $index, $column) {
                        return $model->import_mark ?? '';
                    }
                ],
                [
                    'attribute' => 'is_process',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$process,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$process[$model->is_process];
                    }
                ],
                [
                    'attribute' => 'is_tz',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$tz,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$tz[$model->is_tz];
                    }
                ],
                [
                    'attribute' => 'is_standard',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$standard,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$standard[$model->is_standard];
                    }
                ],
                [
                    'attribute' => 'is_import',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$import,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$import[$model->is_import];
                    }
                ],
                [
                    'attribute' => 'is_repair',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$repair,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$repair[$model->is_repair];
                    }
                ],
                [
                    'attribute' => 'is_special',
                    'filter' => Goods::$special,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$special[$model->is_special];
                    }
                ],
                [
                    'attribute' => 'is_nameplate',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$nameplate,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$nameplate[$model->is_nameplate];
                    }
                ],
                [
                    'attribute' => 'is_emerg',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => Goods::$emerg,
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::$emerg[$model->is_emerg];
                    }
                ],
                [
                    'attribute' => 'part',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                ],
                [
                    'attribute' => 'device_info',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 200px;'],
                    'value' => function ($model) {
                        $text = '';
                        if ($model->device_info) {
                            foreach (json_decode($model->device_info, true) as $key => $device) {
                                $text .= $key . ':' . $device . '<br/>';
                            }
                        }
                        return $text;
                    }
                ],
                [
                    'attribute' => 'is_inquiry',
                    'label' => '是否询价',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'format' => 'raw',
                    'filter' => ['0' => '否', '1' => '是'],
                    'value' => function ($model) {
                        if ($model->inquiry) {
                            return '是';
                        } else {
                            return '否';
                        }
                    }
                ],
                [
                    'attribute' => 'inquiry_number',
                    'label' => '询价条目',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inquiryNumber;
                    }
                ],
                [
                    'attribute' => 'is_inquiry_better',
                    'label' => '是否优选',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'format' => 'raw',
                    'filter' => ['0' => '否', '1' => '是'],
                    'value' => function ($model) {
                        if ($model->inquiryBetter) {
                            $v = Yii::$app->request->get('GoodsSearch')['is_inquiry_better'];
                            if ($v === '0' || $v === '1') {
                                return $v ? '是' : '否';
                            } else {
                                return '是';
                            }
                        } else {
                            return '否';
                        }
                    }
                ],
                [
                    'attribute' => 'is_stock',
                    'label' => '是否有库存',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => ['0' => '否', '1' => '是'],
                    'value' => function ($model) {
                        if ($model->stockNumber) {
                            return '是';
                        } else {
                            return '否';
                        }
                    }
                ],
                [
                    'attribute' => 'stock_number',
                    'label' => '库存数量',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return $model->stock->number;
                        } else {
                            return 0;
                        }
                    }
                ],
                [
                    'attribute' => 'suggest_number',
                    'label' => '建议库存',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return $model->stock->suggest_number;
                        } else {
                            return 0;
                        }
                    }
                ],
                [
                    'attribute' => 'high_number',
                    'label' => '高储',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return $model->stock->high_number;
                        } else {
                            return 0;
                        }
                    }
                ],
                [
                    'attribute' => 'low_number',
                    'label' => '低储',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return $model->stock->low_number;
                        } else {
                            return 0;
                        }
                    }
                ],
                [
                    'attribute' => 'stock_low',
                    'label' => '库存不足',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => ['0' => '否', '1' => '是'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return ($model->stock->number < $model->stock->low_number) ? '是' : '否';
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'stock_high',
                    'label' => '库存超量',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 80px;'],
                    'filter' => ['0' => '否', '1' => '是'],
                    'value' => function ($model) {
                        if ($model->stock) {
                            return ($model->stock->number > $model->stock->high_number) ? '是' : '否';
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'updated_at',
                    'contentOptions' => ['style' => 'min-width: 150px;'],
                    'filter' => DateRangePicker::widget([
                        'name' => 'GoodsSearch[updated_at]',
                        'value' => Yii::$app->request->get('GoodsSearch')['updated_at'],
                    ]),
                    'value' => function ($model) {
                        return substr($model->updated_at, 0, 10);
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' => ['style' => 'min-width: 150px;'],
                    'filter' => DateRangePicker::widget([
                        'name' => 'GoodsSearch[created_at]',
                        'value' => Yii::$app->request->get('GoodsSearch')['created_at'],
                    ]),
                    'value' => function ($model) {
                        return substr($model->created_at, 0, 10);
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript" src="./js/jquery.ajaxupload.js"></script>
<script type="text/javascript">
    function locking(id) {
        $.ajax({
            type:"get",
            url:'?r=goods/locking',
            data:{id:id},
            dataType:'JSON',
            success:function(res){
                if (res && res.code == 200){
                    layer.msg(res.msg, {time:2000});
                    window.location.reload();
                } else {
                    layer.msg(res.msg, {time:2000});
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
        url: '?r=goods/upload',
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
                layer.msg(data.msg, {icon: 1});
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    $('.upload-son').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload-son',
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
            console.log(data);
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                //导入成功
                layer.msg(data.msg, {time: 5000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    $('.upload-check').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload-check',
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
            console.log(data);
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                window.location.href = '?r=goods/upload-check';
                //导入成功
                layer.msg(data.msg, {time: 5000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    $('.upload-check02').ajaxUploadPrompt({
        //上传地址
        url: '?r=goods/upload-check02',
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
            console.log(data);
            //关闭动画
            window.top.layer.close(index);
            //字符串转换json
            var data = JSON.parse(data);
            if (data.code == 200) {
                window.location.href = '?r=goods/upload-check02';
                //导入成功
                layer.msg(data.msg, {time: 5000}, function () {
                    window.location.reload();
                });
            } else {
                //失败提示
                layer.msg(data.msg, {icon: 1});
            }
        }
    });

    //生成询价单
    $('.add_inquiry').click(function () {
        var goods_ids = [];
        $("input[name='selection[]']").each(function (i, e) {
            if ($(e).prop('checked')) {
                goods_ids.push($(e).val())
            }
        });
        if (!goods_ids.length) {
            layer.msg('请选择要询价的商品', {icon: 1});
            return;
        }
        $.ajax({
            type: "POST",
            url: "?r=goods/inquiry-order",
            data: {goods_ids: goods_ids},
            dataType: 'JSON',
            success: function (res) {
                if (res && res.code == 200) {
                    location.replace("?r=order/create&temp_id=" + res.data);
                } else {
                    layer.msg(res.msg, {time: 2000});
                    return false;
                }
            }
        });
    });
</script>
