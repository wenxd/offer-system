<?php

namespace app\controllers;

use Yii;
use app\actions;
use app\models\{AgreementGoods,
    PaymentGoods,
    Stock,
    Goods,
    GoodsSearch,
    Inquiry,
    CompetitorGoods,
    OrderGoods,
    OrderInquiry,
    PurchaseGoods,
    StockLog};
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

        //价格最优
        $inquiryPriceQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('price asc')->one();
        //同期最短
        $inquiryTimeQuery = Inquiry::find()->where(['good_id' => $goods_id])->orderBy('delivery_time asc')->one();
        //最新报价
        $inquiryNewQuery = Inquiry::find()->where(['good_id' => $goods_id, 'is_newest' => Inquiry::IS_NEWEST_YES])->orderBy('updated_at Desc')->one();
        //优选记录
        $inquiryBetterQuery = Inquiry::find()->where(['good_id' => $goods_id, 'is_better' => Inquiry::IS_BETTER_YES])->orderBy('updated_at Desc')->one();

        //库存记录
        $stockQuery = Stock::find()->andWhere(['good_id' => $goods_id])->orderBy('updated_at Desc')->one();

        //采购记录
        $purchaseInquiry = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id, 'type' => PurchaseGoods::TYPE_INQUIRY])->all();
        $price = 100000000;
        $offerDay = 10000000;
        $purchasePrice = '';
        $purchaseDay = '';
        foreach ($purchaseInquiry as $item) {
            if ($item->inquiry->price < $price) {
                $price = $item->inquiry->price;
                $purchasePrice = $item;
            }
            if ($item->inquiry->delivery_time < $offerDay) {
                $offerDay = $item->inquiry->delivery_time;
                $purchaseDay = $item;
            }
        }
        $purchaseStock = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id, 'type' => PurchaseGoods::TYPE_STOCK])->all();
        foreach ($purchaseStock as $item) {
            if ($item->stock->price < $price) {
                $price = $item->stock->price;
                $purchasePrice = $item;
            }
        }

        //最新采购
        $purchaseNew = PurchaseGoods::find()->andWhere(['goods_id' => $goods_id])->orderBy('created_at Desc')->one();

        //竞争对手
        $competitorGoods = CompetitorGoods::find()->where(['goods_id' => $goods_id])->orderBy('updated_at Desc')->one();


        //最后三条入库的

        $stockLog = StockLog::find()->where(['type' => StockLog::TYPE_IN, 'goods_id' => $goods_id])
            ->orderBy('operate_time Desc')->limit(3)->all();
        $order_ids = ArrayHelper::getColumn($stockLog, 'order_id');
        $order_purchase_ids = ArrayHelper::getColumn($stockLog, 'order_purchase_id');

        $purchaseGoods = PurchaseGoods::find()->where(['order_id' => $order_ids, 'order_purchase_id' => $order_purchase_ids, 'goods_id' => $goods_id])->all();

        $inquiry_ids = [];
        $stock_ids   = [];
        foreach ($purchaseGoods as $key => $item) {
            if ($item->type) {
                $stock_ids[] = $item->relevance_id;
            } else {
                $inquiry_ids[] = $item->relevance_id;
            }
        }

        $average = Inquiry::find()->where(['id' => $inquiry_ids])->average('price');

        $data = [];
        $data['goods']            = $goods ? $goods : [];
        $data['inquiryPrice']     = $inquiryPriceQuery;
        $data['inquiryTime']      = $inquiryTimeQuery;
        $data['inquiryNew']       = $inquiryNewQuery;
        $data['inquiryBetter']    = $inquiryBetterQuery;
        $data['stock']            = $stockQuery;

        $data['purchasePrice']    = $purchasePrice;
        $data['purchaseDay']      = $purchaseDay;
        $data['purchaseNew']      = $purchaseNew;

        $data['competitorGoods']  = $competitorGoods;
        $data['competitorGoods']  = $competitorGoods;

        $data['average']          = $average;

        //增加零件的收入记录
        $agreementGoods = AgreementGoods::find()->where(['goods_id' => $goods_id])->orderBy('created_at Desc')->limit(3)->all();
        $data['agreementGoods'] = $agreementGoods;

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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $tableHeader = ['零件号A', '零件号B', '中文描述', '英文描述', '原厂家', '原厂家备注', '设备信息用|分组'];
        $tableValues = '设备1=3|设备2=4|设备5=5';
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            if ($i == 6) {
                $excel->getColumnDimension($letter[$i])->setWidth(32);
            }
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
            if ($i == 6) {
                $excel->setCellValue($letter[$i].'2',$tableValues);
            }
        }

        $title = '零件上传模板';
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
                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            if (empty($value['A']) && empty($value['B'])) {
                                continue;
                            }
                            $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['or', ['goods_number' => trim($value['A'])], ['goods_number_b' => trim($value['B'])]])->one();
                            if (!$goods) {
                                $goods = new Goods();
                            }
                            if ($value['A']) {
                                $goods->goods_number = trim($value['A']);
                            }
                            if ($value['B']) {
                                $goods->goods_number_b = trim($value['B']);
                            }
                            if ($value['C']) {
                                $goods->description = trim($value['C']);
                            }
                            if ($value['D']) {
                                $goods->description_en = trim($value['D']);
                            }
                            if ($value['E']) {
                                $goods->original_company = trim($value['E']);
                            }
                            if ($value['F']) {
                                $goods->original_company_remark = trim($value['F']);
                            }
                            if ($value['G']) {
                                $deviceList = explode('|', trim($value['G']));
                                $device_info = [];
                                foreach ($deviceList as $device) {
                                    list($name, $number) = explode('=', $device);
                                    $device_info[$name] = $number;
                                }
                                $goods->device_info = json_encode($device_info, JSON_UNESCAPED_UNICODE);
                            }
                            if ($goods->save()) {
                                $num++;
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
