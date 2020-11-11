<?php

use app\models\Admin;
use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\Helper;
use app\models\Supplier;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '供应商列表';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => ['询价员', '采购员']])->all();

$html = '<form class="form-horizontal"><div class="form-group"><label for="reason" class="col-sm-2 control-label"></label><div class="col-sm-8"><select class="form-control" id="exit_admin" name="SupplierSearch[grade]"><option value="">请选择申请人</option>';
$ids = [];
foreach ($use_admin as $item) {
    if (isset($item->name)) {
        $id = $item->name->id;
        if (!in_array($id, $ids)) {
            $username = $item->name->username;
            $html .= "<option value={$id}>{$username}</option>";
            $ids[] = $id;
        }

    }
}
$html .= '</select></div></div><div class="form-group"><div class="col-sm-offset-2 col-sm-10"><a class="btn btn-default btn_sure" href="javascript:void(0)" onclick="sure()">确定</a></div></div></form>';
$adminIds = ArrayHelper::getColumn($use_admin, 'user_id');
$admins = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
$admins_id = ArrayHelper::getColumn($admins, 'user_id');

$userId = Yii::$app->user->identity->id;
if (in_array($userId, $adminIds)) {
    $control = '{create} {index}';
} else {
    $control = '{create} {delete} {index} {updateall}';
}
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => $control,
            'buttons' => [
                'index' => function () {
                    return Html::a('<i class="fa fa-reload"></i> 复位', Url::to(['index']), [
                        'data-pjax' => '0',
                        'class' => 'btn btn-success btn-flat',
                    ]);
                },
                'updateall' => function () {
                    return Html::button('批量修改', ['class' => 'btn btn-primary btn-flat', 'onclick' => 'updateall()', ]);
                }
            ]
        ]) ?>
    </div>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'id' => 'griditems',
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
                'id',
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'name';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
//                            if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'short_name',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'short_name';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
//                            if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'full_name',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'full_name';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'contacts',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'contacts';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'mobile',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'mobile';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'telephone',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'telephone';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'email',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'email';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'grade',
                    'format' => 'raw',
                    'filter' => Supplier::$grade,
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'grade';
                        $name = $model->$key;
                        $grade = Supplier::$grade[$name];
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            if ($name != $exit_info[$key]) {
                                $exit_grade = Supplier::$grade[$exit_info[$key]];
//                                return "{$grade}->{$exit_grade}";
                                $grade = $exit_grade;
                            }
                        }
                        return $grade;
                    }
                ],
                [
                    'attribute' => 'grade_reason',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'grade_reason';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'advantage_product',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $key = 'advantage_product';
                        $name = $model->$key;
                        if (!empty($model->exit_info)) {
                            $exit_info = json_decode($model->exit_info, true);
                            //if ($name != $exit_info[$key]) return "{$name}->{$exit_info[$key]}";
                            $name = $exit_info[$key];
                        }
                        return $name;
                    }
                ],
                [
                    'attribute' => 'admin_id',
//                    'visible'   => Yii::$app->user->identity->username == 'admin',
//                    'visible' => in_array($userId, $adminIds),
                    'label' => '申请人',
                    'filter' => in_array($userId, $adminIds) ? Helper::getAdminList(['询价员', '采购员']) : Helper::getAdminList(['系统管理员', '询价员', '采购员']),
                    'value' => function ($model, $key, $index, $column) {
                        if ($model->admin) {
                            return $model->admin->username;
                        } else {
                            return '';
                        }
                    }
                ],
                [
                    'attribute' => 'is_confirm',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width: 100px;'],
                    'filter' => Supplier::$confirm,
                    'value' => function ($model, $key, $index, $column) {
                        return Supplier::$confirm[$model->is_confirm];
                        if (!empty($model->exit_info)) {
                            $confirm .= "(等待修改审核)";
                        }
                        return $confirm;
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'label' => '申请时间',
                    'filter' => DateRangePicker::widget([
                        'name' => 'SupplierSearch[created_at]',
                        'value' => Yii::$app->request->get('SupplierSearch')['created_at'],
                    ]),
                    'value' => function ($model) {
                        return substr($model->created_at, 0, 10);
                    }
                ],
                [
                    'attribute' => 'agree_at',
                    'format' => 'raw',
                    'label' => '入库时间',
                    'filter' => DateRangePicker::widget([
                        'name' => 'SupplierSearch[agree_at]',
                        'value' => Yii::$app->request->get('SupplierSearch')['agree_at'],
                    ]),
                    'value' => function ($model) {
                        return substr($model->agree_at, 0, 10);
                    }
                ],
                [
                    'class' => ActionColumn::className(),
//                'visible'       => !in_array($userId, $adminIds),
                    'contentOptions' => ['style' => 'min-width: 200px;'],
                    'header' => '操作',
                    'template' => '{confirm} {view} {update}',
                    'buttons' => [
                        'confirm' => function ($url, $model, $key) use ($userId, $admins_id) {
                            if (in_array($userId, $admins_id)) {
                                return Html::a('<i class="fa fa-reload"></i> 审批', Url::to(['confirm', 'id' => $model->id]), [
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-success btn-flat btn-xs',
                                ]);
                            }

                        }
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    var ids = [];
    var content = '<?=$html?>';

    function updateall() {
        ids = $('#griditems').yiiGridView('getSelectedRows');
        if (ids.length == 0) {
            layer.msg('请勾选', {time: 2000});
            return false;
        }
        layer.open({
            type: 1,
            title: '修改申请人',
            skin: 'layui-layer-rim', //加上边框
            area: ['500px', '240px'], //宽高
            content: content
        });
    }

    function sure(id) {
        var admin_id = $('#exit_admin').val();

        if (!admin_id) {
            layer.msg('请选择申请人', {time: 2000});
            return false;
        }
        console.log(ids);
        console.log(admin_id);
        layer.confirm('确定要修改吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type: "post",
                url: "?r=search/update-all-supplier-admin",
                data: {ids: ids, admin_id: admin_id},
                dataType: 'JSON',
                success: function (res) {
                    layer.msg(res.msg, {time: 2000});
                    if (res && res.code == 200) {
                        window.location.reload();
                    } else {
                        return false;
                    }
                }
            });
        }, function(){
            layer.closeAll();
        });

    }
</script>
