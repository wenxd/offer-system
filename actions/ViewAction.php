<?php

namespace app\actions;

class ViewAction extends \yii\base\Action
{
    public $modelClass;

    /**
     * view详情页
     *
     * @param $id
     * @return string
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        return $this->controller->render('view', [
            'model' => $model,
        ]);
    }

}