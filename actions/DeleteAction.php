<?php
namespace app\actions;

use yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class DeleteAction extends \yii\base\Action
{
    public $modelClass;

    /**
     * delete删除
     *
     * @param $id
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function run($id)
    {
        if (yii::$app->getRequest()->getIsAjax()) {//AJAX删除
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            if (! $id) {
                return ['code' => 1, 'message' => yii::t('app', "Id doesn't exit")];
            }
            $ids = explode(',', $id);
            $errorIds = [];
            /* @var $model yii\db\ActiveRecord */
            $model = null;
            foreach ($ids as $one) {
                $model = call_user_func([$this->modelClass, 'findOne'], $one);
                if ($model) {
                    if (isset($model->is_deleted)) {
                        $model->is_deleted = 1;
                        if (! $result = $model->update()) {
                            $errorIds[] = $one;
                        }
                    } else {
                        if (! $result = $model->delete()) {
                            $errorIds[] = $one;
                        }
                    }
                }
            }
            if (count($errorIds) == 0) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0];
                }
                if ($err != '') {
                    $err = '.' . $err;
                }
                return ['code' => 1, 'message' => 'id ' . implode(',', $errorIds) . $err];
            }
        } else {
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            $ids = explode(',', $id);
            $errorIds = [];
            /* @var $model yii\db\ActiveRecord */
            $model = null;
            foreach ($ids as $one) {
                $model = call_user_func([$this->modelClass, 'findOne'], $one);
                if ($model) {
                    if (isset($model->is_deleted)) {
                        $model->is_deleted = 1;
                        if (! $result = $model->update()) {
                            $errorIds[] = $one;
                        }
                    } else {
                        if (! $result = $model->delete()) {
                            $errorIds[] = $one;
                        }
                    }
                }
            }
            if (count($errorIds) == 0) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->controller->redirect(yii::$app->request->headers['referer']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0];
                }
                if ($err != '') {
                    $err = '.' . $err;
                }
                yii::$app->getSession()->setFlash('error', 'id ' . implode(',', $errorIds) . $err);
                return $this->controller->redirect(yii::$app->request->headers['referer']);
            }
        }
    }
}