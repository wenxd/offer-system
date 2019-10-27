<?php
use yii\helpers\Html;
use app\models\SystemNotice;
/* @var $this \yii\web\View */
/* @var $content string */
$identity = Yii::$app->user->getIdentity();
//统计每次气泡数量
$notice_number = SystemNotice::find()->where([
        'is_deleted' => SystemNotice::IS_DELETED_NO,
        'is_read'    => SystemNotice::IS_READ_NO,
])->count();

?>
<style>
    .treeview-menu {
        padding-left: 20px;
    }
</style>
<header class="main-header">

    <?= Html::a('<span class="logo-mini">询价</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li>
                    <a href="JavaScript:;">
                        <i class="glyphicon glyphicon-user"></i> <span><?= $identity->username ?></span>
                    </a>
                </li>
                <li>
                    <a href="?r=system-notice/index">
                        <i class="fa fa-bell-o font-20"></i> <span class="label label-success ng-binding" style="background-color:#d24637;z-index:99999"><?=$notice_number?></span>
                    </a>
                </li>
                <li>
                    <?= Html::a(
                        '<i class="glyphicon glyphicon-log-out"></i> 退出',
                        ['/site/logout'],
                        ['data-method' => 'post', 'class' => 'btn btn-flat']
                    ) ?>
                </li>
            </ul>
        </div>
    </nav>
</header>
