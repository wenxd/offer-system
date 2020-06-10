<?php

namespace app\controllers;

use app\models\AgreementGoods;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\models\{Goods, Inquiry, InquiryGoods, Order, OrderAgreement, OrderQuote, OrderFinal, QuoteGoods};
use app\models\OrderQuoteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderQuoteController implements the CRUD actions for OrderQuote model.
 */
class OrderQuoteController extends Controller
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
     * Lists all OrderQuote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderQuote model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $orderQuote = $this->findModel($id);
        $quoteGoods = QuoteGoods::find()->where(['order_quote_id' => $orderQuote->id])->orderBy('serial')->all();
        $inquiryGoods = InquiryGoods::find()->where(['order_id' => $orderQuote->order_id])->all();

        return $this->render('view', [
            'model'        => $orderQuote,
            'quoteGoods'   => $quoteGoods,
            'inquiryGoods' => $inquiryGoods
        ]);
    }

    /**
     * Creates a new OrderQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderQuote model.
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
     * Deletes an existing OrderQuote model.
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
     * Finds the OrderQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //生成报价单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $orderFinal = OrderFinal::findOne($params['order_final_id']);

        $orderQuote                 = new OrderQuote();
        $orderQuote->quote_sn       = $params['quote_sn'];
        $orderQuote->order_id       = $orderFinal->order_id;
        $orderQuote->order_final_id = $params['order_final_id'];
        $orderQuote->goods_info     = json_encode($params['goods_info']);
        $orderQuote->admin_id       = $params['admin_id'];
        $orderQuote->quote_ratio    = $params['quote_ratio'];
        $orderQuote->delivery_ratio = $params['delivery_ratio'];
        $orderQuote->customer_id    = $orderFinal->customer_id;
        if ($orderQuote->save()) {

            $orderFinal->is_quote = OrderFinal::IS_QUOTE_YES;
            $orderFinal->save();

            $data = [];
            foreach ($params['goods_info'] as $item) {
                $row = [];

                $row[] = $orderFinal->order_id;
                $row[] = $params['order_final_id'];
                $row[] = $orderFinal->final_sn;
                $row[] = $orderQuote->primaryKey;
                $row[] = $orderQuote->quote_sn;
                $row[] = $item['goods_id'];
                $row[] = $item['type'];
                $row[] = $item['relevance_id'];
                $row[] = $item['number'];
                $row[] = $item['serial'];
                $row[] = $item['tax_rate'];
                $row[] = $item['price'];
                $row[] = $item['tax_price'];
                $row[] = $item['all_price'];
                $row[] = $item['all_tax_price'];
                $row[] = $item['quote_price'];
                $row[] = $item['quote_tax_price'];
                $row[] = $item['quote_all_price'];
                $row[] = $item['quote_all_tax_price'];
                $row[] = $item['delivery_time'];
                $row[] = $item['quote_delivery_time'];

                $data[] = $row;
            }
            self::insertQuoteGoods($data);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderQuote->getErrors()]);
        }
    }

    //批量插入
    public static function insertQuoteGoods($data)
    {
        $feild = ['order_id', 'order_final_id', 'order_final_sn', 'order_quote_id', 'order_quote_sn', 'goods_id',
            'type', 'relevance_id', 'number', 'serial', 'tax_rate', 'price', 'tax_price', 'all_price', 'all_tax_price',
             'quote_price', 'quote_tax_price', 'quote_all_price', 'quote_all_tax_price', 'delivery_time', 'quote_delivery_time'];
        $num = Yii::$app->db->createCommand()->batchInsert(QuoteGoods::tableName(), $feild, $data)->execute();
    }

    //报价单详情
    public function actionDetail($id)
    {
        $orderQuote = OrderQuote::findOne($id);
        $quoteGoods = QuoteGoods::findAll(['order_quote_id' => $id]);

        $date = date('ymd_');
        $orderI = OrderAgreement::find()->where(['like', 'agreement_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->agreement_sn, '_');
            $str = substr($orderI->agreement_sn, $num+1);
            $number = sprintf("%02d", $str+1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['order']      = Order::findOne($orderQuote->order_id);
        $data['orderQuote'] = $orderQuote;
        $data['quoteGoods'] = $quoteGoods;
        $data['model']      = new OrderAgreement();
        $data['number']     = $number;

        return $this->render('detail', $data);
    }

    //完成报价
    public function actionComplete()
    {
        $params = Yii::$app->request->post();

        $quoteGoods = QuoteGoods::findOne($params['id']);
        $quoteGoods->is_quote = QuoteGoods::IS_QUOTE_YES;
        if ($quoteGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $quoteGoods->getErrors()]);
        }
    }

    //创建收入合同订单
    public function actionCreateAgreement()
    {
        $params = Yii::$app->request->post();

        //首先保存报价单
        $orderQuote = OrderQuote::findOne($params['id']);
        $orderQuote->is_quote       = OrderQuote::IS_QUOTE_YES;
        $orderQuote->quote_only_one = OrderQuote::QUOTE_ONLY;
        $orderQuote->save();

        //创建合同单
        $orderAgreement = new OrderAgreement();
        $orderAgreement->agreement_sn    = $params['agreement_sn'];
        $orderAgreement->order_id        = $orderQuote->order_id;
        $orderAgreement->order_quote_id  = $orderQuote->id;
        $orderAgreement->order_quote_sn  = $orderQuote->quote_sn;

        $json = [];

        $orderAgreement->goods_info      = json_encode($json, JSON_UNESCAPED_UNICODE);
        $orderAgreement->agreement_date  = $params['agreement_date'];
        $orderAgreement->sign_date       = $params['sign_date'];
        $orderAgreement->admin_id        = Yii::$app->user->identity->id;
        $orderAgreement->customer_id     = $orderQuote->customer_id;
        if ($orderAgreement->save()) {
            //更新其他的报价单为不可生成合同单
            $orderQuoteList = OrderQuote::find()->where(['order_id' => $orderQuote->order_id])
                ->andWhere(['!=', 'id', $params['id']])->all();
            foreach ($orderQuoteList as $key => $quote) {
                $quote->quote_only_one = 0;
                $quote->save();
            }
            $money = 0;
            foreach ($params['goods_info'] as $item) {
                $quoteGoods = QuoteGoods::findOne($item['quote_goods_id']);
                $agreementGoods = new AgreementGoods();
                $agreementGoods->order_id            = $quoteGoods->order_id;
                $agreementGoods->order_agreement_id  = $orderAgreement->primaryKey;
                $agreementGoods->order_agreement_sn  = $orderAgreement->agreement_sn;
                $agreementGoods->order_quote_id      = $orderQuote->primaryKey;
                $agreementGoods->order_quote_sn      = $orderQuote->quote_sn;
                $agreementGoods->serial              = $quoteGoods->serial;
                $agreementGoods->goods_id            = $quoteGoods->goods_id;
                $agreementGoods->type                = $quoteGoods->type;
                $agreementGoods->relevance_id        = $quoteGoods->relevance_id;
                $agreementGoods->tax_rate            = $quoteGoods->tax_rate;
                $agreementGoods->price               = $quoteGoods->price;
                $agreementGoods->tax_price           = $quoteGoods->tax_price;
                $agreementGoods->all_price           = $quoteGoods->all_price;
                $agreementGoods->all_tax_price       = $quoteGoods->all_tax_price;
                $agreementGoods->delivery_time       = $quoteGoods->quote_delivery_time;
                //用item的值
                $agreementGoods->quote_price         = $item['price'];
                $agreementGoods->quote_tax_price     = $item['tax_price'];
                $agreementGoods->quote_all_price     = $item['price'] * $item['number'];
                $agreementGoods->quote_all_tax_price = $item['tax_price'] * $item['number'];
                $agreementGoods->quote_delivery_time = $item['delivery_time'];
                $agreementGoods->number              = $item['number'];
                $agreementGoods->inquiry_admin_id    = $quoteGoods->type ? 0 : $quoteGoods->inquiry->admin_id;
                $agreementGoods->save();

                $money += $agreementGoods->quote_all_tax_price;
            }

            $orderAgreement->payment_price = $money;
            $orderAgreement->remain_price  = $money;
            $orderAgreement->save();

            //改变生成了收入合同的成本单
            $orderFinal = OrderFinal::findOne($orderQuote->order_final_id);
            $orderFinal->is_agreement = OrderFinal::IS_AGREEMENT_YES;
            $orderFinal->save();

            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderAgreement->getErrors()]);
        }
    }

    /**
     * 导出报价单
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $tableHeader = ['序号', '零件号', '中文描述', '英文描述', '订单数量', '单位', '未税单价', '含税单价', '未税总价', '含税总价', '税率', '货期',
             '库存数量'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1', $tableHeader[$i]);
        }

        $quoteGoods = QuoteGoods::find()->where(['order_quote_id' => $id])->orderBy('serial')->all();
        $name = date('Ymd');
        foreach ($quoteGoods as $key => $value) {
            $excel->setCellValue('A'.($key + 2), $value->serial);
            $excel->setCellValue('B'.($key + 2), $value->goods->goods_number);
            $excel->setCellValue('C'.($key + 2), $value->goods->description);
            $excel->setCellValue('D'.($key + 2), $value->goods->description_en);
            $excel->setCellValue('E'.($key + 2), $value->number);
            $excel->setCellValue('F'.($key + 2), $value->goods->unit);
            $excel->setCellValue('G'.($key + 2), $value->quote_price);
            $excel->setCellValue('H'.($key + 2), $value->quote_tax_price);
            $excel->setCellValue('I'.($key + 2), $value->quote_all_price);
            $excel->setCellValue('J'.($key + 2), $value->quote_all_tax_price);
            $excel->setCellValue('K'.($key + 2), $value->tax_rate);
            $excel->setCellValue('L'.($key + 2), $value->quote_delivery_time);
            $excel->setCellValue('M'.($key + 2), $value->stockNumber->number);
            $name = $value->order_quote_sn;
        }

        $title = $name;
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

    /**发送报价单
     * @param $id
     * @return \yii\web\Response
     */
    public function actionSend($id)
    {
        $orderQuote = OrderQuote::findOne($id);
        $orderQuote->is_quote       = OrderQuote::IS_QUOTE_YES;
        $orderQuote->is_send        = OrderQuote::IS_SEND_YES;
        $orderQuote->quote_at       = date("Y-m-d H:i:s");
        $orderQuote->save();

        return $this->redirect(['index']);
    }
}
