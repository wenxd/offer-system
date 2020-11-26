<?php

namespace app\controllers;

use app\models\AgreementGoodsData;
use app\models\AgreementStock;
use app\models\GoodsRelation;
use app\models\Supplier;
use Yii;
use app\models\Stock;
use app\models\Inquiry;
use app\models\StockLog;
use app\models\PaymentGoods;
use app\models\TempNotGoods;
use app\models\PurchaseGoods;
use app\models\OrderAgreement;
use app\models\AgreementGoods;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use app\models\OrderAgreementStockOutSearch;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockOutController extends BaseController
{
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderAgreementStockOutSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $data = [];

        $orderAgreement = OrderAgreement::findOne($id);
        if (!$orderAgreement) {
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $agreementGoods = AgreementGoodsData::find()->where([
            'order_agreement_id' => $id,
            'purchase_is_show' => AgreementGoods::IS_SHOW_YES,
        ])->all();
        $type = 'AgreementGoodsData';
        if (empty($agreementGoods)) {
            $agreementGoods = AgreementGoods::find()->where([
                'order_agreement_id' => $id,
                'purchase_is_show' => AgreementGoods::IS_SHOW_YES,
            ])->all();
            $type = 'AgreementGoods';
        }
        $stockLog = StockLog::find()->where([
            'order_id' => $orderAgreement->order_id,
            'type' => StockLog::TYPE_OUT,
        ])->all();

        $data['model'] = $orderAgreement;
        $data['agreementGoods'] = $agreementGoods;
        $data['stockLog'] = $stockLog;
        $data['type'] = $type;
        return $this->render('detail', $data);
    }

    /**
     * 出库管理
     * @return false|string
     */
    public function actionOut()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $params = Yii::$app->request->post();

        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);
        $orderAgreement->stock_admin_id = Yii::$app->user->identity->id;
        $orderAgreement->save();
        $agreementGoods = AgreementGoodsData::find()->where(['id' => $params['id']])->one();
        $model = AgreementGoodsData::find();
        if (empty($agreementGoods)) {
            $agreementGoods = AgreementGoods::find()->where(['id' => $params['id']])->one();
            $model = AgreementGoods::find();
        }

        $order_id = $orderAgreement->order_id;

        //采购
        $purchaseGoods = PurchaseGoods::find()->where([
            'order_id' => $order_id,
            'order_agreement_id' => $orderAgreement->id,
            'serial' => $agreementGoods->serial,
            'goods_id' => $agreementGoods->goods_id,
        ])->one();

        //支出
        $paymentGoods = PaymentGoods::find()->where([
            'purchase_goods_id' => ($purchaseGoods ? $purchaseGoods->id : 0),
        ])->one();

        //判断库存是否够
        $stock = Stock::findOne(['good_id' => $agreementGoods['goods_id']]);
        if (!$stock || ($stock && $stock->number < $agreementGoods['order_number'])) {
            return json_encode(['code' => 500, 'msg' => '库存不够了'], JSON_UNESCAPED_UNICODE);
        }

        $stockLog = new StockLog();
        $stockLog->order_id = $orderAgreement['order_id'];

        $stockLog->order_payment_id = $paymentGoods ? $paymentGoods->order_payment_id : 0;
        $stockLog->payment_sn = $paymentGoods ? $paymentGoods->order_payment_sn : '';

        $stockLog->order_agreement_id = $orderAgreement->id;
        $stockLog->agreement_sn = $orderAgreement->agreement_sn;

        $stockLog->order_purchase_id = $purchaseGoods ? $purchaseGoods->order_purchase_id : 0;
        $stockLog->purchase_sn = $purchaseGoods ? $purchaseGoods->order_purchase_sn : '';

        $stockLog->goods_id = $agreementGoods['goods_id'];
        $stockLog->number = $agreementGoods['order_number'];
        $stockLog->type = StockLog::TYPE_OUT;
        $stockLog->operate_time = date('Y-m-d H:i:s');
        $stockLog->admin_id = Yii::$app->user->identity->id;
        if ($stockLog->save()) {
            if (!$stock) {
                $inquiry = Inquiry::findOne($agreementGoods->relevance_id);
                $stock = new Stock();
                $stock->good_id = $agreementGoods->goods_id;
                $stock->supplier_id = $inquiry->supplier_id;
                $stock->price = $agreementGoods->quote_price;
                $stock->tax_price = $agreementGoods->quote_tax_price;
                $stock->tax_rate = $agreementGoods->tax_rate;
                $stock->number = $agreementGoods->order_number;
                $stock->save();
            }
            // 减库存和临时库存
            $res = Stock::updateAllCounters(['number' => -$agreementGoods->order_number], ['good_id' => $agreementGoods->goods_id]);
            if ($res) {
                $agreementGoods->is_out = AgreementGoods::IS_OUT_YES;
                $agreementGoods->save();
                // 如果有使用库存记录则更新成已出库
                $where = ['order_id' => $agreementGoods->order_id, 'goods_id' => $agreementGoods->goods_id];
                AgreementStock::updateAll(['is_stock' => 1], $where);

                // 从新计算临时库存
                Stock::countTempNumber([$stock->good_id]);
                //判断所有收入合同的零件都已近出库
                $isHasAgreementGoods = $model->where([
                    'order_agreement_id' => $params['order_agreement_id'],
                    'is_out' => AgreementGoods::IS_OUT_NO
                ])->one();
                if (!$isHasAgreementGoods) {
                    $orderAgreement->is_stock = OrderAgreement::IS_STOCK_YES;
                    $orderAgreement->stock_at = date('Y-m-d H:i:s');
                    if ($orderAgreement->is_bill && $orderAgreement->is_payment && $orderAgreement->is_advancecharge) {
                        $orderAgreement->is_complete = OrderAgreement::IS_COMPLETE_YES;
                    }
                    $orderAgreement->save();
                    // 查询使用库存中所有未出库零件，更新已出库，并更新
                    $agreementStocks = AgreementStock::find()->where(['order_id' => $agreementGoods->order_id, 'is_stock' => Stock::IS_DELETED_NO])->all();
                    foreach ($agreementStocks as $agreementStock) {
                        $agreementStock->is_stock = AgreementGoods::IS_OUT_YES;
                        if ($agreementStock->save()) {
                            Stock::countTempNumber([$agreementStock->goods_id]);
                        } else {
                            return json_encode(['code' => 500, 'msg' => $agreementStock->getErrors()], JSON_UNESCAPED_UNICODE);
                        }
                    }
                }
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '出库成功']);
            }
        } else {
            return json_encode(['code' => 500, 'msg' => $stockLog->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 批量出库
     */
    public function actionMoreOut()
    {
        $params = Yii::$app->request->post();

        $agreementGoods = AgreementGoods::findAll(['id' => $params['ids']]);

        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);
        $orderAgreement->stock_admin_id = Yii::$app->user->identity->id;
        $orderAgreement->save();
        $order_id = $orderAgreement->order_id;

        foreach ($agreementGoods as $agreementGood) {
            //采购
            $purchaseGoods = PurchaseGoods::find()->where([
                'order_id' => $order_id,
                'order_agreement_id' => $orderAgreement->id,
                'serial' => $agreementGood->serial,
                'goods_id' => $agreementGood->goods_id,
            ])->one();

            //支出
            $paymentGoods = PaymentGoods::find()->where([
                'purchase_goods_id' => ($purchaseGoods ? $purchaseGoods->id : 0),
            ])->one();

            $stock = Stock::findOne(['good_id' => $agreementGood['goods_id']]);
            if (!$stock || ($stock && $stock->number < $agreementGood['order_number'])) {
                return json_encode(['code' => 500, 'msg' => $agreementGood->goods->goods_number . '库存不够了'], JSON_UNESCAPED_UNICODE);
            }

            $stockLog = new StockLog();
            $stockLog->order_id = $orderAgreement['order_id'];

            $stockLog->order_payment_id = $paymentGoods ? $paymentGoods->order_payment_id : 0;
            $stockLog->payment_sn = $paymentGoods ? $paymentGoods->order_payment_sn : '';

            $stockLog->order_agreement_id = $orderAgreement->id;
            $stockLog->agreement_sn = $orderAgreement->agreement_sn;

            $stockLog->order_purchase_id = $purchaseGoods ? $purchaseGoods->order_purchase_id : 0;
            $stockLog->purchase_sn = $purchaseGoods ? $purchaseGoods->order_purchase_sn : '';

            $stockLog->goods_id = $agreementGood['goods_id'];
            $stockLog->number = $agreementGood['order_number'];
            $stockLog->type = StockLog::TYPE_OUT;
            $stockLog->operate_time = date('Y-m-d H:i:s');
            $stockLog->admin_id = Yii::$app->user->identity->id;
            if ($stockLog->save()) {
                if (!$stock) {
                    $inquiry = Inquiry::findOne($agreementGood->relevance_id);
                    $stock = new Stock();
                    $stock->good_id = $agreementGood->goods_id;
                    $stock->supplier_id = $inquiry->supplier_id;
                    $stock->price = $agreementGood->quote_price;
                    $stock->tax_price = $agreementGood->quote_tax_price;
                    $stock->tax_rate = $agreementGood->tax_rate;
                    $stock->number = $agreementGood->order_number;
                    $stock->save();
                }
                // 减库存和临时库存
                $number = $agreementGood->order_number;
                $res = Stock::updateAllCounters(['number' => -$number, 'temp_number' => -$number], ['good_id' => $agreementGood->goods_id]);
                // 判断是不是
                if ($res) {
                    $agreementGood->is_out = AgreementGoods::IS_OUT_YES;
                    $agreementGood->save();
                    // 如果有使用库存记录则更新成已出库
                    AgreementStock::updateAll(['is_stock' => 1], ['order_id' => $agreementGood->order_id, 'goods_id' => $agreementGood->goods_id]);
                }
            }
        }

        //判断所有收入合同的零件都已近出库
        $isHasAgreementGoods = AgreementGoods::find()->where([
            'order_agreement_id' => $params['order_agreement_id'],
            'is_out' => AgreementGoods::IS_OUT_NO,
            'purchase_is_show' => AgreementGoods::IS_SHOW_YES
        ])->one();
        if (!$isHasAgreementGoods) {
            $orderAgreement->is_stock = OrderAgreement::IS_STOCK_YES;
            $orderAgreement->stock_at = date('Y-m-d H:i:s');
            if ($orderAgreement->is_bill && $orderAgreement->is_payment && $orderAgreement->is_advancecharge) {
                $orderAgreement->is_complete = OrderAgreement::IS_COMPLETE_YES;
            }
            $orderAgreement->save();
        }

        return json_encode(['code' => 200, 'msg' => '出库成功']);
    }

    /**
     * 质检
     */
    public function actionQuality($type = 'check')
    {
        $id = Yii::$app->request->post('agreement_goods_id');
        $goods_type = Yii::$app->request->post('goods_type', 'AgreementGoods');
        if ($goods_type == 'AgreementGoods') {
            $agreementGoods = AgreementGoods::findOne($id);
        } else {
            $agreementGoods = AgreementGoodsData::findOne($id);
        }
        // 鉴定
        if ($type == 'check') {
            $agreementGoods->is_quality = AgreementGoods::IS_QUALITY_YES;
        } elseif ($type == 'cert') {
            // 是否有证书
            $agreementGoods->is_cert = $agreementGoods->is_cert ? 0 : 1;
        }
        if ($agreementGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '成功'], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => $agreementGoods->getErrors()], JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * 批量质检
     */
    public function actionMoreQuality()
    {
        $ids = Yii::$app->request->post('ids');
        $num = AgreementGoodsData::updateAll(['is_quality' => AgreementGoods::IS_QUALITY_YES], ['id' => $ids]);
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '质检成功'], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['code' => 500, 'msg' => '失败'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 下载库中没有的零件号
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $tableHeader = ['零件号', '中文描述', '英文描述', '单位', '数量', '库存数量', '库存位置', '到齐', '出库', '质检'];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }
        //获取数据
        $id = $_GET['id'] ?? 0;
        $orderAgreement = OrderAgreement::findOne($id);
        if (!$orderAgreement) {
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }
        $agreementGoods = AgreementGoods::find()->where([
            'order_agreement_id' => $id,
            'purchase_is_show' => AgreementGoods::IS_SHOW_YES,
        ])->all();

        foreach ($agreementGoods as $key => $item) {
            if ($item->goods) {
                $excel->setCellValue('A' . ($key + 2), $item->goods->goods_number . ' ' . $item->goods->material_code);
                $excel->setCellValue('B' . ($key + 2), $item->goods->description);
                $excel->setCellValue('C' . ($key + 2), $item->goods->description_en);
                $excel->setCellValue('D' . ($key + 2), $item->goods->unit);
            } else {
                $excel->setCellValue('A' . ($key + 2), '');
                $excel->setCellValue('B' . ($key + 2), '');
                $excel->setCellValue('C' . ($key + 2), '');
                $excel->setCellValue('D' . ($key + 2), '');
            }
            $excel->setCellValue('E' . ($key + 2), $item->order_number);

            if ($item->stock) {
                $excel->setCellValue('F' . ($key + 2), $item->stock->number);
                $excel->setCellValue('G' . ($key + 2), $item->stock->position);
                $excel->setCellValue('H' . ($key + 2), $item->stock->number > $item->order_number ? '是' : '否');
                $excel->setCellValue('I' . ($key + 2), $item->is_out ? '是' : '否');
                $excel->setCellValue('J' . ($key + 2), $item->is_quality ? '是' : '否');
            } else {
                $excel->setCellValue('F' . ($key + 2), '');
                $excel->setCellValue('G' . ($key + 2), '');
                $excel->setCellValue('H' . ($key + 2), '');
                $excel->setCellValue('I' . ($key + 2), '');
                $excel->setCellValue('J' . ($key + 2), '');
            }
        }

        $title = '出库管理' . $orderAgreement->agreement_sn;
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
     * 子零件组装顶级零件
     */
    public function actionAssemble($id = false)
    {
        //页面部分
        $agreementGoods = AgreementGoodsData::findOne($id);
        // 子零件出库，顶级零件入库并增加减少库存
        if (Yii::$app->request->isPost) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $post = Yii::$app->request->post('info', []);
                // 更新已组装数量
                $agreementGoods->assemble_number = $post['number'];
                if (!$agreementGoods->save()) {
                    return json_encode(['code' => 502, 'msg' => $agreementGoods->getErrors()]);
                }
                // 子零件出库 && 减库存
                $stockLog = new StockLog();
                foreach ($post['son_info'] as $son) {
                    $item = [
                        'order_id' => $post['order_id'],
                        'order_agreement_id' => $post['order_agreement_id'],
                        'agreement_sn' => $post['order_agreement_sn'],
                        'goods_id' =>$son['goods_id'],
                        'number' =>$son['number'],
                        'type' => StockLog::TYPE_OUT,
                        'operate_time' => date('Y-m-d H:i:s'),
                        'admin_id' => Yii::$app->user->identity->id,
                        'position' => $son['position'],
                        'direction' => '总成组装',
                    ];
                    $stockLog->isNewRecord = true;
                    $stockLog->setAttributes($item);
                    if (!$stockLog->save()) {
                        return json_encode(['code' => 501, 'msg' => $stockLog->errors]);
                    }
                    // 更新子零件库存
                    Stock::updateAllCounters(['number' => -$item['number']], ['good_id' => $item['goods_id']]);
                    Stock::countTempNumber([$item['goods_id']]);
                    $stockLog->id = 0;
                }
                // 顶级零件入库 && 加库存
                $item = [
                    'order_id' => $post['order_id'],
                    'order_agreement_id' => $post['order_agreement_id'],
                    'agreement_sn' => $post['order_agreement_sn'],
                    'goods_id' =>$post['goods_id'],
                    'number' =>$post['number'],
                    'type' => StockLog::TYPE_IN,
                    'operate_time' => date('Y-m-d H:i:s'),
                    'admin_id' => Yii::$app->user->identity->id,
                    'position' => $post['goods_position'],
                    'source' => '总成组装',
                    'direction' => '',
                ];
                $stockLog->isNewRecord = true;
                $stockLog->setAttributes($item);
                if (!$stockLog->save()) {
                    return json_encode(['code' => 501, 'msg' => $stockLog->errors]);
                }
                $stockLog->id = 0;
                // 更新顶级零件库存
                $stock = Stock::findOne(['good_id' => $post['goods_id']]);
                if (!$stock) {
                    $stock = new Stock();
                    $stock->good_id = $post['goods_id'];
                    $stock->number = $post['number'];
                    $stock->position = $post['goods_position'];
                } else {
                    $stock->number += $post['number'];
                }
                $stock->save();
                Stock::countTempNumber([$post['goods_id']]);
                // 添加采购记录
//                $supplie = Supplier::find()->where(['name' => '总成'])->asArray()->one();
//                $item = [
//                    'order_id' => $post['order_id'],
//                    'goods_id' =>$post['goods_id'],
//                    'number' =>$post['number'],
//                    'fixed_number' =>$post['number'],
//                    'type' => 1,
//                    'is_quality' => 1,
//                    'operate_time' => date('Y-m-d H:i:s'),
//                    'inquiry_admin_id' => Yii::$app->user->identity->id,
//                    'position' => $son['position'],
//                    'supplier_id' => $supplie['id'] ?? 0,
//                    'is_payment' => PaymentGoods::IS_PAYMENT_YES,
//                ];
//                $PaymentGoods = new PaymentGoods();
//                if ($PaymentGoods->load(['PaymentGoods' => $item]) && $PaymentGoods->save()) {
//                }
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '零件总成成功']);
            } catch (\Exception $e) {
                return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
            }
        }


        // 获取最低级子零件
        $goods_son = GoodsRelation::getGoodsSonNumber(['goods_id' => $agreementGoods->goods_id, 'number' => 1]);
        // 查询本订单临时占用的零件
        $agreement_stock = AgreementStock::find()
            ->select(['goods_id', "SUM(use_number) AS use_number"])
            ->where(['goods_id' => array_column($goods_son, 'goods_id'), 'order_id' => $agreementGoods->order_id,
                'is_stock' => 0, 'is_confirm' => 1])->groupBy('goods_id')->asArray()->all();
        //计算最大组装数据
        $mix_number = 1000000;
        foreach ($goods_son as $key => $item) {
            // 判断是不是有自身占用库存
            $item['occupy'] = '';
            foreach ($agreement_stock ?? [] as $k => $v) {
                if ($item['goods_id'] == $v['goods_id']) {
                    $item['temp_number'] += $v['use_number'];
                    $item['occupy'] = "【自占*{$v['use_number']}】";
                }
            }
            $son_mix_number = intval($item['temp_number'] / $item['number']);
            if ($son_mix_number > 0) {
                if ($son_mix_number < $mix_number) $mix_number = $son_mix_number;
            } else {
                $mix_number = 0;
            }
            $goods_son[$key] = $item;
        }
        return $this->render('assemble', [
            'agreementGoods' => $agreementGoods,
            'mix_number' => $mix_number,
            'goods_son' => $goods_son,
        ]);
    }
}
