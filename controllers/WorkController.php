<?php
/**
 * 每日每周工作记录规划
 */


namespace app\controllers;

use Yii;

class WorkController extends BaseController
{
    public function actionIndex()
    {
        $this->layout = 'layout-no-nav';
        $titles = ['', ''];
        $temp = [['', ''], ['', '']];
        return $this->render('index', ['temp' => $temp, 'titles' => $titles]);
    }

    public function actionAdd()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            file_put_contents('work.php', json_encode($post));
            var_dump($post);
            die;
        }
        $this->layout = 'layout-no-nav';
        $data = json_decode(file_get_contents('work.php'), true);

        $titles = $data['titles'];
        $items = $data['items'];
        $rows = 4;
        foreach ($items as $item) {
            foreach ($item as $value) {
                $lines_arr = preg_split('/\n|\r/',$value);
                $new_lines = count($lines_arr);
                if ($rows < $new_lines) {
                    $rows = $new_lines;
                }
            }
        }
        return $this->render('add', ['items' => $items, 'titles' => $titles, 'rows' => $rows]);
    }
}