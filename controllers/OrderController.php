<?php

namespace app\controllers;

use app\models\AgreementStock;
use app\models\FinalGoods;
use app\models\Goods;
use app\models\GoodsRelation;
use app\models\Inquiry;
use app\models\InquiryGoods;
use app\models\OrderAgreement;
use app\models\OrderFinal;
use app\models\OrderGoods;
use app\models\OrderGoodsBak;
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
            $str = substr($orderI->order_sn, $num + 1);
            if (is_numeric($str)) {
                $number = sprintf("%02d", $str + 1);
            } else {
                $number = '01';
            }
        } else {
            $number = '01';
        }
        return $this->render('create', [
            'model' => $model,
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
        $ids = Yii::$app->request->get('ids');

        $order = new Order();
        $order->customer_id = $params['customer_id'];
        $order->admin_id = $params['admin_id'];
        $order->order_sn = $params['order_sn'];
        $order->description = $params['description'];
        $order->provide_date = $params['provide_date'];
        $order->order_price = $params['order_price'];
        $order->remark = $params['remark'];
        $order->type = $params['type'];
        $order->status = Order::STATUS_NO;
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
        if (!$order) {
            yii::$app->getSession()->setFlash('error', '查不到此订单信息');
            return $this->redirect(yii::$app->request->headers['referer']);
        }

        $orderGoods = OrderGoods::find()->where(['order_id' => $id])->all();
        $orderInquiry = OrderInquiry::findAll(['order_id' => $id]);
        $orderFinal = OrderFinal::findAll(['order_id' => $id]);
        $orderQuote = OrderQuote::findAll(['order_id' => $id]);
        $orderPurchase = OrderPurchase::findAll(['order_id' => $id]);
        $orderAgreement = OrderAgreement::findAll(['order_id' => $id]);
        $orderPayment = OrderPayment::findAll(['order_id' => $id]);
        $orderUseStock = AgreementStock::find()->where(['order_id' => $id])->all();

        $data['model'] = $order;
        $data['orderGoods'] = $orderGoods;
        $data['orderInquiry'] = $orderInquiry;
        $data['orderFinal'] = $orderFinal;
        $data['orderQuote'] = $orderQuote;
        $data['orderPurchase'] = $orderPurchase;
        $data['orderAgreement'] = $orderAgreement;
        $data['orderPayment'] = $orderPayment;
        $data['orderUseStock'] = $orderUseStock;

        return $this->render('detail', $data);
    }

    /**
     * 2020-09-16 俊杰替换(旧的已注释)
     */
    public function actionCreateInquiryNew($id, $level = 1)
    {
        //清除订单零件对应表临时数据
        OrderGoodsBak::deleteAll();

        $orderGoodsOldList = OrderGoods::find()
            ->select(['order_goods.*', 'goods.is_assembly', 'goods_number'])
            ->join('LEFT JOIN', Goods::tableName(), "goods.id=order_goods.goods_id AND goods.is_deleted=0")
            ->where(['order_id' => $id])->asArray()->all();
        $OrderGoodsBak = [];
        foreach ($orderGoodsOldList as $goods) {
            //是否是总成
            if ($level == 1) {
                $goods['info'] = [];
                $OrderGoodsBak[] = $goods;
            } else {
                if ($goods['is_assembly'] == Goods::IS_ASSEMBLY_YES) {
                    $goods['info'] = '';
                    $goods['sum'] = $goods['number'];
                    $goods['id'] = $goods['goods_id'];
                    $goods['top_goods_number'] = $goods['goods_number'];
                    $data = GoodsRelation::getGoodsSon($goods);
                    if ($data) {
                        foreach ($data as $k => $item) {
                            $item['info'] = [$goods['goods_number'] => $item['sum']];
                            if (isset($OrderGoodsBak[$item['id']])) {
                                $OrderGoodsBak[$item['id']]['number'] += $item['sum'];
                                foreach ($item['info'] as $info_k => $info_v) {
                                    if (isset($OrderGoodsBak[$item['id']]['info'][$info_k])) {
                                        $OrderGoodsBak[$item['id']]['info'][$info_k] += $info_v;
                                    } else {
                                        $OrderGoodsBak[$item['id']]['info'][$info_k] = $info_v;
                                    }
                                }
                            } else {
                                $OrderGoodsBak[$item['id']] = [
                                    'order_id' => $goods['order_id'],
                                    'goods_id' => $item['id'],
                                    'number' => $item['sum'],
                                    'goods_number' => $item['goods_number'],
                                    'serial' => $item['id'],
                                    'is_out' => $goods['is_out'],
                                    'out_time' => $goods['out_time'],
                                    'created_at' => $goods['created_at'],
                                    'updated_at' => $goods['updated_at'],
                                    'info' => $item['info'],
                                ];
                            }
                        }
                    }
                } else {
                    $goods['info'] = [$goods['goods_number'] => $goods['number']];
                    $OrderGoodsBak[] = $goods;
                }
            }
        }
        $OrderGoodsBakInfo = [];
        foreach ($OrderGoodsBak as $item) {
            $key = $item['goods_id'];
            if (!isset($OrderGoodsBakInfo[$key])) {
                $OrderGoodsBakInfo[$key] = $item;
                continue;
            }
            $OrderGoodsBakInfo[$key]['number'] += $item['number'];
            $OrderGoodsBakInfo[$key]['info'] = array_merge($OrderGoodsBakInfo[$key]['info'], $item['info']);
        }
        $model = new OrderGoodsBak();
        foreach ($OrderGoodsBakInfo as $item) {
            if (isset($item['info'])) {
                $item['belong_to'] = json_encode($item['info'], JSON_UNESCAPED_UNICODE);
            }
            $model->isNewRecord = true;
            $model->setAttributes($item);
            $model->save() && $model->id = 0;
        }
        return $this->redirect(['order/create-inquiry', 'id' => $id, 'level' => $level]);
    }
//    public function actionCreateInquiryNew($id)
//    {
//        //处理订单零件合并
//        OrderGoodsBak::deleteAll();
//        $orderGoodsOldList = OrderGoods::find()->where(['order_id' => $id])->asArray()->all();
//        $orderGoodsOldList = ArrayHelper::index($orderGoodsOldList, null, 'goods_id');
//
//        $newOrderGoods = [];
//        foreach ($orderGoodsOldList as $key => $orderGoodsList) {
//            $number = 0;
//            foreach ($orderGoodsList as $k => $orderGoods) {
//                if ($k == 0) {
//                    $saveOrderGoods = $orderGoods;
//                }
//                $number += $orderGoods['number'];
//            }
//            $saveOrderGoods['number'] = $number;
//            $newOrderGoods[] = $saveOrderGoods;
//        }
//        $keys = [];
//        $res = Yii::$app->db->createCommand()->batchInsert(OrderGoodsBak::tableName(), $keys, $newOrderGoods)->execute();
//        return $this->redirect(['order/create-inquiry', 'id' => $id]);
//    }

    /**生成询价单
     * @param $id
     * @return false|string
     */
    public function actionCreateInquiry($id, $level = 1)
    {
        // 选择采购员时判断同一个订单是否已经有过同一个人的采购单号
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $inquiry = OrderInquiry::find()->where($post)->asArray()->one();
            if ($inquiry) {
                return json_encode(['code' => 200,  'msg' => '成功', 'data' => ['inquiry_sn' => $inquiry['inquiry_sn']]]);
            }
            return json_encode(['code' => 500,  'msg' => '数据未找到']);
        }
        $request = Yii::$app->request->get();
        $order = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }

//        $goods_ids = json_decode($order->goods_ids, true);
        $orderInquiry = OrderInquiry::find()->where(['order_id' => $order->id])->all();

        //询价记录
//        $inquiryListOld = Inquiry::find()->where(['good_id' => $goods_ids])->all();
//        $inquiryList = ArrayHelper::index($inquiryListOld, null, 'good_id');

        $orderGoodsQuery = OrderGoodsBak::find()->from('order_goods_bak og')
            ->select('og.*')->leftJoin('goods g', 'og.goods_id=g.id')
            ->where(['order_id' => $order->id]);
        $goods_ids = $orderGoodsQuery->all();
        //询价记录
        $inquiryListOld = Inquiry::find()->where(['good_id' => array_column($goods_ids, 'goods_id')])->all();
        $inquiryList = ArrayHelper::index($inquiryListOld, null, 'good_id');
        if (isset($request['goods_number']) && $request['goods_number']) {
            $orderGoodsQuery->andWhere(['like', 'goods_number', $request['goods_number']]);
        }
        if (isset($request['goods_number_b']) && $request['goods_number_b']) {
            $orderGoodsQuery->andWhere(['like', 'goods_number_b', $request['goods_number_b']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $orderGoodsQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }
        if (isset($request['belong_to']) && $request['belong_to']) {
            $orderGoodsQuery->andWhere(['like', 'belong_to', $request['belong_to']]);
        }
        if (isset($request['is_process']) && $request['is_process'] !== '') {
            $orderGoodsQuery->andWhere(['is_process' => $request['is_process']]);
        }
        if (isset($request['is_special']) && $request['is_special'] !== '') {
            $orderGoodsQuery->andWhere(['is_special' => $request['is_special']]);
        }
        if (isset($request['is_import']) && $request['is_import'] !== '') {
            $orderGoodsQuery->andWhere(['is_import' => $request['is_import']]);
        }
        if (isset($request['is_standard']) && $request['is_standard'] !== '') {
            $orderGoodsQuery->andWhere(['is_standard' => $request['is_standard']]);
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
        $orderGoods = $orderGoodsQuery->all();

        //库存数量
        $stockList = Stock::find()->indexBy('good_id')->all();

        $date = date('ymd_');
        $orderI = OrderInquiry::find()->where(['like', 'inquiry_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $inquirySn = explode('_', $orderI->inquiry_sn);
            $number = sprintf("%03d", $inquirySn[1] + 1);
        } else {
            $number = '001';
        }
        $supplierList = Supplier::find()->where(['is_deleted' => 0, 'is_confirm' => 1])->all();

        $data = [];
        $data['level'] = $level;
        $data['orderInquiry'] = $orderInquiry;
        $data['model'] = new OrderInquiry();
        $data['order'] = $order;
        $data['orderGoods'] = $orderGoods;
        $data['number'] = $number;
        $data['inquiryList'] = $inquiryList;
        $data['stockList'] = $stockList;
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
        $order->customer_id = $oldOrder->customer_id;
        $order->order_sn = $oldOrder->order_sn;
        $order->description = $oldOrder->description;
        $order->order_price = $oldOrder->order_price;
        $order->remark = $oldOrder->remark;
        $order->type = Order::TYPE_FINAL;
        $order->status = $oldOrder->status;
        $order->provide_date = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type;
            $row[] = $value->inquiry_id;
            $row[] = $value->goods_id;
            $row[] = $value->quote_price;
            $row[] = $value->number;
            $row[] = $order->id;
            $row[] = Order::TYPE_FINAL;
            $row[] = $value->status;
            $row[] = $value->remark;
            $row[] = $value->offer_date;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
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
        if (!$model) {
            echo '查不到此报价单信息';
            die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list'] = $list;

        return $this->render('final-quote-detail', $data);
    }

    //保存为采购单
    public function actionSubmitPurchase()
    {
        $ids = Yii::$app->request->post('ids');
        $admin_id = Yii::$app->request->post('admin_id');

        $orderRecord = QuoteRecord::findAll(['id' => $ids]);

        $oldOrder = Order::findOne($orderRecord['0']->order_id);
        $order = new Order();
        $order->customer_id = $oldOrder->customer_id;
        $order->order_sn = $oldOrder->order_sn;
        $order->admin_id = $admin_id;
        $order->description = $oldOrder->description;
        $order->order_price = $oldOrder->order_price;
        $order->remark = $oldOrder->remark;
        $order->type = Order::TYPE_PURCHASE;
        $order->status = $oldOrder->status;
        $order->provide_date = $oldOrder->provide_date;
        $order->save();

        $data = [];
        foreach ($orderRecord as $key => $value) {
            $row = [];

            $row[] = $value->type;
            $row[] = $value->inquiry_id;
            $row[] = $value->goods_id;
            $row[] = $value->quote_price;
            $row[] = $value->number;
            $row[] = $order->id;
            $row[] = Order::TYPE_PURCHASE;
            $row[] = $value->status;
            $row[] = $value->remark;
            $row[] = $value->offer_date;

            $data[] = $row;
        }

        $field = ['type', 'inquiry_id', 'goods_id', 'quote_price', 'number', 'order_id', 'order_type', 'status', 'remark', 'offer_date'];
        $num = Yii::$app->db->createCommand()->batchInsert(QuoteRecord::tableName(), $field, $data)->execute();
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
        if (!$model) {
            echo '查不到此报价单信息';
            die;
        }
        Yii::$app->session->set('order_inquiry_id', $id);
        $list = QuoteRecord::findAll(['order_id' => $id]);

        $model->loadDefaultValues();
        $data['model'] = $model;
        $data['list'] = $list;

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
            'params' => $params,
            'tempGoods' => $tempGoods,
        ]);
    }

    //创建订单  添加动作
    public function actionAddGoods()
    {
        $goods_id = (string)Yii::$app->request->post('goods_id');
        $goods = Goods::find()->where(['id' => $goods_id])->asArray()->one();

        if ($goods) {
            return json_encode(['code' => 200, 'data' => $goods]);
        } else {
            return json_encode(['code' => 500, 'msg' => '没有此零件']);
        }
    }

    //创建订单  添加动作
    public function actionAddGoodsNew()
    {
        $data = [];
        $goods_id = (string)Yii::$app->request->post('goods_id');
        $number = (string)Yii::$app->request->get('number', 10);
        $goods = Goods::find()->where(['id' => $goods_id])->asArray()->one();
        if (empty($goods)) {
            return json_encode(['code' => 500, 'msg' => '没有此零件']);
        }
        $goods['info'] = '';
        $goods['sum'] = $number;
        $data = [$goods];
        //是否是总成
        if ($goods['is_assembly'] == Goods::IS_ASSEMBLY_YES) {
            $data = GoodsRelation::getGoodsSon($goods);
        }
        return json_encode(['code' => 200, 'data' => $data]);
    }

    //保存订单
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->get();
        $goods = Yii::$app->request->post();

        $order = new Order();
        $order->customer_id = $params['customer_id'];
        $order->order_sn = $params['order_sn'];
        $order->manage_name = $params['manage_name'];
        $goodsIds = ArrayHelper::getColumn($goods['goodsInfo'], 'goods_id');
        $order->goods_ids = json_encode($goodsIds);
        $order->order_type = $params['order_type'];
        $order->created_at = $params['created_at'];

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

    /**保存成本单
     * @param $id
     * @param int $key
     * @return false|string
     */
    public function actionCreateFinal($id, $key = 0)
    {
        $data = [];
        $order = Order::findOne($id);
        if (!$order) {
            return json_encode(['code' => 500, 'msg' => '此订单不存在']);
        }
        $goods_ids = json_decode($order->goods_ids, true);
        $goods = Goods::find()->where(['id' => $goods_ids])->all();
        $orderGoods = OrderGoods::find()->where(['order_id' => $order->id])->all();

        //先删除库里的垃圾数据
        FinalGoods::deleteAll(['and', ['order_id' => $id], ['!=', 'key', $key]]);

        //订单零件生成成本零件记录
        foreach ($orderGoods as $value) {
            $isHaveFinalGoods = FinalGoods::find()->where([
                'order_id' => $id,
                'key' => $key,
                'goods_id' => $value->goods_id,
                'serial' => $value->serial,
            ])->one();
            if (!$isHaveFinalGoods) {
                $inquiry = Inquiry::find()->where([
                    'good_id' => $value->goods_id,
                    'is_better' => Inquiry::IS_BETTER_YES,
                    'is_confirm_better' => 1
                ])->one();
                if (!$inquiry) {
                    $inquiry = Inquiry::find()->where([
                        'good_id' => $value->goods_id,
                    ])->orderBy('price asc')->one();
                    if (!$inquiry) {
                        continue;
                    }
                }
                $isHaveFinalGoods = new FinalGoods();
                $isHaveFinalGoods->order_id = $id;
                $isHaveFinalGoods->goods_id = $value->goods_id;
                $isHaveFinalGoods->serial = $value->serial;
                $isHaveFinalGoods->relevance_id = $inquiry->id;
                $isHaveFinalGoods->key = $key;
                $isHaveFinalGoods->number = $value->number;
                $isHaveFinalGoods->save();
            }
        }

        $finalGoods = FinalGoods::find()->where(['order_id' => $id, 'key' => $key])->indexBy('goods_id')->all();

        $date = date('ymd_');
        $orderI = OrderFinal::find()->where(['like', 'final_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $finalSn = explode('_', $orderI->final_sn);
            $number = sprintf("%03d", $finalSn[2] + 1);
        } else {
            $number = '001';
        }

        $data['goods'] = $goods;
        $data['order'] = $order;
        $data['finalGoods'] = $finalGoods;
        $data['orderGoods'] = $orderGoods;
        $data['model'] = new OrderFinal();
        $data['number'] = $number;

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
            'params' => $params,
            'goodsList' => $goodsList
        ]);
    }

    /**
     * 保存询价单
     */
    public function actionSaveInquiryOrder()
    {
        $params = Yii::$app->request->post();

        $order = new Order();
        $order->customer_id = $params['customer_id'];
        $order->order_sn = $params['order_sn'];
        $order->manage_name = $params['manage_name'];
        $order->goods_ids = json_encode($params['goodsIds']);
        $order->order_type = Order::ORDER_TYPE_PROJECT_NO;
        $order->created_at = $params['created_at'];

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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C'];
        $tableHeader = ['品牌', '零件号', '数量'];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        $title = '订单添加零件上传模板' . date('ymd-His');
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
                            $goods = Goods::find()->where([
                                'goods_number' => trim($value['B']),
                                'material_code' => trim($value['A']),
                            ])->one();
                            if ($goods) {
                                $item = [];
                                $item[] = $key - 1;
                                $item[] = $goods->id;
                                $item[] = trim($value['C']);
                                $item[] = $time;
                                $data[] = $item;
                                if (!$goods->goods_number_b) {
                                    $temp_b = TempNotGoodsB::findOne(['goods_id' => $goods->id]);
                                    if (!$temp_b) {
                                        $temp_b = new TempNotGoodsB();
                                    }
                                    $temp_b->goods_id = $goods->id;
                                    $temp_b->goods_number = $goods->goods_number;
                                    $temp_b->goods_number_b = $goods->goods_number_b;
                                    $temp_b->save();
                                }
                            } else {
                                $temp = TempNotGoods::findOne([
                                    'brand_name' => trim($value['A']),
                                    'goods_number' => trim($value['B'])
                                ]);
                                if (!$temp) {
                                    $temp = new TempNotGoods();
                                }
                                $temp->brand_name = trim($value['A']);
                                $temp->goods_number = trim($value['B']);
                                $temp->save();
                            }
                        }
                    }
                    $num = Yii::$app->db->createCommand()->batchInsert(TempOrderGoods::tableName(),
                        ['serial', 'goods_id', 'number', 'token'], $data)->execute();
                }
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条', 'data' => $time], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    //获取没有发行价的零件
    public function actionGoods($id)
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

        $letter = ['A', 'B'];
        $tableHeader = ['品牌', '零件号'];
        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }

        //获取数据
        $res = [];
        $orderGoods = OrderGoods::find()->where(['order_id' => $id])->all();
        $i = 0;
        foreach ($orderGoods as $key => $record) {
            if ($record->goods->publish_tax_price == 0 && !in_array($record->goods_id, $res)) {
                $excel->setCellValue('A' . ($i + 2), $record->goods->material_code);
                $excel->setCellValue('B' . ($i + 2), $record->goods->goods_number);
                $res[] = $record->goods_id;
                $i++;
            }
        }

        $title = '没有发行价的零件号列表' . date('ymd-His');
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
}
