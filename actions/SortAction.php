<?php
namespace app\actions;

use yii;

class SortAction extends \yii\base\Action
{
    public $modelClass;

    /**
     * 排序操作
     *
     */
    public function run()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $value = intval($value);
                    if ($value < 0) {
                        yii::$app->getSession()->setFlash('error', '排序数字请大于等于0');
                        return $this->controller->redirect(yii::$app->request->headers['referer']);
                    }
                    if ($value > 10000) {
                        yii::$app->getSession()->setFlash('error', '排序数字请小于等于10000');
                        return $this->controller->redirect(yii::$app->request->headers['referer']);
                    }

                    /* @var $model yii\db\ActiveRecord */
                    $model = call_user_func([$this->modelClass, 'findOne'], $key);
                    if ($model->sort != $value) {
                        $model->sort = $value;
                        $model->save(false);
                    }
                }
            }
        }

        $this->controller->redirect(['index']);
    }

}