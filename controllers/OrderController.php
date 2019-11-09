<?php

namespace app\controllers;

use app\models\AgreementStock;
use app\models\FinalGoods;
use app\models\Goods;
use app\models\Inquiry;
use app\models\InquiryGoods;
use app\models\OrderAgreement;
use app\models\OrderFinal;
use app\models\OrderGoods;
use app\models\OrderInquiry;
use app\models\OrderPayment;
use app\models\OrderPurchase;
use app\models\OrderQuote;
use app\models\Stock;
use app\models\Supplier;
use app\models\TempNotGoods;
use app\models\TempNotGoodsB;
use app\models\TempOrderGoods;
use app\models\TempOrderInquiry;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use app\models\OrderFinalQuoteSearch;
use app\models\OrderPurchaseSearch;
use app\models\Cart;
use app\models\QuoteRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BaseController
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $date = date('ymd_');
        $orderI = Order::find()->where(['like', 'order_sn', $date])
            ->andWhere(['order_type' => order::ORDER_TYPE_PROJECT_YES])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->order_sn, '_');
            $str    = substr($orderI->order_sn, $num+1);
            if (is_numeric($str)) {
                $number = sprintf("%02d", $str + 1);
            } else {
                $number = '01';
            }
        } else {
            $number = '01';
        }
        return $this->render('create', [
            'model'  => $model,
            'number' => $number
        ]);
    }

    /**
     * Updates an existing Order model.
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
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSubmit()
    {
        $params = Yii::$app->request->get();
        $ids    = Yii::$app->request->get('ids');

        $order = new Order();
        $order->customer_id  = $params['customer_id'];
        $order->admin_id     = $params['admin_id'];
        $order->order_sn     = $params['order_sn'];
        $order->description  = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->order_price  = $params['order_price'];
        $order->remark       = $params['remark'];
        $order->type         = $params['type'];
        $order->status       = Order::STATUS_NO;
        if ($order->save()) {
            $cartList = Cart::find()->where(['id' => $ids])->all();
            $data = [];
            foreach ($cartList as $key => $cart) {
                $row = [];

                $row[] = $cart->type;
                $row[] = $cart->inquiry_id;
                $row[] = $cart->goods_id;
                $row[] = $cart->quotation_price;
                $row[] = $cart->number;
                $row[] = $order->primaryKey;
                $row[] = $params['type'];
                $row[] = $params['remark'];
                $row[] = $params['provide_date'];

                $data[] = $row;
            }
            $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'remark', 'offer_date'];
            $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
            if ($num) {
                Cart::deleteAll(['id' => $ids]);
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $data = [];

        $order = Order::findOne($id);
        if (!$order){
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }

        $orderGoods     = OrderGoods::find()->where(['order_id' => $id])->all();
        $orderInquiry   = OrderInquiry::findAll(['order_id' => $id]);
        $orderFinal     = OrderFinal::findAll(['order_id' => $id]);
        $orderQuote     = OrderQuote::findAll(['order_id' => $id]);
        $orderPurchase  = OrderPurchase::findAll(['order_id' => $id]);
        $orderAgreement = OrderAgreement::findAll(['order_id' => $id]);
        $orderPayment   = OrderPayment::findAll(['order_id' => $id]);
        $orderUseStock  = AgreementStock::find()->where(['order_id' => $id])->all();

        $data['model']          = $order;
        $data['orderGoods']     = $orderGoods;
        $data['orderInquiry']   = $orderInquiry;
        $data['orderFinal']     = $orderFinal;
        $data['orderQuote']     = $orderQuote;
        $data['orderPurchase']  = $orderPurchase;
        $data['orderAgreement'] = $orderAgreement;
        $data['orderPayment']   = $orderPayment;
        $data['orderUseStock']     = $orderUseStock;

        return $this->render('detail', $data);
    }

    /**生成询价单
     * @param $id
     * @return false|string
     */
    public function actionCreateInquiry($id)
    {
        $request = Yii::$app->request->get();
        $order     = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }
        $goods_ids            = json_decode($order->goods_ids, true);
        $goods                = Goods::find()->where(['id' => $goods_ids])->orderBy('original_company Desc')->all();
        $orderInquiry         = OrderInquiry::find()->where(['order_id' => $order->id])->all();

        //询价记录
        $inquiryListOld = Inquiry::find()->all();
        $inquiryList = ArrayHelper::index($inquiryListOld, null, 'good_id');

        $orderGoodsQuery      = OrderGoods::find()->from('order_goods og')
            ->select('og.*')->leftJoin('goods g', 'og.goods_id=g.id')
            ->where(['order_id' => $order->id]);
        if (isset($request['goods_number']) && $request['goods_number']) {
            $orderGoodsQuery->andWhere(['like', 'goods_number', $request['goods_number']]);
        }
        if (isset($request['goods_number_b']) && $request['goods_number_b']) {
            $orderGoodsQuery->andWhere(['like', 'goods_number_b', $request['goods_number_b']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $orderGoodsQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }
        if (isset($request['is_process']) && $request['is_process'] !== '') {
            $orderGoodsQuery->andWhere(['is_process' => $request['is_process']]);
        }
        if (isset($request['is_special']) && $request['is_special'] !== '') {
            $orderGoodsQuery->andWhere(['is_special' => $request['is_special']]);
        }
        if (isset($request['is_nameplate']) && $request['is_nameplate'] !== '') {
            $orderGoodsQuery->andWhere(['is_nameplate' => $request['is_nameplate']]);
        }
        if (isset($request['is_assembly']) && $request['is_assembly'] !== '') {
            $orderGoodsQuery->andWhere(['is_assembly' => $request['is_assembly']]);
        }
        if (isset($request['is_inquiry']) && $request['is_inquiry'] !== '') {
            $inquiryGoodsIds = ArrayHelper::getColumn($inquiryListOld, 'good_id');
            if ($request['is_inquiry']) {
                $orderGoodsQuery->andWhere(['goods_id' => $inquiryGoodsIds]);
            } else {
                $orderGoodsQuery->andWhere(['not in', 'goods_id', $inquiryGoodsIds]);
            }
        }
        $orderGoods           = $orderGoodsQuery->all();

        //库存数量
        $stockList = Stock::find()->indexBy('good_id')->all();

        $date = date('ymd_');
        $orderI = OrderInquiry::find()->where(['like', 'inquiry_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $inquirySn = explode('_', $orderI->inquiry_sn);
            $number = sprintf("%03d", $inquirySn[1]+1);
        } else {
            $number = '001';
        }
        $supplierList = Supplier::find()->where(['is_deleted' => 0, 'is_confirm' => 1])->all();

        $data                 = [];
        $data['orderInquiry'] = $orderInquiry;
        $data['goods']        = $goods;
        $data['model']        = new OrderInquiry();
        $data['order']        = $order;
        $data['orderGoods']   = $orderGoods;
        $data['number']       = $number;
        $data['inquiryList']  = $inquiryList;
        $data['stockList']    = $stockList;
        $data['supplierList'] = $supplierList;

        return $this->render('create-inquiry', $data);
    }

    //生成最终报价单
    public function actionFinalQuote()
    {
        $ids = Yii::$app->request->post('ids');

        $orderRecord = QuoteRecord::findAll(['id' => $ids]);

        $oldOrder = Order::findOne($orderRecord['0']->order_id);
        $order = new Order();
        $order->customer_id   = $oldOrder->customer_id ;
        $order->order_sn      = $oldOrder->order_sn    ;
        $order->description   = $oldOrder->description ;
        $order->order_price   = $oldOrder->order_price ;
        $order->remark        = $oldOrder->remark      ;
        $order->type          = Order::TYPE_FINAL      ;
        $order->status        = $oldOrder->status      ;
        $order->provide_date  = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type       ;
            $row[] = $value->inquiry_id ;
            $row[] = $value->goods_id   ;
            $row[] = $value->quote_price;
            $row[] = $value->number     ;
            $row[] = $order->id;
            $row[] = Order::TYPE_FINAL;
            $row[] = $value->status     ;
            $row[] = $value->remark     ;
            $row[] = $value->offer_date ;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num   = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '保存失败']);
        }
    }

    //最终询价单列表
    public function actionQuoteList()
    {
        $searchModel = new OrderFinalQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('final-quote-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //最终询价单详情
    public function actionFinalQuoteDetail($id)
    {
        $data = [];

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list']  = $list;

        return $this->render('final-quote-detail', $data);
    }

    //保存为采购单
    public function actionSubmitPurchase()
    {
        $ids      = Yii::$app->request->post('ids');
        $admin_id = Yii::$app->request->post('admin_id');

        $orderRecord = QuoteRecord::findAll(['id' => $ids]);

        $oldOrder = Order::findOne($orderRecord['0']->order_id);
        $order = new Order();
        $order->customer_id   = $oldOrder->customer_id ;
        $order->order_sn      = $oldOrder->order_sn    ;
        $order->admin_id      = $admin_id;
        $order->description   = $oldOrder->description ;
        $order->order_price   = $oldOrder->order_price ;
        $order->remark        = $oldOrder->remark      ;
        $order->type          = Order::TYPE_PURCHASE   ;
        $order->status        = $oldOrder->status      ;
        $order->provide_date  = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type       ;
            $row[] = $value->inquiry_id ;
            $row[] = $value->goods_id   ;
            $row[] = $value->quote_price;
            $row[] = $value->number     ;
            $row[] = $order->id;
            $row[] = Order::TYPE_PURCHASE;
            $row[] = $value->status     ;
            $row[] = $value->remark     ;
            $row[] = $value->offer_date ;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num   = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '保存失败']);
        }
    }

    //最采购单列表
    public function actionPurchaseList()
    {
        $searchModel = new OrderPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('purchase-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //最终询价单详情
    public function actionPurchaseDetail($id)
    {
        $data = [];

        $model = Order::findOne($id);
        if (!$model){
            echo '查不到此报价单信息';die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list']  = $list;

        return $this->render('purchase-detail', $data);
    }

    //创建订单第二步 添加零件页面
    public function actionGenerate()
    {
        $params = Yii::$app->request->get();
        $tempGoods = [];
        if (isset($params['token'])) {
            $tempGoods = TempOrderGoods::findAll(['token' => $params['token']]);
        }
        return $this->render('add-goods', [
            'params'    => $params,
            'tempGoods' => $tempGoods,
        ]);
    }

    //创建订单  添加动作
    public function actionAddGoods()
    {
        $goods_id   = (string)Yii::$app->request->post('goods_id');
        $goods_id_b = (string)Yii::$app->request->post('goods_id_b');
        if ($goods_id) {
            $goods = Goods::find()->where(['goods_number' => $goods_id])->asArray()->one();
        } else {
            $goods = Goods::find()->where(['goods_number_b' => $goods_id_b])->asArray()->one();
        }

        if ($goods) {
            return json_encode(['code' => 200, 'data' => $goods]);
        } else {
            return json_encode(['code' => 500, 'msg' => '没有此零件']);
        }
    }

    //保存订单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->get();
        $goods = Yii::$app->request->post();

        $order               = new Order();
        $order->customer_id  = $params['customer_id'];
        $order->order_sn     = $params['order_sn'];
        $order->manage_name  = $params['manage_name'];
        $order->goods_ids    = json_encode($goods['goodsIds']);
        $order->order_type   = $params['order_type'];
        $order->created_at   = $params['created_at'];

        if ($order->save()) {
            $data = [];
            foreach ($goods['goodsInfo'] as $item) {
                $row = [];
                $row[] = $order->primaryKey;
                $row[] = $item['goods_id'];
                $row[] = $item['number'];
                $row[] = $item['serial'];
                $data[] = $row;
            }
            $feild = ['order_id', 'goods_id', 'number', 'serial'];
            $num = Yii::$app->db->createCommand()->batchInsert(OrderGoods::tableName(), $feild, $data)->execute();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
    }

    public function actionCreateFinal($id, $key = 0)
    {
        $data      = [];
        $order     = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }
        $goods_ids     = json_decode($order->goods_ids, true);
        $goods         = Goods::find()->where(['id' => $goods_ids])->all();
        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->all();

        //先删除库里的垃圾数据
        FinalGoods::deleteAll(['and', ['order_id'  => $id], ['!=', 'key', $key]]);

        //订单零件生成成本零件记录
        foreach ($orderGoods as $value) {
            $isHaveFinalGoods = FinalGoods::find()->where([
                'order_id'  => $id,
                'key'       => $key,
                'goods_id'  => $value->goods_id,
                'serial'    => $value->serial,
            ])->one();
            if (!$isHaveFinalGoods) {
                $inquiry = Inquiry::find()->where([
                    'good_id'           => $value->goods_id,
                    'is_better'         => Inquiry::IS_BETTER_YES,
                    'is_confirm_better' => 1
                ])->one();
                if (!$inquiry) {
                    continue;
                }
                $isHaveFinalGoods = new FinalGoods();
                $isHaveFinalGoods->order_id     = $id;
                $isHaveFinalGoods->goods_id     = $value->goods_id;
                $isHaveFinalGoods->serial       = $value->serial;
                $isHaveFinalGoods->relevance_id = $inquiry->id;
                $isHaveFinalGoods->key          = $key;
                $isHaveFinalGoods->number       = $value->number;
                $isHaveFinalGoods->save();
            }
        }

        $finalGoods    = FinalGoods::find()->where(['order_id' => $id, 'key' => $key])->indexBy('goods_id')->all();

        $date = date('ymd_');
        $orderI = OrderFinal::find()->where(['like', 'final_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $finalSn = explode('_', $orderI->final_sn);
            $number = sprintf("%03d", $finalSn[2]+1);
        } else {
            $number = '001';
        }

        $data['goods']        = $goods;
        $data['order']        = $order;
        $data['finalGoods']   = $finalGoods;
        $data['orderGoods']   = $orderGoods;
        $data['model']        = new OrderFinal();
        $data['number']       = $number;

        return $this->render('create-final', $data);
    }

    /**
     * 非项目订单
     */
    public function actionDirectInquiry()
    {
        $params = Yii::$app->request->get();
        $tempOrder = TempOrderInquiry::findOne($params['temp_id']);
        $goodsIds = explode(',', $tempOrder->goods_ids);
        $goodsList = Goods::findAll(['id' => $goodsIds]);

        return $this->render('add-goods-inquiry', [
            'params'    => $params,
            'goodsList' => $goodsList
        ]);
    }

    /**
     * 保存询价单
     */
    public function actionSaveInquiryOrder()
    {
        $params = Yii::$app->request->post();

        $order               = new Order();
        $order->customer_id  = $params['customer_id'];
        $order->order_sn     = $params['order_sn'];
        $order->manage_name  = $params['manage_name'];
        $order->goods_ids    = json_encode($params['goodsIds']);
        $order->order_type   = Order::ORDER_TYPE_PROJECT_NO;
        $order->created_at   = $params['created_at'];

        if ($order->save()) {
            $data = [];
            foreach ($params['goodsInfo'] as $item) {
                $row = [];
                $row[] = $order->primaryKey;
                $row[] = $item['goods_id'];
                $row[] = $item['number'];
                $row[] = $item['serial'];
                $data[] = $row;
            }
            $feild = ['order_id', 'goods_id', 'number', 'serial'];
            $num = Yii::$app->db->createCommand()->batchInsert(OrderGoods::tableName(), $feild, $data)->execute();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $order->getErrors()]);
        }
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

        $letter = ['A', 'B', 'C'];
        $tableHeader = ['序号', '零件号', '数量'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '订单添加零件上传模板' . date('ymd-His');
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
                    $data = [];
                    $time = time();
                    TempNotGoods::deleteAll();
                    TempNotGoodsB::deleteAll();
                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            if (empty($value['B'])) {
                                continue;
                            }
                            $goods = Goods::findOne(['goods_number' => trim($value['B'])]);
                            if ($goods) {
                                $item = [];
                                $item[] = trim($value['A']);
                                $item[] = $goods->id;
                                $item[] = trim($value['C']);
                                $item[] = $time;
                                $data[] = $item;
                                if (!$goods->goods_number_b) {
                                    $temp_b                 = new TempNotGoodsB();
                                    $temp_b->goods_id       = $goods->id;
                                    $temp_b->goods_number   = $goods->goods_number;
                                    $temp_b->goods_number_b = $goods->goods_number_b;
                                    $temp_b->save();
                                }
                            } else {
                                $temp = new TempNotGoods();
                                $temp->goods_number = trim($value['B']);
                                $temp->save();
                            }
                        }
                    }
                    $num = Yii::$app->db->createCommand()->batchInsert(TempOrderGoods::tableName(), ['serial', 'goods_id', 'number', 'token'], $data)->execute();
                }
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条', 'data' => $time], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
