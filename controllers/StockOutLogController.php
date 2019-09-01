<?php

namespace app\controllers;

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Order;
use app\models\Stock;
use app\models\SystemConfig;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\models\StockLog;
use app\models\StockOutLogSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockOutLogController implements the CRUD actions for StockLog model.
 */
class StockOutLogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all StockLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockOutLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StockLog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StockLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StockLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StockLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StockLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the StockLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**手动添加出库记录
     * @return false|string
     */
    public function actionAdd()
    {
        $params = Yii::$app->request->post();
        $stock = Stock::findOne(['good_id' => $params['goods_id']]);
        if (!$stock) {
            return json_encode(['code' => 500, 'msg' => '没有此库存记录，不能出库']);
        }
        if ($params['number'] > $stock->number) {
            return json_encode(['code' => 500, 'msg' => '库存数量不够，剩下' . $stock->number]);
        }
        $stockLog = new StockLog();
        $stockLog->goods_id     = $params['goods_id'];
        $stockLog->number       = $params['number'];
        $stockLog->type         = StockLog::TYPE_OUT;
        $stockLog->remark       = $params['remark'];
        $stockLog->operate_time = date('Y-m-d H:i:s');
        $stockLog->admin_id     = Yii::$app->user->identity->id;
        $stockLog->is_manual    = StockLog::IS_MANUAL_YES;
        $stockLog->direction    = $params['direction'] ? $params['direction'] : '';
        $stockLog->customer_id  = $params['customer_id'] ? $params['customer_id'] : 0;
        $stockLog->region       = $params['region'] ? $params['region'] : '';
        $stockLog->plat_name    = $params['plat_name'] ? $params['plat_name'] : '';
        if ($stockLog->save()) {
            $stock->number -= $params['number'];
            $stock->save();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()]);
        }
    }

    public function actionDownload()
    {
        $use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '系统管理员']])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

        $adminList = Admin::find()->where(['id' => $adminIds])->all();
        $admins = [];
        foreach ($adminList as $key => $admin) {
            $admins[$admin->id] = $admin->username;
        }
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $excel=$spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        $tableHeader = ['订单号', '收入合同单号', '零件号', '库存数量', '价格', '总价', '采购员', '出库时间', '手动',
            '订单类型', '去向', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        //获取数据
        $params = Yii::$app->request->get('StockOutLogSearch');
        $query = StockLog::find()->where(['stock_log.type' => StockLog::TYPE_OUT]);
        if ((isset($params['order_sn']) && $params['order_sn']) ||
            (isset($params['order_type']) && $params['order_type'] != '')) {
            $query->leftJoin('order as a', 'a.id = stock_log.order_id');
            $query->andFilterWhere(['like', 'a.order_sn', $params['order_sn']]);
            $query->andFilterWhere(['a.order_type' => $params['order_type']]);
        }
        if (isset($params['goods_number']) && $params['goods_number']) {
            $query->leftJoin('goods as b', 'b.id = stock_log.goods_id');
            $query->andFilterWhere(['like', 'b.goods_number', $params['goods_number']]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'stock_log.id'                => $params['id'],
            'stock_log.number'            => $params['number'],
            'stock_log.agreement_sn'      => $params['agreement_sn'],
            'stock_log.is_manual'         => $params['is_manual'],
            'stock_log.direction'         => $params['direction'],
        ]);
        $stockLogList = $query->all();

        foreach ($stockLogList as $key => $stockLog) {
            for($i = 0; $i < count($letter); $i++) {
                if ($stockLog->order) {
                    $excel->setCellValue($letter[$i] . ($key + 2), $stockLog->order->order_sn);
                } else {
                    $excel->setCellValue($letter[$i] . ($key + 2), '');
                }
                $excel->setCellValue($letter[$i+1] . ($key + 2), $stockLog->agreement_sn);
                if ($stockLog->goods) {
                    $excel->setCellValue($letter[$i+2] . ($key + 2), $stockLog->goods->goods_number);
                } else {
                    $excel->setCellValue($letter[$i+2] . ($key + 2), '');
                }
                $excel->setCellValue($letter[$i+3] . ($key + 2), $stockLog->number);
                $excel->setCellValue($letter[$i+4] . ($key + 2), $stockLog->stock->price);
                $excel->setCellValue($letter[$i+5] . ($key + 2), $stockLog->number * $stockLog->stock->price);
                $excel->setCellValue($letter[$i+6] . ($key + 2), $admins[$stockLog->admin_id]);
                $excel->setCellValue($letter[$i+7] . ($key + 2), $stockLog->operate_time);
                $excel->setCellValue($letter[$i+8] . ($key + 2), StockLog::$manual[$stockLog->is_manual]);
                if ($stockLog->order) {
                    $excel->setCellValue($letter[$i+9] . ($key + 2), Order::$orderType[$stockLog->order->order_type]);
                } else {
                    $excel->setCellValue($letter[$i+9] . ($key + 2), '');
                }
                $excel->setCellValue($letter[$i+10] . ($key + 2), $stockLog->direction);
                $excel->setCellValue($letter[$i+11] . ($key + 2), $stockLog->remark);
                break;
            }
        }

        $title = '出库记录' . date('ymd-His');
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;
    }
}
