<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\{Admin,
    AgreementGoods,
    PaymentGoods,
    Stock,
    Goods,
    GoodsSearch,
    Inquiry,
    CompetitorGoods,
    OrderGoods,
    OrderInquiry,
    PurchaseGoods,
    StockLog,
    SystemConfig,
    TempOrderInquiry};
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
                'data'  => function(){
                    $searchModel  = new GoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Goods::className(),
                'scenario'   => 'goods',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Goods::className(),
                'scenario'   => 'goods',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Goods::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Goods::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Goods::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Goods::className(),
            ],
        ];
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
        $good_number = (string)Yii::$app->request->get('good_number');
        $goods = Goods::find()->where(['goods_number' => $good_number, 'is_deleted' => Goods::IS_DELETED_NO])->one();
        if (!$goods) {
            yii::$app->getSession()->setFlash('error', '没有此零件');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $goods_id = $goods->id;

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
        $agreementGoodsNew  = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->one();
        //最高价
        $agreementGoodsHigh = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('quote_price Desc')->one();
        //最低价
        $agreementGoodsLow  = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('quote_price asc')->one();

        //竞争对手 发行价
        $competitorGoodsIssue  = CompetitorGoods::find()->where(['goods_id' => $goods_id, 'is_issue' => CompetitorGoods::IS_ISSUE_YES])->one();
        //最新
        $competitorGoodsNew  = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('updated_at Desc')->one();
        //最高价
        $competitorGoodsHigh = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('price Desc')->one();
        //最低价
        $competitorGoodsLow  = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('price asc')->one();

        $data = [];
        $data['goods']            = $goods ? $goods : [];

        $data['stock']            = $stockQuery;

        $data['inquiryPrice']     = $inquiryPriceQuery;
        $data['inquiryTime']      = $inquiryTimeQuery;
        $data['inquiryNew']       = $inquiryNewQuery;
        $data['inquiryBetter']    = $inquiryBetterQuery;

        $data['paymentNew']       = $paymentNew;
        $data['paymentPrice']     = $paymentPrice;
        $data['paymentDay']       = $paymentDay;

        $data['agreementGoodsNew']  = $agreementGoodsNew;
        $data['agreementGoodsHigh'] = $agreementGoodsHigh;
        $data['agreementGoodsLow']  = $agreementGoodsLow;

        $data['competitorGoodsIssue']  = $competitorGoodsIssue;
        $data['competitorGoodsNew']    = $competitorGoodsNew;
        $data['competitorGoodsHigh']   = $competitorGoodsHigh;
        $data['competitorGoodsLow']    = $competitorGoodsLow;

        //所有用户
        $adminList = Admin::find()->indexBy('id')->all();
        $data['adminList'] = $adminList;

        return $this->render('search-result', $data);
    }

    public function actionGetInfo()
    {
        $goods_id = Yii::$app->request->get('goods_id');

        $goods        = Goods::findOne($goods_id);
        $orderGoods   = OrderGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->asArray()->one();
        $orderInquiry = OrderInquiry::find()->where(['order_id' => $orderGoods['order_id']])->orderBy('created_at Desc')->asArray()->one();

        $data                 = [];
        $data['goods']        = $goods->toArray();
        $data['orderGoods']   = $orderGoods;
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
        $excel=$spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC'];
        $tableHeader = ['零件号', '中文描述', '英文描述', '原厂家', '厂家号', '材质', 'TZ', '加工', '标准', '进口', '紧急', '大修',
            '总成', '特制', '铭牌', '所属设备', '所属部位', '建议库存', '设备用量', '单位', '技术', '原厂家备注', '零件备注', '发行含税单价',
            '发行货期', '预估发行价', '设备类别', '导入类别', '发行税率'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '零件上传模板' . date('ymd-His');
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
                if (file_exists($saveName)) {
                    //获取excel对象
                    $spreadsheet = IOFactory::load($saveName);
                    //数据转换为数组
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    //总数
                    $total = count($sheetData);
                    $num = 0;

                    $high_stock_ratio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_HIGH_STOCK_RATIO])->scalar();
                    $low_stock_ratio  = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_LOW_STOCK_RATIO])->scalar();

                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            if (empty($value['A']) && empty($value['E'])) {
                                continue;
                            }
                            if (trim($value['A'])) {
                                $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO, 'goods_number' => trim($value['A'])])->one();
                            } else {
                                $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO, 'goods_number_b' => trim($value['E'])])->one();
                            }
                            if (!$goods) {
                                $goods = new Goods();
                            }
                            //零件号
                            if ($value['A']) {
                                $goods->goods_number = trim($value['A']);
                            }
                            //中文描述
                            if ($value['B']) {
                                $goods->description = (string)trim($value['B']);
                            }
                            //英文描述
                            if ($value['C']) {
                                $goods->description_en = (string)trim($value['C']);
                            }
                            //原厂家
                            if ($value['D']) {
                                $goods->original_company = (string)trim($value['D']);
                            }
                            //厂家号
                            if ($value['E']) {
                                $goods->goods_number_b = (string)trim($value['E']);
                            }
                            //材质
                            if ($value['F']) {
                                $goods->material = (string)trim($value['F']);
                            }
                            //是否TZ
                            if ($value['G'] && $value['G'] == '是') {
                                $goods->is_tz = Goods::IS_TZ_YES;
                            } else {
                                $goods->is_tz = Goods::IS_TZ_NO;
                            }
                            //加工
                            if ($value['H'] && $value['H'] == '是') {
                                $goods->is_process = Goods::IS_PROCESS_YES;
                            } else {
                                $goods->is_process = Goods::IS_PROCESS_NO;
                            }
                            //标准
                            if ($value['I'] && $value['I'] == '是') {
                                $goods->is_standard = Goods::IS_STANDARD_YES;
                            } else {
                                $goods->is_standard = Goods::IS_STANDARD_NO;
                            }
                            //进口
                            if ($value['J'] && $value['J'] == '是') {
                                $goods->is_import = Goods::IS_IMPORT_YES;
                            } else {
                                $goods->is_import = Goods::IS_IMPORT_NO;
                            }
                            //紧急
                            if ($value['K'] && $value['K'] == '是') {
                                $goods->is_emerg = Goods::IS_EMERG_YES;
                            } else {
                                $goods->is_emerg = Goods::IS_EMERG_NO;
                            }
                            //大修
                            if ($value['L'] && $value['L'] == '是') {
                                $goods->is_repair = Goods::IS_REPAIR_YES;
                            } else {
                                $goods->is_repair = Goods::IS_REPAIR_NO;
                            }
                            //总成
                            if ($value['M'] && $value['M'] == '是') {
                                $goods->is_assembly = Goods::IS_ASSEMBLY_YES;
                            } else {
                                $goods->is_assembly = Goods::IS_ASSEMBLY_NO;
                            }
                            //特制
                            if ($value['N'] && $value['N'] == '是') {
                                $goods->is_special = Goods::IS_SPECIAL_YES;
                            } else {
                                $goods->is_special = Goods::IS_SPECIAL_NO;
                            }
                            //铭牌
                            if ($value['O'] && $value['O'] == '是') {
                                $goods->is_nameplate = Goods::IS_NAMEPLATE_YES;
                            } else {
                                $goods->is_nameplate = Goods::IS_NAMEPLATE_NO;
                            }
                            //所属部位
                            if ($value['Q']) {
                                $goods->part = (string)trim($value['Q']);
                            }
                            //单位
                            $goods->unit = $value['T'] ? trim($value['T']) : '件';
                            //技术备注、技术
                            if ($value['U']) {
                                $goods->technique_remark = (string)trim($value['U']);
                            }
                            //原厂家备注
                            if ($value['V']) {
                                $goods->original_company_remark = (string)trim($value['V']);
                            }
                            //零件备注
                            if ($value['W']) {
                                $goods->remark = (string)trim($value['W']);
                            }
                            if ($value['P'] && $value['S']) {
                                $deviceName   = trim($value['P']);
                                $deviceNumber = trim($value['S']);
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
                            if ($value['X']) {
                                $goods->publish_tax_price = trim($value['X']);
                            }
                            //发行货期
                            if ($value['Y']) {
                                $goods->publish_delivery_time = trim($value['Y']);
                            }
                            //预估发行价
                            if ($value['Z']) {
                                $goods->estimate_publish_price = trim($value['Z']);
                            }
                            //物资编码
                            if ($value['AA']) {
                                $goods->material_code = (string)trim($value['AA']);
                            }
                            //物资编码
                            if ($value['AB']) {
                                $goods->import_mark = (string)trim($value['AB']);
                            }
                            //发行税率
                            if ($value['AC']) {
                                $goods->publish_tax = trim($value['AC']);
                            }
                            if ($goods->save()) {
                                $num++;
                            }
                            //建议库存
                            if ($value['R']) {
                                $stock = Stock::find()->where(['good_id' => $goods->id])->one();
                                if (!$stock) {
                                    $stock = new Stock();
                                    $stock->good_id         = $goods->id;
                                }
                                $stock->suggest_number  = trim($value['R']);
                                $stock->high_number     = (int) round($high_stock_ratio * trim($value['R']));
                                $stock->low_number      = (int) round($low_stock_ratio * trim($value['R']));
                                $stock->save();
                            }
                        }
                    }
                }
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条'], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    //零件直接生成询价单，进入询价临时零件库
    public function actionInquiryOrder()
    {
        $goodsIds = Yii::$app->request->post('goods_ids');
        $temp = new TempOrderInquiry();
        $temp->goods_ids = implode(',',$goodsIds);
        if ($temp->save()) {
            return json_encode(['code' => 200, 'msg' => '成功', 'data' => $temp->primaryKey], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => $temp->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }
}
