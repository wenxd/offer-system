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
use app\models\OrderGoodsBak;
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
    public $enableCsrfValidation = false;

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

    //保存询价单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        foreach ($params['goods_info'] as $key => $goods) {
            if (trim($goods['supplier_name'])) {
                $supplier = Supplier::find()->where(['name' => trim($goods['supplier_name'])])->one();
                if (!$supplier) {
                    return json_encode(['code' => 500, 'msg' => '序号' . $goods['serial'] . '供应商不正确']);
                }
                $params['goods_info'][$key]['supplier_id'] = $supplier->id;
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderInquiry = new OrderInquiry();
            $orderInquiry->inquiry_sn = $params['inquiry_sn'];
            $orderInquiry->order_id = $params['order_id'];
            $orderInquiry->end_date = $params['end_date'];
            $orderInquiry->admin_id = $params['admin_id'];

            $json = $params['goods_info'] ? $params['goods_info'] : [];

            $orderInquiry->goods_info = json_encode([], JSON_UNESCAPED_UNICODE);
            if ($orderInquiry->save()) {
                $data = [];
                foreach ($params['goods_info'] as $goods) {
                    $row = [];
                    //批量数据
                    $row[] = $params['order_id'];
                    $row[] = $orderInquiry->id;
                    $row[] = $params['inquiry_sn'];
                    $row[] = $goods['goods_id'];
                    $row[] = $goods['number'];
                    $row[] = $goods['serial'];
                    $row[] = isset($goods['supplier_id']) ? $goods['supplier_id'] : 0;
                    $row[] = $goods['remark'];
                    $data[] = $row;
                }
                self::insertInquiryGoods($data);
                //是否全部派送询价员
                $count = InquiryGoods::find()->select('id')->where(['order_id' => $params['order_id']])->count();
                $orderGoodsCount = OrderGoodsBak::find()->select('id')->where(['order_id' => $params['order_id']])->count();
                if ($count >= $orderGoodsCount) {
                    $order = Order::findOne($params['order_id']);
                    $order->is_dispatch = Order::IS_DISPATCH_YES;
                    $order->save();
                }
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '保存成功']);
            } else {
                return json_encode(['code' => 500, 'msg' => $orderInquiry->getErrors()]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    //批量插入
    public static function insertInquiryGoods($data)
    {
        $feild = ['order_id', 'order_inquiry_id', 'inquiry_sn', 'goods_id', 'number', 'serial', 'supplier_id', 'remark'];
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

        $orderGoods = OrderGoods::find()->where(['order_id' => $orderInquiry->order_id])->orderBy('serial')->all();
        $goods_ids = ArrayHelper::getColumn($orderGoods, 'goods_id');

        $data = [];
        $data['orderInquiry'] = $orderInquiry;
        $inquiryGoods = InquiryGoods::find()->from('inquiry_goods as i')->select('i.*')
            ->where([
            'i.inquiry_sn' => $orderInquiry->inquiry_sn,
            'i.order_id'   => $orderInquiry->order_id,
            'i.is_deleted' => InquiryGoods::IS_DELETED_NO,
            'g.is_deleted' => Goods::IS_DELETED_NO,
            ])->leftJoin('goods as g', 'g.id = i.goods_id')->orderBy('i.serial asc')->all();
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
        $use_admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
        $super_user_id = $use_admin->user_id;

        $info = InquiryGoods::findOne($id);

        //询价单
        $orderInquiry = OrderInquiry::findOne($info->order_inquiry_id);

        $info->is_inquiry = InquiryGoods::IS_INQUIRY_YES;
//        $info->reason     = '';
//        $info->is_result  = InquiryGoods::IS_INQUIRY_NO;
        $info->admin_id   = Yii::$app->user->identity->id;
        $info->inquiry_at = date('Y-m-d H:i:s');
        if ($info->save()) {
            //询价员询不出价的，超管确认询价，给询价员发确认询价的通知
            if ($super_user_id == Yii::$app->user->identity->id && $info->is_result) {
                $stockAdmin = AuthAssignment::find()->where(['item_name' => '询价员', 'user_id' => $orderInquiry->admin_id])->one();
                $systemNotice = new SystemNotice();
                $systemNotice->admin_id  = $stockAdmin->user_id;
                $systemNotice->content   = '询不出的厂家号' . $info->goods->goods_number_b . '管理员已经确认询价';
                $systemNotice->notice_at = date('Y-m-d H:i:s');
                $systemNotice->save();
            }

            //如果都询价了，本订单和询价单就是已询价
            $res = InquiryGoods::find()->where(['inquiry_sn' => $info->inquiry_sn, 'is_inquiry' => InquiryGoods::IS_INQUIRY_NO])->one();
            if (!$res) {

                $orderInquiry->is_inquiry = OrderInquiry::IS_INQUIRY_YES;
                $orderInquiry->final_at   = $info->inquiry_at;
                $orderInquiry->save();
            }
            //判断订单是否都确认询价
            $res = InquiryGoods::find()->where(['order_id' => $info->order_id, 'is_inquiry' => InquiryGoods::IS_INQUIRY_NO])->one();
            if (!$res) {
                //订单改状态
                $order = Order::findOne($info->order_id);
                $order->status = Order::STATUS_YES;
                $order->save();
            }
            return json_encode(['code' => 200, 'msg' => '确认成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $info->getErrors()]);
        }
    }

    /**
     * 全部确认询价
     */
    public function actionConfirmAll()
    {
        $ids = Yii::$app->request->post('ids');

        $use_admin = AuthAssignment::find()->where(['item_name' => ['系统管理员', '订单管理员']])->all();
        $super_user_id = ArrayHelper::getColumn($use_admin, 'user_id');
        $user_id = Yii::$app->user->identity->id;

        InquiryGoods::updateAll([
            'is_inquiry' => InquiryGoods::IS_INQUIRY_YES,
            'inquiry_at' => date('Y-m-d H:i:s'),
        ], ['id' =>$ids]);

        $inquiryNoResult = InquiryGoods::find()->where([
            'id'          => $ids,
            'is_deleted'  => InquiryGoods::IS_DELETED_NO,
            'is_result'   => InquiryGoods::IS_RESULT_YES
        ])->one();

        //询价员询不出价的，超管确认询价，给询价员发确认询价的通知
        if (in_array($user_id, $super_user_id) && $inquiryNoResult) {
            $inquiryAdmin = AuthAssignment::find()->where(['item_name' => '询价员', 'user_id' => $inquiryNoResult->admin_id])->one();
            $systemNotice = new SystemNotice();
            $systemNotice->admin_id  = $inquiryAdmin->user_id;
            $systemNotice->content   = '询不出的厂家号' . $inquiryNoResult->goods->goods_number_b . '管理员已经确认询价';
            $systemNotice->notice_at = date('Y-m-d H:i:s');
            $systemNotice->save();
        }

        //如果都询价了，本订单和询价单就是已询价
        $info = InquiryGoods::findOne($ids[0]);
        $orderInquiry   = OrderInquiry::findOne($info->order_inquiry_id);
        $orderInquiry->is_inquiry = OrderInquiry::IS_INQUIRY_YES;
        $orderInquiry->final_at   = $info->inquiry_at;
        $orderInquiry->save();

        //判断订单是否都确认询价
        //订单改状态
        $orderInquiryNoInquiry = OrderInquiry::find()->where([
            'order_id'   => $info->order_id,
            'is_inquiry' => OrderInquiry::IS_INQUIRY_NO
        ])->one();
        if (!$orderInquiryNoInquiry) {
            $order = Order::findOne($info->order_id);
            $order->status = Order::STATUS_YES;
            $order->save();
        }

        return json_encode(['code' => 200, 'msg' => '确认成功']);
    }

    //询价记录询不出添加原因
    public function actionAddReason()
    {
        $params = Yii::$app->request->post();
        $inquiryGoods = InquiryGoods::findOne($params['id']);
        $inquiryGoods->reason        = $params['reason'];
        $inquiryGoods->is_result     = InquiryGoods::IS_RESULT_YES;
        $inquiryGoods->not_result_at = date('Y-m-d');
        $inquiryGoods->admin_id      = Yii::$app->user->identity->id;
        $inquiryGoods->is_result_tag = 1;
        if ($inquiryGoods->save()) {
            //超级管理员
            $user_super = AuthAssignment::find()->where(['item_name' => '系统管理员'])->one();
            $admin_name = Yii::$app->user->identity->username;
            //给超管通知
            $notice = new SystemNotice();
            $notice->admin_id  = $user_super->user_id;
            $notice->content   = $admin_name . '寻不出零件' . $inquiryGoods->goods->goods_number . '的价格,询价单号' . $inquiryGoods->inquiry_sn;
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

        $inquiryGoods = InquiryGoods::find()->from('inquiry_goods as i')->select('i.*')
            ->where([
                'i.inquiry_sn' => $orderInquiry->inquiry_sn,
                'i.order_id'   => $orderInquiry->order_id,
                'i.is_deleted' => InquiryGoods::IS_DELETED_NO,
                'g.is_deleted' => Goods::IS_DELETED_NO,
            ])->leftJoin('goods as g', 'g.id = i.goods_id')->all();

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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        $tableHeader = ['ID*', '询价单号*', '原厂家', '厂家号*', '技术备注', '中文描述', '英文描述', '询价数量*', '单位', '含税单价*（不带符号）',
            '货期(周)*', '税率*', '供应商准确名称*', '备注', '是否优选', '优选理由', '特殊说明'];
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
                    //原厂家
                    $excel->setCellValue($letter[$i+2] . ($key + 2), $inquiry->goods->original_company);
                    //厂家号
                    $excel->setCellValue($letter[$i+3] . ($key + 2), $inquiry->goods->goods_number_b);
                    //技术备注
                    $excel->setCellValue($letter[$i+4] . ($key + 2), $inquiry->goods->technique_remark);
                    //中文描述
                    $excel->setCellValue($letter[$i+5] . ($key + 2), $inquiry->goods->description);
                    //英文描述
                    $excel->setCellValue($letter[$i+6] . ($key + 2), $inquiry->goods->description_en);
                }
                //含税单价
                //$excel->setCellValue($letter[$i+7] . ($key + 2), '');
                //询价数量
                $excel->setCellValue($letter[$i+7] . ($key + 2), $inquiry->number);
                //单位
                $excel->setCellValue($letter[$i+8] . ($key + 2), $inquiry->goods->unit);
                //货期(周)
                //$excel->setCellValue($letter[$i+10] . ($key + 2), $deliver);
                //供应商
                //$excel->setCellValue($letter[$i+11] . ($key + 2), '');
                //税率
                $excel->setCellValue($letter[$i+11] . ($key + 2), $tax);
                //特殊说明
                $excel->setCellValue($letter[$i+16] . ($key + 2), $inquiry->remark);
                break;
            }
        }

        $title = $orderInquiry->inquiry_sn . Yii::$app->user->identity->username;
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
                        if ($key > 1) {
                            if (!$value['A']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行ID不能为空'], JSON_UNESCAPED_UNICODE);
                            }
                            if (!$value['B']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行询价单号不能为空'], JSON_UNESCAPED_UNICODE);
                            }
                            if (!$value['D']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行厂家号不能为空'], JSON_UNESCAPED_UNICODE);
                            }
                            if (!$value['H']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行询价数量不能为空'], JSON_UNESCAPED_UNICODE);
                            }
//                            if (!$value['J']) {
//                                unlink('./' . $saveName);
//                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行含税单价不能为空'], JSON_UNESCAPED_UNICODE);
//                            }
//                            if (!$value['K']) {
//                                unlink('./' . $saveName);
//                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行货期(周)不能为空'], JSON_UNESCAPED_UNICODE);
//                            }
                            if (!$value['L']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行税率不能为空'], JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }

                    foreach ($sheetData as $key => $value) {
                        if ($key == 2) {
                            $orderInquiry = OrderInquiry::find()->where(['inquiry_sn' => trim($value['B'])])->one();
                        }
                        if ($key > 1 && trim($value['J']) && trim($value['K'])) {
                            $inquiryGoods = InquiryGoods::findOne(trim($value['A']));
                            $goods = Goods::findOne($inquiryGoods->goods_id);
                            if ($goods) {
                                if (isset($supplierList[trim($value['M'])])) {
                                    $is_inquiry = Inquiry::find()->where([
                                        'admin_id'         => Yii::$app->user->identity->id,
                                        'order_inquiry_id' => $orderInquiry->id,
                                        'good_id'          => $goods->id,
                                        'tax_rate'         => trim($value['L']),
                                        'supplier_id'      => $supplierList[trim($value['M'])]->id,
                                    ])->one();
                                    if ($is_inquiry) {
                                        continue;
                                    }
                                    $inquiry                    = new Inquiry();
                                    $inquiry->inquiry_goods_id  = trim($value['A']);
                                    $inquiry->order_inquiry_id  = $orderInquiry->id;
                                    $inquiry->tax_rate          = trim($value['L']);
                                    $inquiry->price             = trim($value['J']) / ((100 + $inquiry->tax_rate) / 100);
                                    $inquiry->number            = trim($value['H']);
                                    $inquiry->tax_price         = trim($value['J']);
                                    $inquiry->good_id           = $goods->id;
                                    $inquiry->supplier_id       = $supplierList[trim($value['M'])]->id;
                                    $inquiry->all_price         = $inquiry->price * $inquiry->number;
                                    $inquiry->all_tax_price     = $inquiry->tax_price * $inquiry->number;
                                    $inquiry->inquiry_datetime  = date('Y-m-d H:i:s');
                                    $inquiry->remark            = trim($value['N']);
                                    $inquiry->delivery_time     = trim($value['K']);
                                    $inquiry->admin_id          = Yii::$app->user->identity->id;
                                    $inquiry->order_id          = $orderInquiry->order_id;
                                    $inquiry->is_upload         = Inquiry::IS_UPLOAD_YES;
                                    if (trim($value['O']) && trim($value['O']) == '是') {
                                        $inquiry->is_better     = Inquiry::IS_BETTER_YES;
                                    }
                                    if (trim($value['P'])) {
                                        $inquiry->better_reason = trim($value['P']);
                                    }

                                    if ($inquiry->save()) {
                                        $num++;
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
}
