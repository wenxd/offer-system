<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\AgreementStock;
use app\models\AuthAssignment;
use app\models\OrderAgreement;
use app\models\OrderPayment;
use app\models\PaymentGoods;
use app\models\Stock;
use app\models\Supplier;
use app\models\SystemNotice;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\models\OrderPurchase;
use app\models\OrderPurchaseSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\OrderFinal;
use app\models\PurchaseGoods;

/**
 * OrderPurchaseController implements the CRUD actions for OrderPurchase model.
 */
class OrderPurchaseController extends BaseController
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
     * Lists all OrderPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderPurchase model.
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
     * Creates a new OrderPurchase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderPurchase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderPurchase model.
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
     * Deletes an existing OrderPurchase model.
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
     * Finds the OrderPurchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderPurchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderPurchase::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**生成采购单
     * @return false|string
     */
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $open = false;
        foreach ($params['goods_info'] as $goods) {
            if ($goods['number'] > 0) {
                $open = true;
            }
        }
        $orderAgreement = OrderAgreement::findOne($params['order_agreement_id']);
        if ($open) {
            $orderPurchase                     = new OrderPurchase();
            $orderPurchase->purchase_sn        = $params['purchase_sn'];
            $orderPurchase->agreement_sn       = $orderAgreement->agreement_sn;
            $orderPurchase->order_id           = $orderAgreement->order_id;
            $orderPurchase->order_agreement_id = $params['order_agreement_id'];
            $orderPurchase->goods_info         = json_encode([], JSON_UNESCAPED_UNICODE);
            $orderPurchase->end_date           = $params['agreement_date'];
            $orderPurchase->admin_id           = $params['admin_id'];
            if ($orderPurchase->save()) {
                $agreement_goods_ids = [];
                foreach ($params['goods_info'] as $item) {
                    $agreementGoods = AgreementGoods::findOne($item['agreement_goods_id']);
                    //处理保存使用库存记录
//                    $use_stock_number = $agreementGoods->order_number >= $item['number'] ?  $agreementGoods->order_number - $item['number'] : 0;
//                    if ($use_stock_number) {
//                        $stock = Stock::find()->where(['good_id' => $agreementGoods->goods_id])->one();
//                        $agreementStock = new AgreementStock();
//                        $agreementStock->order_id           = $orderAgreement->order_id;
//                        $agreementStock->order_agreement_id = $orderAgreement->id;
//                        $agreementStock->order_agreement_sn = $orderAgreement->agreement_sn;
//                        $agreementStock->order_purchase_id  = $orderPurchase->id;
//                        $agreementStock->order_purchase_sn  = $orderPurchase->purchase_sn;
//                        $agreementStock->goods_id           = $agreementGoods->goods_id;
//                        $agreementStock->price              = $stock ? $stock->price : 0;
//                        $agreementStock->tax_price          = $stock ? $stock->tax_price : 0;
//                        $agreementStock->use_number         = $use_stock_number;
//                        $agreementStock->all_price          = $agreementStock->price * $use_stock_number;
//                        $agreementStock->all_tax_price      = $agreementStock->tax_price * $use_stock_number;
//                        $agreementStock->save();
//                    }

                    if ($item['number'] > 0) {
                        if ($agreementGoods) {
                            $purchaseGoods = new PurchaseGoods();

                            $purchaseGoods->order_id            = $orderAgreement->order_id;
                            $purchaseGoods->order_agreement_id  = $orderAgreement->id;
                            $purchaseGoods->order_purchase_id   = $orderPurchase->primaryKey;
                            $purchaseGoods->order_purchase_sn   = $orderPurchase->purchase_sn;
                            $purchaseGoods->serial              = $agreementGoods->serial;
                            $purchaseGoods->goods_id            = $agreementGoods->goods_id;
                            $purchaseGoods->type                = $agreementGoods->type;
                            $purchaseGoods->relevance_id        = $agreementGoods->relevance_id;
                            $purchaseGoods->number              = $agreementGoods->order_number;
                            $purchaseGoods->tax_rate            = $agreementGoods->tax_rate;
                            $purchaseGoods->price               = $agreementGoods->price;
                            $purchaseGoods->tax_price           = $agreementGoods->tax_price;
                            $purchaseGoods->all_price           = $agreementGoods->all_price;
                            $purchaseGoods->all_tax_price       = $agreementGoods->all_tax_price;
                            $purchaseGoods->fixed_price         = $agreementGoods->price;
                            $purchaseGoods->fixed_tax_price     = $agreementGoods->tax_price;
                            $purchaseGoods->fixed_number        = $item['number'];
                            $purchaseGoods->inquiry_admin_id    = $agreementGoods->inquiry_admin_id;
                            $purchaseGoods->agreement_sn        = $orderAgreement->agreement_sn;
                            $purchaseGoods->purchase_date       = $params['agreement_date'];
                            $purchaseGoods->delivery_time       = $agreementGoods->delivery_time;
                            $purchaseGoods->save();
                        }
                    } else {
                        $agreement_goods_ids[] = $item['agreement_goods_id'];
                        //处理保存使用库存记录
                        $stock = Stock::find()->where(['good_id' => $agreementGoods->goods_id])->one();
                        $agreementStock = new AgreementStock();
                        $agreementStock->order_id           = $orderAgreement->order_id;
                        $agreementStock->order_agreement_id = $orderAgreement->id;
                        $agreementStock->order_agreement_sn = $orderAgreement->agreement_sn;
                        $agreementStock->order_purchase_id  = $orderPurchase->id;
                        $agreementStock->order_purchase_sn  = $orderPurchase->purchase_sn;
                        $agreementStock->goods_id           = $agreementGoods->goods_id;
                        $agreementStock->price              = $stock ? $stock->price : 0;
                        $agreementStock->tax_price          = $stock ? $stock->tax_price : 0;
                        $agreementStock->use_number         = $agreementGoods->order_number;
                        $agreementStock->all_price          = $agreementStock->price * $agreementGoods->order_number;
                        $agreementStock->all_tax_price      = $agreementStock->tax_price * $agreementGoods->order_number;
                        $agreementStock->save();
                    }
                }
                AgreementGoods::updateAll(['is_deleted' => 1], ['id' => $agreement_goods_ids]);
                //判断是否全部生成采购单
                $agreementGoodsCount = AgreementGoods::find()->where([
                    'order_agreement_id' => $orderAgreement->id,
                    'is_deleted'         => 0,
                    'purchase_is_show'   => AgreementGoods::IS_SHOW_YES
                ])->count();
                $purchaseGoodsCount  = PurchaseGoods::find()->where(['order_agreement_id' => $orderAgreement->id])->count();
                if ($agreementGoodsCount == $purchaseGoodsCount) {
                    $orderAgreement->is_purchase = OrderAgreement::IS_PURCHASE_YES;
                    $orderAgreement->save();
                }
            } else {
                return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
            }
        } else {
            foreach ($params['goods_info'] as $item) {
                $agreementGoods = AgreementGoods::findOne($item['agreement_goods_id']);
                //处理保存使用库存记录
                $use_stock_number = $agreementGoods->order_number;
                $stock = Stock::find()->where(['good_id' => $agreementGoods->goods_id])->one();

                $agreementStock = new AgreementStock();
                $agreementStock->order_id           = $orderAgreement->order_id;
                $agreementStock->order_agreement_id = $orderAgreement->id;
                $agreementStock->order_agreement_sn = $orderAgreement->agreement_sn;
                $agreementStock->goods_id           = $agreementGoods->goods_id;
                $agreementStock->price              = $stock ? $stock->price : 0;
                $agreementStock->tax_price          = $stock ? $stock->tax_price : 0;
                $agreementStock->use_number         = $use_stock_number;
                $agreementStock->all_price          = $agreementStock->price * $use_stock_number;
                $agreementStock->all_tax_price      = $agreementStock->tax_price * $use_stock_number;
                $agreementStock->save();
            }
            $agreement_goods_ids = ArrayHelper::getColumn($params['goods_info'], 'agreement_goods_id');
            AgreementGoods::updateAll(['is_deleted' => 1], ['id' => $agreement_goods_ids]);
        }

        //处理是否全部走库存
        $agreementGoodsList = AgreementGoods::find()->where([
            'order_agreement_id' => $orderAgreement->id,
            'is_deleted'         => 0,
            'purchase_is_show'   => AgreementGoods::IS_SHOW_YES
        ])->all();
        $purchaseNumber = 0;
        foreach ($agreementGoodsList as $value) {
            $purchaseNumber += $value['purchase_number'];
        }
        if ($purchaseNumber == 0) {
            $orderAgreement->is_all_stock = OrderAgreement::IS_ALL_STOCK_YES;
            $orderAgreement->save();
        }

        if (count($agreement_goods_ids)) {
            //采购数量是0，给库管员通知
            $stockAdmin = AuthAssignment::find()->where(['item_name' => '库管员'])->one();
            $systemNotice = new SystemNotice();
            $systemNotice->admin_id  = $stockAdmin->user_id;
            $systemNotice->content   = '采购合同单号' . $orderPurchase->purchase_sn . '需要点货';
            $systemNotice->notice_at = date('Y-m-d H:i:s');
            $systemNotice->save();
        }
        return json_encode(['code' => 200, 'msg' => '保存成功']);
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request->get();

        $orderPurchase = OrderPurchase::findOne($id);

        $purchaseQuery = PurchaseGoods::find()->from('purchase_goods pg')->select('pg.*')
            ->leftJoin('goods g', 'pg.goods_id=g.id')
            ->leftJoin('inquiry i', 'pg.relevance_id=i.id')
            ->where(['pg.order_purchase_id' => $id]);
        if (isset($request['original_company']) && $request['original_company']) {
            $purchaseQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }
        if (isset($request['supplier_id']) && $request['supplier_id']) {
            $purchaseQuery->andWhere(['i.supplier_id' => $request['supplier_id']]);
        }
        if (isset($request['is_stock']) && $request['is_stock'] !== '') {
            $purchaseQuery->andWhere(['pg.is_stock' => $request['is_stock']]);
        }
        $purchaseGoods         = $purchaseQuery->orderBy('serial')->all();

        $data = [];
        $data['orderPurchase'] = $data['model'] = $orderPurchase;
        $data['purchaseGoods'] = $purchaseGoods;

        //支出合同号
        $date = date('ymd_');
        $orderI = OrderPayment::find()->where(['like', 'payment_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $finalSn = explode('_', $orderI->payment_sn);
            $number = sprintf("%03d", $finalSn[2]+1);
        } else {
            $number = '001';
        }
        $data['number'] = $number;
        //供应商列表
        $supplier = Supplier::find()->where(['is_deleted' => Supplier::IS_DELETED_NO])->all();
        $data['supplier'] = $supplier;
        //获取生成了支出合同商品的列表
        $purchaseGoodsIds = ArrayHelper::getColumn($purchaseGoods, 'id');
        $paymentGoods = PaymentGoods::find()->where(['purchase_goods_id' => $purchaseGoodsIds])->all();
        $data['paymentGoods'] = $paymentGoods;

        return $this->render('detail', $data);
    }

    public function actionComplete($id)
    {
        OrderPayment::updateAll(['purchase_status' => OrderPayment::PURCHASE_STATUS_PASS], ['order_purchase_id' => $id]);

        $orderPurchase = OrderPurchase::findOne($id);
        $orderPurchase->is_complete = 2;
        $orderPurchase->save();

        return $this->redirect(['index']);
    }

    public function actionComplete1()
    {
        $params = Yii::$app->request->post();

        $purchaseGoods = PurchaseGoods::findOne($params['id']);
        if (!$purchaseGoods) {
            return json_encode(['code' => 500, 'msg' => '不存在此条数据']);
        }

        $purchaseGoods->is_purchase   = PurchaseGoods::IS_PURCHASE_YES;
        $purchaseGoods->agreement_sn  = $params['this_agreement_sn'];
        $purchaseGoods->purchase_date = $params['this_delivery_date'];
        if ($purchaseGoods->save()){
            $purchaseComplete = PurchaseGoods::find()
                ->where(['order_purchase_id' => $purchaseGoods->order_purchase_id])
                ->andWhere('is_purchase = 0')->one();
            if (!$purchaseComplete) {
                $orderPurchase = OrderPurchase::findOne($purchaseGoods->order_purchase_id);
                $orderPurchase->is_purchase = OrderPurchase::IS_PURCHASE_YES;
                $orderPurchase->save();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $purchaseGoods->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function actionCompleteAll()
    {
        $params = Yii::$app->request->post();
        $orderPurchase = OrderPurchase::findOne($params['id']);
        $orderPurchase->agreement_date = $params['agreement_date'];
        $orderPurchase->agreement_time = date('Y-m-d H:i:s');
        $orderPurchase->is_purchase    = OrderPurchase::IS_PURCHASE_YES;
        if ($orderPurchase->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 导出采购单详情页面
     */
    public function actionDownload($id)
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $tableHeader = ['序号', '原厂家', '厂家号', '中文描述', '采购数量', '单位', '含税单价', '含税总价', '货期(周)', '供应商'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $purchaseGoods = PurchaseGoods::find()->where(['order_purchase_id' => $id])->orderBy('serial')->all();
        foreach ($purchaseGoods as $key => $value) {
            for($i = 0; $i < count($letter); $i++) {
                $excel->setCellValue($letter[$i] . ($key + 2), $value->serial);
                if ($value->goods) {
                    //厂家号
                    $excel->setCellValue($letter[$i+1] . ($key + 2), $value->goods->original_company);
                    $excel->setCellValue($letter[$i+2] . ($key + 2), $value->goods->goods_number_b);
                    $excel->setCellValue($letter[$i+3] . ($key + 2), $value->goods->description);
                    $excel->setCellValue($letter[$i+5] . ($key + 2), $value->goods->unit);
                } else {
                    $excel->setCellValue($letter[$i+1] . ($key + 2), '');
                    $excel->setCellValue($letter[$i+2] . ($key + 2), '');
                    $excel->setCellValue($letter[$i+3] . ($key + 2), '');
                    $excel->setCellValue($letter[$i+5] . ($key + 2), '');
                }
                //采购数量
                $excel->setCellValue($letter[$i+4] . ($key + 2), $value->fixed_number);
                //含税单价
                $excel->setCellValue($letter[$i+6] . ($key + 2), $value->fixed_tax_price);
                //含税总价
                $excel->setCellValue($letter[$i+7] . ($key + 2), $value->fixed_tax_price * $value->fixed_number);
                //货期(周)
                $excel->setCellValue($letter[$i+8] . ($key + 2), $value->delivery_time);
                //供应商
                $excel->setCellValue($letter[$i+9] . ($key + 2), $value->inquiry->supplier->name);
                break;
            }
        }

        $title = '采购单详情' . date('ymd-His');
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
