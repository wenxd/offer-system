<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
$identity = Yii::$app->user->getIdentity();
?>

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
