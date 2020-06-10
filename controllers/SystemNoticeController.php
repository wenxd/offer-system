<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\SystemNotice;
use app\models\SystemNoticeSearch;
use app\controllers\BaseController;

/**
 * SystemNoticeController implements the CRUD actions for SystemNotice model.
 */
class SystemNoticeController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new SystemNoticeSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => SystemNotice::className(),
            ],
        ];
    }

    public function actionGetNotice()
    {
        $recently_time = date('Y-m-d H:i:s', (time() - 300));
        $notice = SystemNotice::find()->where([
            'is_deleted' => SystemNotice::IS_DELETED_NO,
            'is_read'    => SystemNotice::IS_READ_NO,
            'admin_id'   => Yii::$app->user->id,
        ])->andWhere("notice_at >= '$recently_time'")->one();
        if ($notice) {
            return json_encode(['code' => 200, 'msg' => '你有新的系统消息了！']);
        }
    }

    /**
     * 全部已读
     */
    public function actionReadAll()
    {
        $ids = Yii::$app->request->post('ids');
        $admin_id = Yii::$app->user->identity->id;
        $num = SystemNotice::updateAll(['is_read' => SystemNotice::IS_READ_YES], ['id' => $ids, 'admin_id' => $admin_id]);
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '全部已读']);
        }
    }
}
