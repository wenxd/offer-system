<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\SystemNotice;
use Yii;
use app\actions;
use app\models\Supplier;
use app\models\SupplierSearch;

/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new SupplierSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
//            'update' => [
//                'class'      => actions\UpdateAction::className(),
//                'modelClass' => Supplier::className(),
//                'scenario'   => 'supplier',
//            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Supplier::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Supplier::className(),
            ],
        ];
    }

    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = Supplier::findOne($id);
        $post = Yii::$app->request->post();
        if ($post) {
            // 判断是不是超管
            $admins = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
            $admins_id  = \yii\helpers\ArrayHelper::getColumn($admins, 'user_id');
            $userId = Yii::$app->user->identity->id;
            if (!in_array($userId, $admins_id)) {
                $post['Supplier'] = [
                    'exit_info' => json_encode($post['Supplier'], JSON_UNESCAPED_UNICODE),
                    'is_confirm' => Supplier::IS_DELETED_NO
                ];
            } else {
                $post['Supplier']['exit_info'] = '';
                $post['Supplier']['is_confirm'] = Supplier::IS_DELETED_YES;
            }
            if ($model->load($post) && $model->save()) {
                yii::$app->getSession()->setFlash('success', 'success');
            } else {
                yii::$app->getSession()->setFlash('error', $model->errors);
            }
        }
        if ($model->exit_info ?? false) {
            foreach (json_decode($model->exit_info) AS $k => $v) {
                $model->$k = $v;
            }
        }
        return $this->render('update', ['model' => $model,]);
    }

    public function actionDetail()
    {
        $id = Yii::$app->request->get('id');
        $supplier = Supplier::find()->where(['id' => $id])->asArray()->one();

        return json_encode(['code' => 200, 'data' => $supplier]);
    }

    public function actionCreate()
    {
        $model = new Supplier();
        $model->scenario = 'supplier';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionConfirm($id)
    {
        $supplier = Supplier::findOne($id);
        $supplier->is_confirm = Supplier::IS_CONFIRM_YES;
        $supplier->save();

        //给采购员、询价员通知
        $notice = new SystemNotice();
        $notice->admin_id  = $supplier->admin_id;
        $notice->content   = '管理员已确认了你创建的供应商' . $supplier->name;
        $notice->notice_at = date('Y-m-d H:i:s');
        $notice->save();

        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }

    /**
     * 修改审批
     */
    public function actionUpdateConfirm($id)
    {
        $model = Supplier::findOne($id);
        $exit_info = json_decode($model['exit_info'], true);
        $exit_info['exit_info'] = null;
        if ($model->load(['Supplier' => $exit_info]) && $model->save()) {
            yii::$app->getSession()->setFlash('success', 'success');
        } else {
            yii::$app->getSession()->setFlash('error', $model->errors);
        }
        return "<script>history.go(-1);</script>";
    }
}
