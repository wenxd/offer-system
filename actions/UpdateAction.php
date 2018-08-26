<?php
namespace app\actions;

use backend\models\PrizeSetting;
use yii;
use yii\web\BadRequestHttpException;

class UpdateAction extends \yii\base\Action
{
    public $modelClass;

    public $scenario = 'default';

    /**
     * update修改
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function run($id)
    {
        if (! $id) throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
        /* @var $model yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);
        if (! $model) throw new BadRequestHttpException(yii::t('app', "Cannot find model by $id"));
        $model->setScenario( $this->scenario );

        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->controller->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->controller->render('update', [
            'model' => $model,
        ]);
    }

}