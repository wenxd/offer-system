<?php

namespace app\controllers;

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Customer;
use app\models\Goods;
use app\models\Order;
use app\models\Stock;
use app\models\SystemConfig;
use app\models\TempNotGoods;
use app\models\TempNotStock;
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
    public $enableCsrfValidation = false;

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

    /**导出数据
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        $tableHeader = ['订单号', '收入合同单号', '零件号', '库存数量', '价格', '总价', '采购员', '出库时间', '手动',
            '订单类型', '客户', '区块', '平台名称', '去向', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        //获取数据
        $params = Yii::$app->request->get('StockOutLogSearch');
        //var_dump($params);die;
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

        if (isset($params['direction']) && $params['direction']) {
            $query->andFilterWhere(['like', 'stock_log.direction', $params['direction']]);
        }
        if (isset($params['agreement_sn']) && $params['agreement_sn']) {
            $query->andFilterWhere(['like', 'stock_log.agreement_sn', $params['agreement_sn']]);
        }
        if (isset($params['region']) && $params['region']) {
            $query->andFilterWhere(['like', 'stock_log.region', $params['region']]);
        }
        if (isset($params['plat_name']) && $params['plat_name']) {
            $query->andFilterWhere(['like', 'stock_log.plat_name', $params['plat_name']]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'stock_log.id'                => $params['id'],
            'stock_log.number'            => $params['number'],
            'stock_log.is_manual'         => $params['is_manual'],
            'stock_log.customer_id'       => $params['customer_id'],
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

                $excel->setCellValue($letter[$i+10] . ($key + 2), $stockLog->customer_id);
                $excel->setCellValue($letter[$i+11] . ($key + 2), $stockLog->region);
                $excel->setCellValue($letter[$i+12] . ($key + 2), $stockLog->plat_name);
                $excel->setCellValue($letter[$i+13] . ($key + 2), $stockLog->direction);
                $excel->setCellValue($letter[$i+14] . ($key + 2), $stockLog->remark);
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

    /**
     * 下载导出库存模板
     */
    public function actionDownloadExcel()
    {
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $tableHeader = ['零件号', '库存数量', '客户', '区块', '平台名称', '去向', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '手动出库上传模板' . date('ymd-His');
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

    /**
     *批量手动出库
     */
    public function actionUpload()
    {
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            return json_encode(['code' => 500, 'msg' => '没有检测到上传文件']);
        } else {
            //导入文件是否正确
            if ($_FILES["FileName"]["error"] > 0) {
                return json_encode(['code' => 500, 'msg' => $_FILES["FileName"]["error"]]);
            } //导入文件类型
            else if ($_FILES['FileName']['type'] == 'application/vnd.ms-excel' ||
                $_FILES['FileName']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                $_FILES['FileName']['type'] == 'application/octet-stream'
            ) {
                //获取文件名称
                $ext = explode('.', $_FILES["FileName"]["name"]);
                $saveName = date('YmdHis') . rand(1000, 9999) . '.' . end($ext);
                //保存文件
                move_uploaded_file($_FILES["FileName"]["tmp_name"], $saveName);
                if (file_exists($saveName)) {
                    //获取excel对象
                    $spreadsheet = IOFactory::load($saveName);
                    //数据转换为数组
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    //总数
                    $total = count($sheetData);
                    $num = 0;

                    $systemList = SystemConfig::find()->where([
                        'title'  => [SystemConfig::TITLE_TAX, SystemConfig::TITLE_HIGH_STOCK_RATIO, SystemConfig::TITLE_LOW_STOCK_RATIO],
                        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->all();

                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            if (empty($value['A'])) {
                                continue;
                            }
                            $goods = Goods::find()->where(['goods_number' => trim($value['A'])])->one();
                            $customer = Customer::find()->where(['name' => trim($value['C'])])->one();
                            if (!$goods) {
                                $temp = new TempNotGoods();
                                $temp->goods_number = trim($value['A']);
                                $temp->save();
                            } else {
                                $stock = Stock::find()->where(['good_id' => $goods->id])->one();
                                if (!$stock) {
                                    $notStock = new TempNotStock();
                                    $notStock->goods_id = $goods->id;
                                    $notStock->save();
                                } else {
                                    $stockLog = new StockLog();
                                    $stockLog->goods_id     = $goods->id;
                                    $stockLog->number       = $value['B'] ? trim($value['B']) : 0;
                                    $stockLog->type         = StockLog::TYPE_OUT;
                                    $stockLog->customer_id  = $customer ? $customer->id : 0;
                                    $stockLog->region       = $value['D'] ? trim($value['D']) : '';
                                    $stockLog->plat_name    = $value['E'] ? trim($value['E']) : '';
                                    $stockLog->direction    = $value['F'] ? trim($value['F']) : '';
                                    $stockLog->admin_id     = Yii::$app->user->identity->id;
                                    $stockLog->is_manual    = StockLog::IS_MANUAL_YES;
                                    $stockLog->remark       = $value['G'] ? trim($value['G']) : '';
                                    $stockLog->operate_time = date('Y-m-d H:i:s');
                                    $stockLog->save();
                                }
                            }
                        }
                    }
                }
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条'], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
