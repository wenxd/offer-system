<?php

namespace app\controllers;

use app\models\Brand;
use app\models\Goods;
use app\models\Stock;
use app\models\SystemConfig;
use app\models\TempNotGoods;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\models\StockLog;
use app\models\StockInLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockInLogController implements the CRUD actions for StockLog model.
 */
class StockInLogController extends Controller
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
        $searchModel = new StockInLogSearch();
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
            return $this->redirect(['stock-in-log/index']);
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

    /**手动添加入库记录
     * @return false|string
     */
    public function actionAdd()
    {
        $params = Yii::$app->request->post();
        $stockLog = new StockLog();
        $stockLog->goods_id     = $params['goods_id'];
        $stockLog->number       = $params['number'];
        $stockLog->type         = StockLog::TYPE_IN;
        $stockLog->remark       = $params['remark'];
        $stockLog->operate_time = date('Y-m-d H:i:s');
        $stockLog->admin_id     = Yii::$app->user->identity->id;
        $stockLog->is_manual    = StockLog::IS_MANUAL_YES;
        $stockLog->source       = $params['source'];
        $stockLog->position     = $params['position'];
        if ($stockLog->save()) {
            $systemList = SystemConfig::find()->where([
                'title'  => [SystemConfig::TITLE_TAX, SystemConfig::TITLE_HIGH_STOCK_RATIO, SystemConfig::TITLE_LOW_STOCK_RATIO],
                'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->all();

            foreach ($systemList as $key => $value) {
                if ($value['title'] == SystemConfig::TITLE_TAX) {
                    $tax = $value['value'];
                }
                if ($value['title'] == SystemConfig::TITLE_HIGH_STOCK_RATIO) {
                    $highRatio = $value['value'];
                }
                if ($value['title'] == SystemConfig::TITLE_LOW_STOCK_RATIO) {
                    $lowRatio = $value['value'];
                }
            }
            $stock = Stock::find()->where(['good_id' => $params['goods_id']])->one();
            if (!$stock) {
                $stock   = new Stock();
                $stock->good_id         = $params['goods_id'];
                $stock->price           = 0;
                $stock->tax_rate        = $tax;
                $stock->tax_price       = $stock->price * (1 + $tax / 100);
                $stock->number          = $params['number'];
                $stock->temp_number     = $params['number'];
                $stock->position        = $params['position'];
                $stock->suggest_number  = 0;
                $stock->high_number     = 0 * $highRatio;
                $stock->low_number      = 0 * $lowRatio;
                $stock->save();
            } else {
                if ($params['position']) {
                    $stock->position = $params['position'];
                }
                $stock->number   += $params['number'];
                $stock->temp_number   += $params['number'];
                $stock->save();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()]);
        }
    }

    /**
     * 下载导入库存模板
     */
    public function actionDownload()
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F'];
        $tableHeader = ['品牌', '零件号', '入库数量', '库存位置', '入库来源', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '手动入库上传模板' . date('ymd-His');
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
     *批量手动入库
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
                            if (empty($value['B'])) {
                                continue;
                            }
                            $brand = Brand::find()->where(['name' => trim($value['A'])])->one();
                            if (!$brand) {
                                return json_encode(['code' => 500, 'msg' => '品牌' . trim($value['A']) . '不存在，清先添加此品牌'], JSON_UNESCAPED_UNICODE);
                            }
                            $goods = Goods::find()->where([
                                'is_deleted'   => Goods::IS_DELETED_NO,
                                'goods_number' => trim($value['B']),
                                'brand_id'     => $brand->id,
                            ])->one();
                            if (!$goods) {
                                $temp = TempNotGoods::findOne([
                                    'brand_name'   => trim($value['A']),
                                    'goods_number' => trim($value['B'])
                                ]);
                                if (!$temp) {
                                    $temp = new TempNotGoods();
                                }
                                $temp->brand_name   = trim($value['A']);
                                $temp->goods_number = trim($value['B']);
                                $temp->save();
                            } else {
                                $stockLog = new StockLog();
                                $stockLog->goods_id     = $goods->id;
                                $stockLog->number       = $value['C'] ? trim($value['C']) : 0;
                                $stockLog->type         = StockLog::TYPE_IN;
                                $stockLog->remark       = $value['F'] ? trim($value['F']) : '';
                                $stockLog->operate_time = date('Y-m-d H:i:s');
                                $stockLog->admin_id     = Yii::$app->user->identity->id;
                                $stockLog->is_manual    = StockLog::IS_MANUAL_YES;
                                $stockLog->source       = trim($value['E']);
                                $stockLog->position     = trim($value['D']);
                                if ($stockLog->save()) {
                                    foreach ($systemList as $k => $item) {
                                        if ($item['title'] == SystemConfig::TITLE_TAX) {
                                            $tax = $item['value'];
                                        }
                                        if ($item['title'] == SystemConfig::TITLE_HIGH_STOCK_RATIO) {
                                            $highRatio = $item['value'];
                                        }
                                        if ($item['title'] == SystemConfig::TITLE_LOW_STOCK_RATIO) {
                                            $lowRatio = $item['value'];
                                        }
                                    }
                                    $stock = Stock::find()->where(['good_id' => $goods->id])->one();
                                    if (!$stock) {
                                        $stock = new Stock();
                                        $stock->good_id         = $goods->id;
                                        $stock->price           = 0;
                                        $stock->tax_rate        = $tax;
                                        $stock->tax_price       = 0;
                                        $stock->number          = trim($value['C']);
                                        $stock->temp_number          = trim($value['C']);
                                        $stock->position        = $value['D'] ? trim($value['D']) : '';
                                        $stock->suggest_number  = 0;
                                        $stock->high_number     = $stock->suggest_number * $highRatio;
                                        $stock->low_number      = $stock->suggest_number * $lowRatio;
                                        $stock->save();
                                    } else {
                                        if (trim($value['D'])) {
                                            $stock->position = $value['D'] ? trim($value['D']) : '';
                                        }
                                        $stock->number += trim($value['C']);
                                        $stock->temp_number += trim($value['C']);
                                        $stock->save();
                                    }
                                    $num++;
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
