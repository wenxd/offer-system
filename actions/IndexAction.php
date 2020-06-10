<?php
namespace app\actions;

class IndexAction extends \yii\base\Action
{

    public $data;

    public function run()
    {
        $data = $this->data;
        if( $data instanceof \Closure){
            $data = call_user_func( $this->data );
        }
        return $this->controller->render('index', $data);
    }

}