<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/11/13
 * Time: 18:05
 */
namespace app\controllers;

use app\models\Inquiry;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex()
    {
        $query = Inquiry::find()->alias('i')->select('i.*, g.*')
            ->leftJoin('goods g', 'i.good_id=g.id')->asArray()->all();

        var_dump($query);die;
    }
}
