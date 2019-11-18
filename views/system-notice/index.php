<?php

use app\models\AuthAssignment;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\extend\widgets\Bar;
use yii\grid\CheckboxColumn;
use app\extend\grid\ActionColumn;
use kartik\daterange\DateRangePicker;
use app\models\SystemNotice;
use app\models\SystemNoticeSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SystemNoticeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统通知';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$userId    = Yii::$app->user->identity->id;
$isShow    = in_array($userId, $adminIds);
if ($isShow) {
    $operate = '{delete} {read-all}';
} else {
    $operate = '{read-all}';
}

?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => $operate,
            'buttons' => [
                'read-all' => function () {
                    return Html::button('<i class="fa fa-book"></i> 全部已读', [
                        'data-pjax' => '0',
                        'class'     => 'btn btn-primary btn-flat read-all',
                    ]);
                },
            ],
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
            'id',
            'content',
            [
                'attribute' => 'is_read',
                'format'    => 'raw',
                'filter'    => SystemNotice::$read,
                'value'     => function ($model, $key, $index, $column) {
                    $dropdown = SystemNotice::$read;
                    if ($model->is_read == SystemNotice::IS_READ_NO) {
                        $status  = SystemNotice::IS_READ_YES;
                        $text    = $dropdown[$model->is_read];
                        $class   = 'btn btn-warning btn-xs btn-flat';
                    } else {
                        return SystemNotice::$read[$model->is_read];
                    }
                    $url = Url::to([
                        'status',
                        'id'     => $model->id,
                        'status' => $status,
                        'field'  => 'is_read'
                    ]);
                    return Html::a($text, $url, [
                        'class'        => $class,
                        'data-method'  => 'post',
                        'data-pjax'    => '0',
                    ]);

                }
            ],
            [
                'attribute' => 'notice_at',
                'contentOptions'=>['style'=>'min-width: 150px;'],
                'filter'    => DateRangePicker::widget([
                    'name'  => 'SystemNoticeSearch[notice_at]',
                    'value' => Yii::$app->request->get('SystemNoticeSearch')['notice_at'],
                ])
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions'=>['style'=>'min-width: 80px;'],
                'header' => '操作',
                'template' => '{view}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    $('.read-all').click(function (e) {
        var ids = [];
        $('tbody input').each(function (i, e) {
            if ($(e).prop("checked")) {
                ids.push($(e).val());
            }
        });
        if (ids.length) {
            $.ajax({
                type: "post",
                url: '?r=system-notice/read-all',
                data: {ids: ids},
                dataType: 'JSON',
                success: function (res) {
                    if (res && res.code == 200) {
                        layer.msg(res.msg, {time: 2000});
                        window.location.reload();
                    } else {
                        layer.msg(res.msg, {time: 2000});
                        return false;
                    }
                }
            });
        } else {
            layer.msg('请选择已读的选项', {time:2000});
        }
    });
</script>
