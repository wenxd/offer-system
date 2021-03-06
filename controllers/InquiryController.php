<?php

namespace app\controllers;

use app\assets\Common;
use app\models\Admin;
use app\models\AuthAssignment;
use Yii;
use app\actions;
use app\models\{Brand,
    Goods,
    InquiryGoods,
    OrderInquiry,
    PaymentGoods,
    Stock,
    Inquiry,
    PurchaseGoods,
    InquirySearch,
    Supplier,
    SystemConfig,
    TempNotGoods};
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use yii\helpers\ArrayHelper;

/**
 * InquiryController implements the CRUD actions for Inquiry model.
 */
class InquiryController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new InquirySearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Inquiry::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Inquiry::className(),
            ],
        ];
    }

    public function actionAdd()
    {
        $model = new Inquiry();
        $inquiryGoods = InquiryGoods::findOne($_GET['inquiry_goods_id']);

        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                if ($inquiryGoods) {
                    return $this->redirect(['order-inquiry/view', 'id' => $inquiryGoods->order_inquiry_id]);
                } else {
                    return $this->redirect(['order-inquiry/index']);
                }
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }

        return $this->render('add-inquiry', [
            'model'        => $model,
            'inquiryGoods' => $inquiryGoods,
        ]);
    }

    public function actionCreate()
    {
        $model = new Inquiry();

        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['create']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Inquiry::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSearch($goods_id)
    {
        $goods = Goods::findOne($goods_id);

        $where = ['good_id' => $goods_id, 'is_deleted' => Inquiry::IS_DELETED_NO];
        //库存记录
        $stockQuery = Stock::find()->where($where)->orderBy('updated_at Desc')->one();
        //询价记录 价格最优
        $inquiryPriceQuery  = Inquiry::find()->where($where)->orderBy('price asc, Created_at Desc')->one();
        //同期最短(货期)
        $inquiryTimeQuery   = Inquiry::find()->where($where)->orderBy('delivery_time asc, Created_at Desc')->one();
        //最新报价
        $inquiryNewQuery    = Inquiry::find()->where($where)->orderBy('Created_at Desc')->one();
        //优选记录
        $inquiryBetterQuery = Inquiry::find()->where($where)->andWhere(['is_better' => Inquiry::IS_BETTER_YES, 'is_confirm_better' => 1])->orderBy('updated_at Desc')->one();
        //采购记录  最新采购
        $paymentNew   = PaymentGoods::find()->andWhere(['goods_id' => $goods_id])->orderBy('created_at Desc')->one();
        //价格最低采购
        $paymentPrice = PaymentGoods::find()->andWhere(['goods_id' => $goods_id])->orderBy('fixed_price asc')->one();
        //货期采购
        $paymentDay   = PaymentGoods::find()->andWhere(['goods_id' => $goods_id])->orderBy('delivery_time asc')->one();

        $data = [];
        $data['goods']         = $goods ? $goods : [];

        $data['inquiryPrice']  = $inquiryPriceQuery;
        $data['inquiryTime']   = $inquiryTimeQuery;
        $data['inquiryNew']    = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;

        $data['stock']         = $stockQuery;

        $data['paymentNew']    = $paymentNew;
        $data['paymentPrice']  = $paymentPrice;
        $data['paymentDay']    = $paymentDay;

        return $this->render('search-result', $data);
    }

    /**
     * 下载批量询价模板
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $tableHeader = ['品牌', '零件号', '供应商', '税率', '含税单价', '询价数量', '货期(周)', '询价备注', '是否优选',
            '优选理由', '询价员'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '批量询价模板' . date('ymd-His');
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
                    $delivery = SystemConfig::find()->select('value')->where([
                        'title'  => SystemConfig::TITLE_DELIVERY_TIME,
                        'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();

                    $use_admin = AuthAssignment::find()->where(['item_name' => '询价员'])->all();
                    $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
                    $adminList = Admin::find()->where(['id' => $adminIds])->indexBy('username')->all();

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

                            $supplier = Supplier::find()->where(['name' => trim($value['C'])])->one();
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
                                if ($supplier) {
                                    $inquiry = Inquiry::find()->where([
                                        'good_id'       => $goods->id,
                                        'supplier_id'   => $supplier->id,
                                        'tax_rate'      => trim($value['D']),
                                        'tax_price'     => trim($value['E']),
                                    ])->one();
                                    if (!$inquiry) {
                                        $inquiry = new Inquiry();
                                        $inquiry->good_id           = $goods->id;
                                        $inquiry->supplier_id       = $supplier->id;
                                        $inquiry->tax_rate          = trim($value['D']);
                                        $inquiry->price             = $value['E'] / (1 + trim($value['D']) / 100);
                                        $inquiry->tax_price         = $value['E'] ? trim($value['E']) : 0;
                                        $inquiry->number            = $value['F'] ? trim($value['F']) : 0;
                                        $inquiry->delivery_time     = $value['G'] ? trim($value['G']) : $delivery;
                                        $inquiry->inquiry_datetime  = date('Y-m-d H:i:s');
                                        $inquiry->all_price         = $inquiry->number * $inquiry->price;
                                        $inquiry->all_tax_price     = $inquiry->number * $inquiry->tax_price;
                                        $inquiry->is_better         = (trim($value['I']) == '是') ? 1 : 0;
                                        $inquiry->better_reason     = $value['J'] ? trim($value['J']) : '';
                                        $inquiry->remark            = $value['H'] ? trim($value['H']) : '';
                                        $inquiry->admin_id          = isset($adminList[trim($value['K'])]) ? $adminList[trim($value['K'])]->id : Yii::$app->user->identity->id;
                                        if ($inquiry->save()) {
                                            $num++;
                                        } else {
                                            return json_encode(['code' => 500, 'msg' => $inquiry->getErrors()], JSON_UNESCAPED_UNICODE);
                                        }
                                    }
                                    
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

    public function actionConfirm($id)
    {
        $inquiry = Inquiry::findOne($id);
        $inquiry->is_confirm_better = 1;
        if ($inquiry->save()){
            yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        } else {
            $errors = $inquiry->getErrors();
            $err = '';
            foreach ($errors as $v) {
                $err .= $v[0] . '<br>';
            }
            Yii::$app->getSession()->setFlash('error', $err);
        }
        return $this->redirect(['index']);
    }

    public function actionDownloadInquiryTemp()
    {
        // 品牌，零件号，竞争对手名称  导出未税单价
        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        $tableHeader = self::InquiryTemp;;
        $fileName = '询价记录模板' . date('ymd-His');
        Common::DownloadTemp($letter, $tableHeader, $fileName);
    }
    /**
     * 询价记录列表：根据零件号和品牌/价格类型  导出最高和最低价格（未税单价），货期，采购员，厂家，是否采购记录，咨询时间，询价备注，技术备注，原厂家，厂家号
     */
    const InquiryTemp = ['品牌', '零件号', '价格类型(最高/最低)', '价格', '货期', '询价员', '是否采购记录', '咨询时间', '询价备注', '技术备注', '原厂家', '厂家号'];
    /**
     * 上传询价记录模板
     */
    public function actionUploadInquiryTempCheck()
    {
//        $sheetData = [ 2 => [
//            'A' => 'FAG',
//            'B' => '杂项-纸箱子',
//            'C' => '最低',
//        ]];
        $cache = Yii::$app->cache;
        $key_name = 'upload_inquiry_temp_check';
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            if ($cache->exists($key_name)) {
                $data = json_decode($cache->get($key_name), true);
                $cache->delete($key_name);
                $fileName = '询价记录模板结果.csv';
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                $fp = fopen('php://output', 'a');//打开output流
                foreach ($data as $rowData) {
                    mb_convert_variables('GBK', 'UTF-8', $rowData);
                    fputcsv($fp, $rowData);
                }
                unset($data);//释放变量的内存
                ob_flush();
                flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
                fclose($fp);
                exit();
            }
            return json_encode(['code' => 500, 'msg' => '没有检测到上传文件'], JSON_UNESCAPED_UNICODE);
        } else {
            //导入文件是否正确
            if ($_FILES["FileName"]["error"] > 0) {
                return json_encode(['code' => 500, 'msg' => $_FILES["FileName"]["error"]]);
            } else if ($_FILES['FileName']['type'] == 'application/vnd.ms-excel' || $_FILES['FileName']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['FileName']['type'] == 'application/octet-stream') {
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
                    //组装数据
                    $data = [self::InquiryTemp];
                    foreach ($sheetData as $k => $v) {
                        if ($k > 1) {
                            $brand = trim($v['A']) ?? '';
                            $goods_number = trim($v['B']) ?? '';
                            $type = trim($v['C']) ?? '';
                            $info = [$brand, $goods_number, $type];
                            if (!$brand || !$goods_number || !$type) {
                                $info[] = '上传数据为空跳出';
                                $data[] = $info;
                                continue;
                            }
                            $orderBy = $type == '最高' ? SORT_DESC : SORT_ASC;
                            $select = [
                                'inquiry.price', 'inquiry.delivery_time',
                                'A.username',
                                'inquiry.is_purchase', 'inquiry.created_at', 'inquiry.remark', 'inquiry.technique_remark',
                                'goods.goods_number_b', 'goods.original_company',
                            ];
                            $Inquiry_info = Inquiry::find()->select($select)
                                ->where(['goods.goods_number' => $goods_number, 'goods.material_code' => $brand])
                                ->join('LEFT JOIN', Goods::tableName(), 'inquiry.good_id = goods.id')
                                ->join('LEFT JOIN', admin::tableName() . " AS A", 'A.id = inquiry.admin_id')
                                ->orderBy(['inquiry.price' => $orderBy])->asArray()->one();
                            if (empty($Inquiry_info)) {
                                $info[] = '查询为空跳出';
                                $data[] = $info;
                                continue;
                            }
                            $info[] = $Inquiry_info['price'];
                            $info[] = $Inquiry_info['delivery_time'];
                            $info[] = $Inquiry_info['username'] ?? '';
                            $info[] = $Inquiry_info['is_purchase'] ? '是' : '否';
                            $info[] = $Inquiry_info['created_at'];
                            $info[] = $Inquiry_info['remark'];
                            $info[] = $Inquiry_info['technique_remark'];
                            $info[] = $Inquiry_info['goods_number_b'];
                            $info[] = $Inquiry_info['original_company'];
                            $data[] = $info;
                            // 根据品牌和零件号查询零件信息
//                $goods_number_info = Goods::find()
//                    ->where(['goods_number' => $goods_number, 'material_code' => $brand])
//                    ->asArray([''])->one();
//                if (empty($goods_number_info)) {
//                    $info[] = '零件查询为空跳出';
//                    $data[] = $info;
//                    continue;
//                }
//                $Inquiry_info = Inquiry::find()->with('admin')->where(['good_id' => $goods_number_info['id']])->orderBy(['price' => $orderBy])->asArray()->one();
//                if (empty($Inquiry_info)) {
//                    $info[] = '询价查询为空跳出';
//                    $data[] = $info;
//                    continue;
//                }
//                $info[] = $Inquiry_info['price'];
//                $info[] = $Inquiry_info['delivery_time'];
//                $info[] = $Inquiry_info['admin']['username'] ?? '';
//                $info[] = $Inquiry_info['is_purchase'] ? '是' : '否';
//                $info[] = $Inquiry_info['created_at'];
//                $info[] = $Inquiry_info['remark'];
//                $info[] = $Inquiry_info['technique_remark'];
//                $info[] = $goods_number_info['goods_number_b'];
//                $info[] = $goods_number_info['original_company'];
//                $data[] = $info;
                        }
                    }
                    if (count($data) > 1) {
                        $cache->set($key_name, json_encode($data), 60);
                    }
                    unlink('./' . $saveName);
                    return json_encode(['code' => 200, 'msg' => '数据生成成功'], JSON_UNESCAPED_UNICODE);
                }
                return json_encode(['code' => 500, 'msg' => "数据生成失败"], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
