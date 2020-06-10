<?php

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
?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Bar::widget([
            'template' => '{delete}',
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
                'contentOptions'=>['style'=>'min-width: 130px;'],
                'header' => '操作',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
