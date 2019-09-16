<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/24
 * Time: 14:02
 */
namespace app\controllers;

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Goods;
use app\models\InquiryGoods;
use app\models\OrderGoods;
use app\models\QuoteRecord;
use app\models\StockLog;
use app\models\Supplier;
use app\models\SystemConfig;
use app\models\SystemNotice;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\actions;
use app\models\Cart;
use app\models\Order;
use app\models\Inquiry;
use app\models\OrderInquiry;
use app\models\OrderInquirySearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class OrderInquiryController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new OrderInquirySearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ]
        ];
    }

    /*
     * 已废弃
     */
    public function actionSubmit()
    {
        $params = Yii::$app->request->get('OrderInquiry');
        $type   = Yii::$app->request->get('type');

        $orderType = 1;
        if ($type == 1) {
            $order = new OrderQuote();
        } else {
            $order = new OrderInquiry();
            $orderType = 2;
        }

        $order->customer_id  = $params['customer_id'];
        $order->order_id     = $params['order_id'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->quote_price  = $params['quote_price'];
        $order->remark       = $params['remark'];

        $order->record_ids = json_encode([], JSON_UNESCAPED_UNICODE);
        if ($order->save()) {
            $cartList = Cart::find()->all();
            $data = [];
            foreach ($cartList as $key => $cart) {
                $row = [];

                $row[] = $cart->type;
                $row[] = $cart->inquiry_id;
                $row[] = $cart->goods_id;
                $row[] = $cart->quotation_price;
                $row[] = $cart->number;
                $row[] = $order->primaryKey;
                $row[] = $orderType;
                $row[] = $params['remark'];

                $data[] = $row;
            }
            $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'remark'];
            $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
            if ($num) {
                Cart::deleteAll();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    /*
     * 已废弃
     */
    public function actionDetail($id)
    {
        $data = [];

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id, 'order_type' => QuoteRecord::TYPE_INQUIRY]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list']  = $list;

        return $this->render('detail', $data);
    }

    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderInquiry = new OrderInquiry();
        $orderInquiry->inquiry_sn = $params['inquiry_sn'];
        $orderInquiry->order_id   = $params['order_id'];
        $orderInquiry->end_date   = $params['end_date'];
        $orderInquiry->admin_id   = $params['admin_id'];

        $json = $params['goods_info'] ? $params['goods_info'] : [];
        $data = [];
        foreach ($params['goods_info'] as $goods) {
            $row = [];
            //批量数据
            $row[] = $params['order_id'];
            $row[] = $params['inquiry_sn'];
            $row[] = $goods['goods_id'];
            $row[] = $goods['number'];
            $row[] = $goods['serial'];
            $data[] = $row;
        }

        $orderInquiry->goods_info = json_encode($json, JSON_UNESCAPED_UNICODE);
        if ($orderInquiry->save()) {
            self::insertInquiryGoods($data);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderInquiry->getErrors()]);
        }
    }

    //批量插入
    public static function insertInquiryGoods($data)
    {
        $feild = ['order_id', 'inquiry_sn', 'goods_id', 'number', 'serial'];
        $num = Yii::$app->db->createCommand()->batchInsert(InquiryGoods::tableName(), $feild, $data)->execute();
    }

    //询价单详情
    public function actionView($id)
    {
        $orderInquiry = OrderInquiry::findOne($id);
        if (!$orderInquiry) {
            yii::$app->getSession()->setFlash('error', '没有此询价单');
            return $this->redirect(['index']);
        }

        $orderGoods = OrderGoods::find()->where(['order_id' => $orderInquiry->order_id])->all();
        $goods_ids = ArrayHelper::getColumn($orderGoods, 'goods_id');

        $data = [];
        $data['orderInquiry'] = $orderInquiry;
        $inquiryGoods = InquiryGoods::find()->where([
            'inquiry_sn' => $orderInquiry->inquiry_sn,
            'order_id'   => $orderInquiry->order_id,
            'is_deleted' => InquiryGoods::IS_DELETED_NO])->all();
        $data['inquiryGoods'] = $inquiryGoods;
        $data['orderGoods']   = $orderGoods;

        //总询价数
        $inquiryList = Inquiry::find()->where(['good_id' => $goods_ids])->asArray()->all();
        $inquiryList = ArrayHelper::index($inquiryList, null, 'good_id');

        //我的询价数
        $inquiryMyList = Inquiry::find()->where([
            'good_id'           => $goods_ids,
            'order_inquiry_id'  => $id,
            'admin_id'          => Yii::$app->user->identity->id,
        ])->asArray()->all();
        $inquiryMyList = ArrayHelper::index($inquiryMyList, null, 'good_id');

        $data['inquiryList']    = $inquiryList;
        $data['inquiryMyList']  = $inquiryMyList;

        return $this->render('view', $data);
    }

    //询价确认接口
    public function actionConfirm($id)
    {
        $info = InquiryGoods::findOne($id);
        $info->is_inquiry = InquiryGoods::IS_INQUIRY_YES;
        $info->reason     = '';
        $info->is_result  = InquiryGoods::IS_INQUIRY_NO;
        if ($info->save()) {
            //如果都询价了，本订单和询价单就是已询价
            $res = InquiryGoods::find()->where(['inquiry_sn' => $info->inquiry_sn, 'is_inquiry' => InquiryGoods::IS_INQUIRY_NO])->one();
            if (!$res) {
                //询价单改状态
                $orderInquiry = OrderInquiry::find()->where(['inquiry_sn' => $info->inquiry_sn])->one();
                $orderInquiry->is_inquiry = OrderInquiry::IS_INQUIRY_YES;
                $orderInquiry->save();
                //订单改状态
                $order = Order::findOne($orderInquiry->order_id);
                $order->status = Order::STATUS_YES;
                $order->save();
            }
            return json_encode(['code' => 200, 'msg' => '确认成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $info->getErrors()]);
        }
    }

    //询价记录询不出添加原因
    public function actionAddReason()
    {
        $params = Yii::$app->request->post();
        $inquiryGoods = InquiryGoods::findOne($params['id']);
        $inquiryGoods->reason    = $params['reason'];
        $inquiryGoods->is_result = InquiryGoods::IS_RESULT_YES;
        if ($inquiryGoods->save()) {
            //超级管理员
            $user_super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
            $admin_name = Yii::$app->user->identity->username;
            //给超管通知
            $notice = new SystemNotice();
            $notice->admin_id  = $user_super->user_id;
            $notice->content   = $admin_name . '寻不出零件的价格';
            $notice->notice_at = date('Y-m-d H:i:s');
            $notice->save();
            return json_encode(['code' => 200, 'msg' => '成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $inquiryGoods->getErrors()]);
        }
    }

    //下载询价单详情
    public function actionDownload($id)
    {
        $orderInquiry = OrderInquiry::findOne($id);
        if (!$orderInquiry) {
            yii::$app->getSession()->setFlash('error', '没有此询价单');
            return $this->redirect(['index']);
        }

        $inquiryGoods = InquiryGoods::find()->where([
            'inquiry_sn' => $orderInquiry->inquiry_sn,
            'order_id'   => $orderInquiry->order_id,
            'is_deleted' => InquiryGoods::IS_DELETED_NO])->all();

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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $tableHeader = ['ID', '询价单号', '厂家号', '原厂家', '中文描述', '英文描述', '税率', '含税单价', '询价数量', '含税总价',
            '货期(周)', '供应商', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }
        $tax = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_TAX])->scalar();
        $deliver = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_DELIVERY_TIME])->scalar();
        foreach ($inquiryGoods as $key => $inquiry) {
            for($i = 0; $i < count($letter); $i++) {
                //ID
                $excel->setCellValue($letter[$i] . ($key + 2), $inquiry->id);
                //询价单号
                $excel->setCellValue($letter[$i+1] . ($key + 2), $inquiry->inquiry_sn);
                if ($inquiry->goods) {
                    //厂家号
                    $excel->setCellValue($letter[$i+2] . ($key + 2), $inquiry->goods->goods_number_b);
                    //原厂家
                    $excel->setCellValue($letter[$i+3] . ($key + 2), $inquiry->goods->original_company);
                    //中文描述
                    $excel->setCellValue($letter[$i+4] . ($key + 2), $inquiry->goods->description);
                    //英文描述
                    $excel->setCellValue($letter[$i+5] . ($key + 2), $inquiry->goods->description_en);
                }
                //税率
                $excel->setCellValue($letter[$i+6] . ($key + 2), $tax);
                //含税单价
                //$excel->setCellValue($letter[$i+7] . ($key + 2), '');
                //询价数量
                $excel->setCellValue($letter[$i+8] . ($key + 2), $inquiry->number);
                //含税总价
                //$excel->setCellValue($letter[$i+9] . ($key + 2), '');
                //货期(周)
                //$excel->setCellValue($letter[$i+10] . ($key + 2), $deliver);
                //供应商
                //$excel->setCellValue($letter[$i+11] . ($key + 2), '');
                //备注
                break;
            }
        }

        $title = '询价单' . $orderInquiry->inquiry_sn . date('His') . Yii::$app->user->identity->username;
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
     * 上传导入
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
                    $supplierList = Supplier::find()->select('id, name')
                        ->where(['is_deleted' => Supplier::IS_DELETED_NO])->indexBy('name')->all();
                    foreach ($sheetData as $key => $value) {
                        if ($key == 2) {
                            $orderInquiry = OrderInquiry::findOne(trim($value['B']));
                        }
                        if ($key > 1) {
                            if (empty($value['A']) || empty($value['B'])) {
                                continue;
                            }
                            $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['goods_number_b' => trim($value['B'])])->one();
                            if (isset($supplierList[trim($value['L'])])) {
                                $inquiry = new Inquiry();
                                $inquiry->inquiry_goods_id  = trim($value['A']);
                                $inquiry->order_inquiry_id  = trim($value['B']);
                                $inquiry->tax_rate          = trim($value['G']);
                                $inquiry->price             = trim($value['H']) / ((100 + $inquiry->tax_rate)/100);
                                $inquiry->number            = trim($value['I']);
                                $inquiry->tax_price         = trim($value['H']);
                                $inquiry->good_id           = $goods->id;
                                $inquiry->supplier_id       = $supplierList[trim($value['L'])]->id;
                                $inquiry->all_price         = $inquiry->price * $inquiry->number;
                                $inquiry->all_tax_price     = $inquiry->tax_price * $inquiry->number;
                                $inquiry->inquiry_datetime  = date('Y-m-d H:i:s');
                                $inquiry->remark            = trim($value['M']);
                                $inquiry->delivery_time     = trim($value['K']);
                                $inquiry->admin_id          = Yii::$app->user->identity->id;
                                $inquiry->order_id          = $orderInquiry->order_id;
                                if ($inquiry->save()) {
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
