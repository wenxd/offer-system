<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\{Admin,
    AgreementGoods,
    Brand,
    GoodsPublish,
    GoodsRelation,
    PaymentGoods,
    Stock,
    Goods,
    GoodsSearch,
    Inquiry,
    CompetitorGoods,
    OrderGoods,
    OrderInquiry,
    SystemConfig,
    TempOrderInquiry
};
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use yii\base\ErrorException;
use yii\base\UserException;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data' => function () {
                    $searchModel = new GoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class' => actions\CreateAction::className(),
                'modelClass' => Goods::className(),
                'scenario' => 'goods',
            ],
            'update' => [
                'class' => actions\UpdateAction::className(),
                'modelClass' => Goods::className(),
                'scenario' => 'goods',
            ],
            'delete' => [
                'class' => actions\DeleteAction::className(),
                'modelClass' => Goods::className(),
            ],
            'sort' => [
                'class' => actions\SortAction::className(),
                'modelClass' => Goods::className(),
            ],
            'status' => [
                'class' => actions\StatusAction::className(),
                'modelClass' => Goods::className(),
            ],
        ];
    }

    /**
     * 生成采购策略
     */
    public function actionIndex2()
    {
        $searchModel = new GoodsPublish();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        return $this->render('index2', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 零件锁定
     */
    public function actionLocking($id)
    {
        $model = Goods::findOne($id);
        if ($model->locking == 1) {
            $model->locking = 0;
            $msg = '解锁';
        } else {
            $model->locking = 1;
            $msg = '锁定';
        }
        if ($model->save()) {
            return json_encode(['code' => 200, 'msg' => "{$msg}  成功"]);
        }
        return json_encode(['code' => 500, 'msg' => "{$msg}  失败"]);
    }

    /**获取商品编号
     * @return string
     */
    public function actionGetNumber()
    {
        $goods_number = Yii::$app->request->get('goods_number');

        $goods = Goods::findOne(['goods_number' => $goods_number, 'is_deleted' => Goods::IS_DELETED_NO]);

        if ($goods) {
            return json_encode(['code' => 200, 'data' => $goods->id]);
        } else {
            return json_encode(['code' => 500, 'msg' => '没有数据']);
        }
    }

    public function actionManage()
    {
        $data = [];
        return $this->render('manage', $data);
    }

    public function actionSearchResult()
    {
        $goods_id = Yii::$app->request->get('goods_id');
        $goods = Goods::findOne($goods_id);
        if (!$goods) {
            yii::$app->getSession()->setFlash('error', '没有此零件');
            return $this->redirect(yii::$app->request->headers['referer']);
        }

        //库存记录
        $stockQuery = Stock::find()->andWhere(['good_id' => $goods_id])->orderBy('updated_at Desc')->one();

        //询价记录 价格最优
        $inquiryPriceQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('price asc, Created_at Desc')->one();
        //同期最短(货期)
        $inquiryTimeQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('delivery_time asc, Created_at Desc')->one();
        //最新报价
        $inquiryNewQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('Created_at Desc')->one();
        //优选记录
        $inquiryBetterQuery = Inquiry::find()->where(['good_id' => $goods_id, 'is_better' => Inquiry::IS_BETTER_YES, 'is_confirm_better' => 1])->one();

        //采购记录  最新采购
        $paymentNew = PaymentGoods::find()->andWhere(['goods_id' => $goods_id, 'is_payment' => PaymentGoods::IS_PAYMENT_YES])->orderBy('created_at Desc')->one();
        //价格最低采购
        $paymentPrice = PaymentGoods::find()->andWhere(['goods_id' => $goods_id, 'is_payment' => PaymentGoods::IS_PAYMENT_YES])->orderBy('fixed_price asc')->one();
        //货期采购
        $paymentDay = PaymentGoods::find()->andWhere(['goods_id' => $goods_id, 'is_payment' => PaymentGoods::IS_PAYMENT_YES])->orderBy('delivery_time asc')->one();

        //收入记录 最新
        $agreementGoodsNew = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->one();
        //最高价
        $agreementGoodsHigh = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('quote_price Desc')->one();
        //最低价
        $agreementGoodsLow = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('quote_price asc')->one();

        //竞争对手 发行价
        $competitorGoodsIssue = CompetitorGoods::find()->where(['goods_id' => $goods_id, 'is_issue' => CompetitorGoods::IS_ISSUE_YES])->one();
        //最新
        $competitorGoodsNew = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('updated_at Desc')->one();
        //最高价
        $competitorGoodsHigh = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('price Desc')->one();
        //最低价
        $competitorGoodsLow = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('price asc')->one();

        $data = [];
        $data['goods'] = $goods ? $goods : [];

        $data['stock'] = $stockQuery;

        $data['inquiryPrice'] = $inquiryPriceQuery;
        $data['inquiryTime'] = $inquiryTimeQuery;
        $data['inquiryNew'] = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;

        $data['paymentNew'] = $paymentNew;
        $data['paymentPrice'] = $paymentPrice;
        $data['paymentDay'] = $paymentDay;

        $data['agreementGoodsNew'] = $agreementGoodsNew;
        $data['agreementGoodsHigh'] = $agreementGoodsHigh;
        $data['agreementGoodsLow'] = $agreementGoodsLow;

        $data['competitorGoodsIssue'] = $competitorGoodsIssue;
        $data['competitorGoodsNew'] = $competitorGoodsNew;
        $data['competitorGoodsHigh'] = $competitorGoodsHigh;
        $data['competitorGoodsLow'] = $competitorGoodsLow;

        //所有用户
        $adminList = Admin::find()->indexBy('id')->all();
        $data['adminList'] = $adminList;

        return $this->render('search-result', $data);
    }

    public function actionGetInfo()
    {
        $goods_id = Yii::$app->request->get('goods_id');

        $goods = Goods::findOne($goods_id);
        $orderGoods = OrderGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->asArray()->one();
        $orderInquiry = OrderInquiry::find()->where(['order_id' => $orderGoods['order_id']])->orderBy('created_at Desc')->asArray()->one();

        $data = [];
        $data['goods'] = $goods->toArray();
        $data['orderGoods'] = $orderGoods;
        $data['orderInquiry'] = $orderInquiry;

        return json_encode(['code' => 200, 'data' => $data]);
    }

    /**
     * 下载零件模板
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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'];
        $tableHeader = ['品牌', '零件号', '中文描述', '英文描述', '原厂家', '厂家号', '材质', 'TZ', '加工', '标准', '进口', '紧急', '大修',
            '锁定(是/否)', '特制', '铭牌', '所属设备', '所属部位', '建议库存', '设备用量', '单位', '技术', '原厂家备注', '零件备注', '发行含税单价',
            '发行货期', '预估发行价', '导入类别', '发行税率', '美金出厂价', '发行价类别', '准确(是/否)', '有价(是/否)'];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        $title = '零件上传模板' . date('ymd-His');
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
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
     *批量上传零件
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
                $err = [];
                if (file_exists($saveName)) {
                    //获取excel对象
                    $spreadsheet = IOFactory::load($saveName);
                    //数据转换为数组
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    //总数
                    $total = count($sheetData);
                    $num = 0;

                    $high_stock_ratio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_HIGH_STOCK_RATIO])->scalar();
                    $low_stock_ratio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_LOW_STOCK_RATIO])->scalar();
                    //汇率
                    $rate = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_RATE, 'is_deleted' => SystemConfig::IS_DELETED_NO])->scalar();
                    //到货系数
                    $arrivalRatio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_ARRIVAL_RATIO, 'is_deleted' => SystemConfig::IS_DELETED_NO])->scalar();
                    //税率
                    $tax = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_TAX, 'is_deleted' => SystemConfig::IS_DELETED_NO])->scalar();
                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            if (empty($value['B']) && empty($value['F'])) {
                                continue;
                            }

                            $brand = Brand::find()->where(['name' => trim($value['A'])])->one();
                            if (!$brand) {
                                $err[] = $key;
                                continue;
//                                return json_encode(['code' => 500, 'msg' => '品牌' . trim($value['A']) . '不存在，清先添加此品牌'], JSON_UNESCAPED_UNICODE);
                            }
                            if (trim($value['B'])) {
                                $goods = Goods::find()->where([
                                    'is_deleted' => Goods::IS_DELETED_NO,
                                    'goods_number' => trim($value['B']),
                                    'brand_id' => $brand->id,
                                ])->one();
                            } else {
                                $goods = Goods::find()->where([
                                    'is_deleted' => Goods::IS_DELETED_NO,
                                    'goods_number_b' => trim($value['F']),
                                    'brand_id' => $brand->id,
                                ])->one();
                            }
                            if (!$goods) {
                                $goods = new Goods();
                            }
                            // 判断上次数据中锁定状态，批量锁，以导入为准（要不导入就没意义了）。批量未锁，以线上为准。
                            if (!in_array($value['N'], ['是', '否'])) $value['N'] = '否';
                            $locking = true;
                            if ($value['N'] == '否') {
                                if (isset($goods->locking) && $goods->locking == 1) {
                                    $locking = false;
                                } else {
                                    $goods->locking = 0;
                                }
                            } else {
                                $goods->locking = 1;
                            }

                            //零件号
                            if ($value['B']) {
                                $goods->goods_number = trim($value['B']);
                            }
                            //中文描述
                            if ($value['C']) {
                                $goods->description = (string)trim($value['C']);
                            }
                            //英文描述
                            if ($value['D']) {
                                $goods->description_en = (string)trim($value['D']);
                            }
                            //原厂家
                            if ($value['E'] && $locking) {
                                $goods->original_company = (string)trim($value['E']);
                            }
                            //厂家号
                            if ($value['F'] && $locking) {
                                $goods->goods_number_b = (string)trim($value['F']);
                            }
                            //材质
                            if ($value['G']) {
                                $goods->material = (string)trim($value['G']);
                            }
                            //是否TZ
                            if ($value['H'] && $value['H'] == '是') {
                                $goods->is_tz = Goods::IS_TZ_YES;
                            } else {
                                $goods->is_tz = Goods::IS_TZ_NO;
                            }
                            //加工
                            if ($value['I'] && $value['I'] == '是') {
                                $goods->is_process = Goods::IS_PROCESS_YES;
                            } else {
                                $goods->is_process = Goods::IS_PROCESS_NO;
                            }
                            //标准
                            if ($value['J'] && $value['J'] == '是') {
                                $goods->is_standard = Goods::IS_STANDARD_YES;
                            } else {
                                $goods->is_standard = Goods::IS_STANDARD_NO;
                            }
                            //进口
                            if ($value['K'] && $value['K'] == '是') {
                                $goods->is_import = Goods::IS_IMPORT_YES;
                            } else {
                                $goods->is_import = Goods::IS_IMPORT_NO;
                            }
                            //紧急
                            if ($value['L'] && $value['L'] == '是') {
                                $goods->is_emerg = Goods::IS_EMERG_YES;
                            } else {
                                $goods->is_emerg = Goods::IS_EMERG_NO;
                            }
                            //大修
                            if ($value['M'] && $value['M'] == '是') {
                                $goods->is_repair = Goods::IS_REPAIR_YES;
                            } else {
                                $goods->is_repair = Goods::IS_REPAIR_NO;
                            }
                            //总成
//                            if ($value['N'] && $value['N'] == '是') {
//                                $goods->is_assembly = Goods::IS_ASSEMBLY_YES;
//                            } else {
//                                $goods->is_assembly = Goods::IS_ASSEMBLY_NO;
//                            }
                            //特制
                            if ($value['O'] && $value['O'] == '是') {
                                $goods->is_special = Goods::IS_SPECIAL_YES;
                            } else {
                                $goods->is_special = Goods::IS_SPECIAL_NO;
                            }
                            //铭牌
                            if ($value['P'] && $value['P'] == '是') {
                                $goods->is_nameplate = Goods::IS_NAMEPLATE_YES;
                            } else {
                                $goods->is_nameplate = Goods::IS_NAMEPLATE_NO;
                            }
                            //所属部位
                            if ($value['R']) {
                                $goods->part = (string)trim($value['R']);
                            }
                            //单位
                            $goods->unit = $value['U'] ? trim($value['U']) : '件';
                            //技术备注、技术
                            if ($value['V']) {
                                $goods->technique_remark = (string)trim($value['V']);
                            }
                            //原厂家备注
                            if ($value['W']) {
                                $goods->original_company_remark = (string)trim($value['W']);
                            }
                            //零件备注
                            if (trim($value['X'])) {
                                $remark = $goods->remark ?? false;
                                $remark_arr = [];
                                if ($remark) {
                                    $remark_arr = explode(';', $remark ?? '');
                                }
                                $x = trim($value['X']);
                                // 符号转换
                                $x = str_replace('；', ';', $x);
                                $x_arr = explode(';', $x ?? '');
                                foreach ($x_arr as $item) {
                                    if (trim($item) && !in_array($item, $remark_arr)) {
                                        $remark_arr[] = $item;
                                    }
                                }
                                $goods->remark = implode(';', $remark_arr);
                            }
                            if ($value['Q'] && $value['T']) {
                                $deviceName = strtoupper(trim($value['Q']));
                                $deviceNumber = trim($value['T']);
                                $device = [];
                                $device[$deviceName] = $deviceNumber;
                                $oldDevice = json_decode($goods->device_info, true);
                                if ($goods->isNewRecord) {
                                    $goods->device_info = json_encode($device, JSON_UNESCAPED_UNICODE);
                                } else {
                                    //存在某个key
                                    $oldDevice = is_array($oldDevice) ? $oldDevice : [];
                                    if (array_key_exists($deviceName, $oldDevice)) {
                                        $oldDevice[$deviceName] = $deviceNumber;
                                        $newDevice = $oldDevice;
                                    } else {
                                        $newDevice = array_merge($oldDevice, $device);
                                    }
                                    $goods->device_info = json_encode($newDevice, JSON_UNESCAPED_UNICODE);
                                }
                            }
                            //发行含税单价
                            if ($value['Y']) {
                                $goods->publish_tax_price = trim($value['Y']);
                            }
                            //发行货期
                            if ($value['Z']) {
                                $goods->publish_delivery_time = trim($value['Z']);
                            }
                            //预估发行价
                            if ($value['AA']) {
                                $goods->estimate_publish_price = trim($value['AA']);
                            }
                            //品牌名称
                            if ($brand) {
                                $goods->material_code = $brand->name;
                                $goods->brand_id = $brand->id;
                            }
                            //导入类别
                            if (trim($value['AB'])) {
                                $import_mark = $goods->import_mark ?? false;
                                $import_mark_arr = [];
                                if ($import_mark) {
                                    $import_mark_arr = explode(';', $import_mark ?? '');
                                }
                                $x = (string)trim($value['AB']);
                                // 符号转换
                                $x = str_replace('；', ';', $x);
                                $x_arr = explode(';', $x ?? '');
                                foreach ($x_arr as $item) {
                                    if (trim($item) && !in_array($item, $import_mark_arr)) {
                                        $import_mark_arr[] = $item;
                                    }
                                }
                                $goods->import_mark = implode(';', $import_mark_arr);
                            }
                            //发行税率
                            if (trim($value['AC'])) {
                                $goods->publish_tax = trim($value['AC']);
                            } else {
                                $goods->publish_tax = $tax;
                            }
                            //美金出厂价
                            if (trim($value['AD'])) {
                                $goods->factory_price = trim($value['AD']);
                                $goods->publish_tax_price = $goods->factory_price * $rate * $arrivalRatio * (1 + $goods->publish_tax / 100);
                            }
                            if ($goods->save()) {
                                $num++;
                                //发行价记录(发行含税单价、预估发行价、美金出厂价，不填的导入。不做发行价记录)
                                $Y = trim($value['Y']) ?? 0;
                                $AA = trim($value['AA']) ?? 0;
                                $AD = trim($value['AD']) ?? 0;
                                $AC = trim($value['AC']) ?? 0;
                                $publish = $goods->toArray();
                                $publish['publish_tax_price'] = $Y ? $Y : 0;
                                $publish['estimate_publish_price'] = $AA ? $AA : 0;
                                $publish['factory_price'] = $AD ? $AD : 0;
                                $publish['publish_tax'] = $AC ? $AC : 0;
                                $publish['publish_type'] = trim($value['AE']);
                                $publish['is_publish_accuracy'] = trim($value['AF']) == '是' ? 1 : 0;
                                $is_price = trim($value['AG']) ?? '是';
                                $publish['is_price'] = $is_price == '否' ? 0 : 1;
                                $publish['original_company'] = (string)trim($value['E']);
                                $publish['goods_number_b'] = (string)trim($value['F']);
                                $publish['updated_at'] = date('Y-m-d H:i:s');
                                $publish['created_at'] = date('Y-m-d H:i:s');
                                $publish_status = false;
                                //正常情况，三个价格都为否，不录入。则有一个不为空则录入
                                if ($Y || $AA || $AD) {
                                    $publish_status = true;
                                } else {
                                    // 如果三个价格都为否，有价写了否，则需要生成一条三条为0的发行价记录。
                                    if (!$publish['is_price']) {
                                        $publish['publish_tax_price'] = 0;
                                        $publish['estimate_publish_price'] = 0;
                                        $publish['factory_price'] = 0;
                                        $publish_status = true;
                                    }
                                }
                                if ($publish_status) {
                                    // 去重：零件号，三个价格，发行价类别
                                    $status = GoodsPublish::find()->where([
                                        'id' => $publish['id'],
                                        'publish_tax_price' => $publish['publish_tax_price'],
                                        'estimate_publish_price' => $publish['estimate_publish_price'],
                                        'factory_price' => $publish['factory_price'],
                                        'publish_type' => $publish['publish_type'],
                                    ])->one();
                                    if (empty($status)) {
                                        $publish_model = new GoodsPublish();
                                        if ($publish['is_publish_accuracy']) {
                                            GoodsPublish::updateAll(['is_publish_accuracy' => 0], ['id' => $publish['id']]);
                                        }
                                        if (!$publish_model->load(['GoodsPublish' => $publish]) || !$publish_model->save()) {
                                            $err[] = $key;
                                        }
                                    }
                                }
                            } else {
                                $err[] = $key;
                                continue;
                                return json_encode(['code' => 500, 'msg' => current($goods->getErrors())], JSON_UNESCAPED_UNICODE);
                            }
                            //建议库存
                            if ($value['S']) {
                                $stock = Stock::find()->where(['good_id' => $goods->id])->one();
                                if (!$stock) {
                                    $stock = new Stock();
                                    $stock->good_id = $goods->id;
                                }
                                $stock->suggest_number = trim($value['S']);
                                $stock->high_number = (int)round($high_stock_ratio * trim($value['S']));
                                $stock->low_number = (int)round($low_stock_ratio * trim($value['S']));
                                $stock->save();
                            }
                        }
                    }
                }
                $err_num = count($err);
                $err_json = implode(',', $err);
                $msg = "{$num}条成功, {$err_num}条失败,失败行号($err_json)";
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条'], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    //零件直接生成询价单，进入询价临时零件库
    public function actionInquiryOrder()
    {
        $goodsIds = Yii::$app->request->post('goods_ids');
        $temp = new TempOrderInquiry();
        $temp->goods_ids = implode(',', $goodsIds);
        if ($temp->save()) {
            return json_encode(['code' => 200, 'msg' => '成功', 'data' => $temp->primaryKey], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => $temp->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    //添加子零件页面
    public function actionAddson($id)
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        $data = [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
        return $this->render('addson', $data);
    }

    //添加子零件的操作
    public function actionDoAddson()
    {
        $params = Yii::$app->request->post();

        $pGoodsId = $params['p_goods_id'];
        $goodsIds = $params['goods_list'];

        //循环处理子零件
        foreach ($goodsIds as $key => $record) {
            $goodsRelation = GoodsRelation::find()->where([
                'p_goods_id' => $pGoodsId,
                'goods_id' => $record['goods_id'],
                'is_deleted' => GoodsRelation::IS_DELETED_NO,
            ])->one();
            if ($goodsRelation) {
                $goodsRelation->number = $record['number'];
                $goodsRelation->is_deleted = GoodsRelation::IS_DELETED_NO;
            } else {
                //循环判断上下级零件互斥
                $mutex_res = GoodsRelation::goodsMutex($record['goods_id'], [$pGoodsId, $record['goods_id']]);
                if ($mutex_res) {
                    return $this->success(500, '添加失败，互斥错误');
                    continue;
                }
                $goodsRelation = new GoodsRelation();
                $goodsRelation->p_goods_id = $pGoodsId;
                $goodsRelation->goods_id = $record['goods_id'];
                $goodsRelation->number = $record['number'];
            }
            if (!$goodsRelation->save()) {
                return $this->error(500, $goodsRelation->getErrors());
            }
        }

        //修改零件为总成
        $goods = Goods::findOne($pGoodsId);
        $goods->is_assembly = Goods::IS_ASSEMBLY_YES;
        $goods->save();

        return $this->success(200, '添加成功');
    }

    /**
     * 零件详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = Goods::findOne($id);

        $goodsRelationList = GoodsRelation::find()->where([
            'p_goods_id' => $id,
            'is_deleted' => GoodsRelation::IS_DELETED_NO,
        ])->all();

        return $this->render('view', [
            'model' => $model,
            'goodsList' => $goodsRelationList,
        ]);
    }

    public function actionDeleteSon()
    {
        $params = Yii::$app->request->post();

        $pGoodsId = $params['p_goods_id'];
        $goodsId = $params['goods_id'];

        $goodsRelation = GoodsRelation::find()->where([
            'p_goods_id' => $pGoodsId,
            'goods_id' => $goodsId,
            'is_deleted' => GoodsRelation::IS_DELETED_NO,
        ])->one();
        if ($goodsRelation) {
            $goodsRelation->is_deleted = GoodsRelation::IS_DELETED_YES;
            $goodsRelation->save();
            // 计算是不是已经没有子零件了
            if (GoodsRelation::find()->where(['p_goods_id' => $pGoodsId, 'is_deleted' => GoodsRelation::IS_DELETED_NO,])->count() == 0) {
                Goods::updateAll(['is_assembly' => 0], ['id' => $pGoodsId]);
            }
            return $this->success(200, '删除成功');
        } else {
            return $this->error(500, '没有此关联关系');
        }
    }

    /**
     * 下载零件模板(子)
     */
    public function actionDownloadSon()
    {
//        $fileName = '零件模板(子).csv';
//        $columns = ["品牌", "零件号", "品牌(子)", "零件号(子)", "数量(子)",];
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment; filename="' . $fileName . '"');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        $fp = fopen('php://output', 'a');//打开output流
//        mb_convert_variables('GBK', 'UTF-8', $columns);
//        fputcsv($fp, $columns);
//        ob_flush();
//        flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
//        fclose($fp);
//        exit();
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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E'];
        $tableHeader = ["品牌", "零件号", "品牌(子)", "零件号(子)", "数量(子)",];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        $title = '零件模板(子)' . date('ymd-His');
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
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
     * 批量上传零件(子)
     */
    public function actionUploadSon()
    {
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            return json_encode(['code' => 500, 'msg' => '没有检测到上传文件']);
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
                    $info = [];
                    $err = [];
                    //组装数据
                    foreach ($sheetData as $k => $v) {
                        if ($k > 1) {
                            $number = trim($v['E']);
                            if (!is_numeric($number) || $number < 1) {
                                $err[] = $k;
                                continue;
                            }
                            //获取顶级零件数据
                            $top_part = GoodsSearch::getGoods(trim($v['A']), trim($v['B']));
                            if (!$top_part) {
                                $err[] = $k;
                                continue;
                            }
                            //获取子级零件数据
                            $son_part = GoodsSearch::getGoods(trim($v['C']), trim($v['D']));
                            if (!$son_part) {
                                $err[] = $k;
                                continue;
                            }
                            //循环判断上下级零件互斥
                            $mutex_res = GoodsRelation::goodsMutex($son_part['id'], [$top_part['id'], $son_part['id']]);
                            if ($mutex_res) {
                                $err[] = $k;
                                continue;
                            }
                            $info[$top_part['id']][] = [
                                'p_goods_id' => $top_part['id'],
                                'goods_id' => $son_part['id'],
                                'number' => trim($v['E']),
                            ];
                        }
                    }
                    //循环保存(更改顶级为软删除)
                    $num = 0;
                    $model = new GoodsRelation();
                    foreach ($info as $k => $v) {
                        GoodsRelation::updateAll(['is_deleted' => GoodsRelation::IS_DELETED_YES], ['p_goods_id' => $k]);
                        Goods::updateAll(['is_assembly' => Goods::IS_ASSEMBLY_YES], ['id' => $k]);
                        foreach ($v as $item) {
                            $model->isNewRecord = true;
                            $model->setAttributes($item);
                            $model->save() && $model->id = 0;
                            $num++;
                        }
                    }
                    $msg = "{$num}条成功";
                    $err_num = count($err);
                    if ($err_num > 0) {
                        $err_json = implode(',', $err);
                        $msg = "{$num}条成功, {$err_num}条失败,失败行号($err_json)";
                    }
                    unlink('./' . $saveName);
                    return json_encode(['code' => 200, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
                }
                return json_encode(['code' => 500, 'msg' => "数据保存失败"], JSON_UNESCAPED_UNICODE);
            }

        }
    }

    /**
     * 下载零件校验模板
     */
    public function actionDownloadCheck()
    {
//        $fileName = '检测模板.csv';
//        $columns = ["品牌", "零件号", "是否有零件号", "是否有厂家号", "是否锁定", "是否有发行未税单价", "是否TZ", "是否总成", "是否加工", "是否标准", "是否询价", "是否有库存",];
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment; filename="' . $fileName . '"');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        $fp = fopen('php://output', 'a');//打开output流
//        mb_convert_variables('GBK', 'UTF-8', $columns);
//        fputcsv($fp, $columns);
//        ob_flush();
//        flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
//        fclose($fp);
//        exit();
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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        $tableHeader = ["品牌", "零件号", "是否有零件号", "是否有厂家号", "是否锁定", "是否有发行未税单价", "是否TZ", "是否总成", "是否加工", "是否标准", "是否询价", "是否有库存", "发行未税单价", "设备信息"];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        $title = '检测模板' . date('ymd-His');
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
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
     * 上传校验模板
     */
    public function actionUploadCheck()
    {
        $cache = Yii::$app->cache;
        $key_name = 'goods_number_check';
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            if ($cache->exists($key_name)) {
                $data = json_decode($cache->get($key_name), true);
                $cache->delete($key_name);
                $fileName = '检测结果.csv';
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
                    $data = [
                        ["品牌", "零件号", "是否有零件号", "是否有厂家号", "是否锁定", "是否有发行未税单价", "是否TZ", "是否总成", "是否加工", "是否标准", "是否询价", "是否有库存", "发行未税单价", "设备信息"]
                    ];
                    foreach ($sheetData as $k => $v) {
                        if ($k > 1) {
                            $brand = trim($v['A']);
                            $goods_number = trim($v['B']);
                            $info = [$brand, $goods_number];
                            $goods = Goods::find()
                                ->with('inquirylow')
                                ->with('stock')
                                ->where(['is_deleted' => Goods::IS_DELETED_NO, 'goods_number' => $goods_number, 'material_code' => $brand])
                                ->asArray()->one();
                            if (empty($goods)) {
                                $info[] = '否';
                            } else {
                                $info[] = '是';
                                $info[] = !empty($goods['goods_number_b']) ? '是' : '否';
                                $info[] = $goods['locking'] == 1 ? '是' : '否';
                                $info[] = $goods['publish_tax_price'] > 0 ? '是' : '否';
                                $info[] = $goods['is_tz'] ? '是' : '否';
                                $info[] = $goods['is_assembly'] ? '是' : '否';
                                $info[] = $goods['is_process'] ? '是' : '否';
                                $info[] = $goods['is_standard'] ? '是' : '否';
                                $info[] = !empty($goods['inquirylow']) ? '是' : '否';
                                $info[] = !empty($goods['stock']) && $goods['stock']['number'] > 0 ? '是' : '否';
                                $info[] = $goods['publish_price'];
                                $text = '';
                                if ($goods['publish_price']) {
                                    foreach (json_decode($goods['publish_price'], true) as $key => $device) {
                                        $text .= $key . ':' . $device . '<br/>';
                                    }
                                }
                                $info[] = $text;


                            }
                            $data[] = $info;
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

    /**
     * 下载零件校验模板02
     */
    public function actionDownloadCheck02()
    {
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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E'];
        $tableHeader = ["随意号码", "是否存在", "零件号第几行", "零件号备注第几行", "厂家号第几行"];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        $title = '检测模板02' . date('ymd-His');
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
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
     * 上传校验模板02
     */
    public function actionUploadCheck02()
    {
        $cache = Yii::$app->cache;
        $key_name = 'goods_number_check02';
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            if ($cache->exists($key_name)) {
                $data = json_decode($cache->get($key_name), true);
                $cache->delete($key_name);
                $fileName = '检测结果02.csv';
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
                    $data = [
                        ["随意号码", "是否存在", "零件号第几行", "零件号备注第几行", "厂家号第几行"]
                    ];
                    foreach ($sheetData as $k => $v) {
                        if ($k > 1) {
                            $goods = trim($v['A']);
                            $is_exist = '否';
                            // 零件号第几行
                            $goods_number_str = '';
                            $goods_number = Goods::find()->select(['id'])->where(['like', 'goods_number', $goods])->asArray()->all();
                            if (!empty($goods_number)) {
                                $is_exist = '是';
                                $goods_number_str = implode('，', array_column($goods_number, 'id'));
                            }
                            //零件号备注第几行
                            $remark_str = '';
                            $remark = Goods::find()->select(['id'])->where(['like', 'remark', $goods])->asArray()->all();
                            if (!empty($remark)) {
                                $is_exist = '是';
                                $remark_str = implode('，', array_column($remark, 'id'));
                            }
                            //厂家号第几行
                            $original_company_str = '';
                            $original_company = Goods::find()->select(['id'])->where(['like', 'original_company', $goods])->asArray()->all();
                            if (!empty($original_company)) {
                                $is_exist = '是';
                                $original_company_str = implode('，', array_column($original_company, 'id'));
                            }
                            $data[] = [$goods, $is_exist, $goods_number_str, $remark_str, $original_company_str];
                        }
                    }
                    if (count($data) > 1) {
                        $cache->set($key_name, json_encode($data), 60);
                    }
//                    unlink('./' . $saveName);
                    return json_encode(['code' => 200, 'msg' => '数据生成成功'], JSON_UNESCAPED_UNICODE);
                }
                return json_encode(['code' => 500, 'msg' => "数据生成失败"], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
