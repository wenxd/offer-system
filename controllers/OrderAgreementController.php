<?php

namespace app\controllers;

use app\models\AgreementStock;
use Yii;
use app\models\{AgreementGoodsBak,
    AgreementGoodsData,
    GoodsRelation,
    Inquiry,
    Order,
    OrderAgreement,
    OrderPurchase,
    InquiryGoods,
    AgreementGoods,
    PurchaseGoods,
    Stock,
    SystemConfig
};
use app\models\OrderAgreementSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderAgreementController implements the CRUD actions for OrderAgreement model.
 */
class OrderAgreementController extends Controller
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
     * Lists all OrderAgreement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderAgreementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderAgreement model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $agreementGoods = AgreementGoodsData::find()->where(['order_agreement_id' => $id])->orderBy('serial')->all();
        if (empty($agreementGoods)) {
            $agreementGoods = AgreementGoods::find()->where(['order_agreement_id' => $id])->orderBy('serial')->all();
        }

        $date = date('ymd_');
        $orderI = OrderAgreement::find()->where(['like', 'agreement_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->agreement_sn, '_');
            $str = substr($orderI->agreement_sn, $num + 1);
            $number = sprintf("%02d", $str + 1);
        } else {
            $number = '01';
        }

        return $this->render('view', [
            'model' => $model,
            'agreementGoods' => $agreementGoods,
            'number' => $number,
        ]);
    }

    /**
     * Creates a new OrderAgreement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderAgreement();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderAgreement model.
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
     * Deletes an existing OrderAgreement model.
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
     * Finds the OrderAgreement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderAgreement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderAgreement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 保存采购数量并生成使用库存记录
     */
    public function actionSaveStrategyNumber($id)
    {
        $params = Yii::$app->request->post('goods_info', []);
        $goods_info = [];
        foreach ($params as $v) {
            $goods_info[$v['goods_id']] = $v['strategy_number'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        // 查询收入合同单号与零件ID对应表
        $agreementGoods = AgreementGoodsData::find()->alias('ag')
            ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
            ->with('goodsRelation')->with('goods')
            ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
            ->orderBy('serial')->all();
        foreach ($agreementGoods as $goods) {
            // 匹配零件号，更新采购策略采购数量
            if (isset($goods_info[$goods->goods_id])) {
                // 判断使用库存中是否已经存在
                $count = AgreementStock::find()->where([
                    'order_id' => $goods->order_id,
                    'order_agreement_id' => $goods->order_agreement_id,
                    'goods_id' => $goods->goods_id, 'source' => AgreementStock::STRATEGY
                ])->one();
                if ($count) {
                    //如果已经存在并确认则跳过
                    if ($count->is_confirm == AgreementStock::IS_CONFIRM_YES) {
                        continue;
                    }
                    $count->delete();
                }
                $goods->strategy_number = $goods_info[$goods->goods_id];
                $use_number = $goods->number - $goods->strategy_number;
                $goods->strategy_stock_number = $use_number;
                $goods->is_strategy_stock = $use_number > 0 ? 1 : 0;
                if (!$goods->save()) {
                    $transaction->rollBack();
                    return json_encode(['code' => 501, 'msg' => $goods->errors], JSON_UNESCAPED_UNICODE);
                }
                // 判断是否使用库存（策略采购数量 < 订单需求数量）
                if ($goods->strategy_number < $goods->number) {
                    // 加入使用库存列表
                    $stock_model = new AgreementStock();
                    $stock_data = [
                        'order_id' => $goods->order_id,
                        'order_agreement_id' => $goods->order_agreement_id,
                        'order_agreement_sn' => $goods->order_agreement_sn,
                        'goods_id' => $goods->goods_id,
                        'serial' => $goods->serial,
                        'price' => $goods->price,
                        'tax_price' => $goods->tax_price,
                        'use_number' => $use_number,
                        'all_price' => $goods->price * $use_number,
                        'all_tax_price' => $goods->tax_price * $use_number,
                        'source' => AgreementStock::STRATEGY,
                    ];
                    if (!$stock_model->load(['AgreementStock' => $stock_data]) || !$stock_model->save()) {
                        $transaction->rollBack();
                        return json_encode(['code' => 502, 'msg' => $stock_model->errors], JSON_UNESCAPED_UNICODE);
                    }
                }
            }
        }
        // 更新收入合同
        $orderAgreement = OrderAgreement::findOne($id);
        $orderAgreement->is_strategy_number = 1;
        if (!$orderAgreement->save()) {
            $transaction->rollBack();
            return json_encode(['code' => 503, 'msg' => $orderAgreement->errors], JSON_UNESCAPED_UNICODE);
        }
        $transaction->commit();
        return json_encode(['code' => 200, 'msg' => '保存采购数量并生成使用库存记录成功'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 生成采购策略
     */
    public function actionStrategy($id)
    {
        $orderAgreement = OrderAgreement::findOne($id);
        //判断是否有原始数据
        if (!AgreementGoodsData::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0, 'purchase_is_show' => 1])->count()) {
            $agreementGoods = AgreementGoods::find()->alias('ag')
                ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
                ->with('goodsRelation')->with('goods')
                ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
                ->orderBy('serial')->all();
            //保存到原始数据表
            $AgreementGoodsDataModel = new AgreementGoodsData();
            foreach ($agreementGoods as $item) {
                $AgreementGoodsDataModel->isNewRecord = true;
                $item_arr = $item->toArray();
                $item_arr['strategy_number'] = $item_arr['number'];
                $AgreementGoodsDataModel->setAttributes($item_arr);
                $AgreementGoodsDataModel->save() && $AgreementGoodsDataModel->id = 0;
            }
        }
        $agreementGoods = AgreementGoodsData::find()->alias('ag')
            ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
            ->with('goodsRelation')->with('goods')
            ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
            ->andWhere("strategy_number > 0")
            ->orderBy('serial')->all();
        if (Yii::$app->request->isPost) {
            try {
                $post = Yii::$app->request->post('goods_info', []);
                $transaction = Yii::$app->db->beginTransaction();
                AgreementGoods::deleteAll(['order_agreement_id' => $id, 'is_deleted' => 0, 'purchase_is_show' => 1]);
                AgreementGoodsBak::deleteAll(['order_agreement_id' => $id]);
                $agreementGoodsNews = [];
                foreach ($agreementGoods as $good) {
                    $item = $good->toArray();
                    unset($item['id']);
                    $item['number'] = $good['strategy_number'];
                    $item['order_number'] = $item['number'];
                    $item['purchase_number'] = $item['number'];
                    $item['top_goods_number'] = isset($good->goods->goods_number) ? $good->goods->goods_number : '';
                    //需要拆分
                    if (in_array($item['goods_id'], $post)) {
                        //策略采购数量
                        $item['number'] = $good['strategy_number'];
                        $good->belong_to = '';
                        $data = GoodsRelation::getGoodsSonPrice($item, []);
                        foreach ($data as $v) {
                            $agreementGoodsNews[] = $v;
                        }
                    } else {
                        $good->belong_to = '[]';
                        //不需要拆分
                        $agreementGoodsNews[] = $item;
                    }
                    $good->save();
                }
                //重组采购策略
                $goodsNews = [];
                foreach ($agreementGoodsNews as $goodsNew) {
                    $goods_id = $goodsNew['goods_id'];
                    $goodsNew['info'][$goodsNew['top_goods_number']] = $goodsNew['number'];
                    if (isset($goodsNews[$goods_id])) {
                        $goodsNew['info'] = $goodsNews[$goods_id]['info'];
                        $goodsNew['info'][$goodsNew['top_goods_number']] = $goodsNew['number'];
                        $goodsNew['number'] += $goodsNews[$goods_id]['number'];
                        $goodsNew['order_number'] = $goodsNew['number'];
                        $goodsNew['purchase_number'] = $goodsNew['number'];
                    }
                    $info = json_encode($goodsNew['info'], JSON_UNESCAPED_UNICODE);
                    $goodsNew['belong_to'] = $info;
                    $goodsNew['tax_price'] = $goodsNew['price'] * (1 + $goodsNew['tax_rate'] / 100);//'含税单价',
                    $goodsNew['all_price'] = $goodsNew['number'] * $goodsNew['price'];
                    $goodsNew['all_tax_price'] = $goodsNew['number'] * $goodsNew['tax_price'];
                    $goodsNew['delivery_time'] = $goodsNew['delivery_time'];
                    $goodsNew['quote_delivery_time'] = $goodsNew['quote_delivery_time'];
                    $goodsNews[$goods_id] = $goodsNew;
                }
                $model = new AgreementGoods();
                $model_bak = new AgreementGoodsBak();
                foreach ($goodsNews as $item) {
                    $model->isNewRecord = true;
                    $model->setAttributes($item);
                    if (!$model->save()) {
                        return json_encode(['code' => 500, 'msg' => 'Goods数据添加失败']);
                    }
                    $item['agreement_goods_id'] = $model->id;
                    $model->id = 0;
                    $model_bak->isNewRecord = true;
                    $model_bak->setAttributes($item);
                    if (!$model_bak->save()) {
                        return json_encode(['code' => 500, 'msg' => 'GoodsBak数据添加失败']);
                    }
                    $model_bak->id = 0;
                }
                $orderAgreement->is_strategy = 1;
                $orderAgreement->save();
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '修改策略成功']);
            } catch (\Exception $e) {
                return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
            }
        }
        $inquiryGoods = InquiryGoods::find()->where(['order_id' => $orderAgreement->order_id])->indexBy('goods_id')->all();
        $purchaseGoods = PurchaseGoods::find()->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $id])->asArray()->all();
        $purchaseGoods = ArrayHelper::index($purchaseGoods, null, 'goods_id');

        $date = date('ymd_');
        $orderI = OrderPurchase::find()->where(['like', 'purchase_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->purchase_sn, '_');
            $str = substr($orderI->purchase_sn, $num + 1);
            $number = sprintf("%02d", $str + 1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['orderAgreement'] = $orderAgreement;
        $data['agreementGoods'] = $agreementGoods;
        $data['model'] = new OrderAgreement();
        $data['number'] = $number;
        $data['inquiryGoods'] = $inquiryGoods;
        $data['purchaseGoods'] = $purchaseGoods;
        $data['order'] = Order::findOne($orderAgreement->order_id);
        $data['id'] = $id;
        return $this->render('strategy', $data);
    }

    /**
     * 生成采购订单
     * @param $id
     * @param string $type
     * @return false|string
     */
    public function actionDetail($id, $type = 'order')
    {
        if (Yii::$app->request->isPost) {
            try {
                $params = Yii::$app->request->post('goods_info', []);
                $goods_info = [];
                foreach ($params as $v) {
                    $goods_info[$v['goods_id']] = $v['strategy_number'];
                }
                $transaction = Yii::$app->db->beginTransaction();
                // 查询收入合同单号与零件ID对应表
                $agreementGoods = AgreementGoods::find()->alias('ag')
                    ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
                    ->with('goodsRelation')->with('goods')
                    ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
                    ->orderBy('serial')->all();
                foreach ($agreementGoods as $goods) {
                    // 匹配零件号，更新采购策略采购数量
                    if (isset($goods_info[$goods->goods_id])) {
                        // 判断使用库存中是否已经存在
                        $count = AgreementStock::find()->where([
                            'order_id' => $goods->order_id,
                            'order_agreement_id' => $goods->order_agreement_id,
                            'goods_id' => $goods->goods_id, 'source' => AgreementStock::PURCHASE
                        ])->one();
                        if ($count) {
                            //如果已经存在并确认则跳过
                            if ($count->is_confirm == AgreementStock::IS_CONFIRM_YES) {
                                continue;
                            }
                            $count->delete();
                        }
                        $goods->purchase_number = $goods_info[$goods->goods_id];
                        $use_number = $goods->number - $goods->purchase_number;
                        $goods->purchase_stock_number = $use_number;
                        $goods->is_purchase_stock = $use_number > 0 ? 1 : 0;
                        if (!$goods->save()) {
                            $transaction->rollBack();
                            return json_encode(['code' => 501, 'msg' => $goods->errors], JSON_UNESCAPED_UNICODE);
                        }
                        // 判断是否使用库存（策略采购数量 < 订单需求数量）
                        if ($goods->purchase_number < $goods->number) {
                            // 加入使用库存列表
                            $stock_model = new AgreementStock();
                            $stock_data = [
                                'order_id' => $goods->order_id,
                                'order_agreement_id' => $goods->order_agreement_id,
                                'order_agreement_sn' => $goods->order_agreement_sn,
                                'goods_id' => $goods->goods_id,
                                'serial' => $goods->serial,
                                'price' => $goods->price,
                                'tax_price' => $goods->tax_price,
                                'use_number' => $use_number,
                                'all_price' => $goods->price * $use_number,
                                'all_tax_price' => $goods->tax_price * $use_number,
                                'source' => AgreementStock::PURCHASE,
                            ];
                            if (!$stock_model->load(['AgreementStock' => $stock_data]) || !$stock_model->save()) {
                                $transaction->rollBack();
                                return json_encode(['code' => 502, 'msg' => $stock_model->errors], JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }
                // 更新收入合同
                $orderAgreement = OrderAgreement::findOne($id);
                $orderAgreement->is_purchase_number = 1;
                if (!$orderAgreement->save()) {
                    $transaction->rollBack();
                    return json_encode(['code' => 503, 'msg' => $orderAgreement->errors], JSON_UNESCAPED_UNICODE);
                }
                $transaction->commit();
                return json_encode(['code' => 200, 'msg' => '保存采购数量并生成使用库存记录成功'], JSON_UNESCAPED_UNICODE);
            } catch (\Exception $e) {
                return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
            }
        }
        $request = Yii::$app->request->get();
        $orderAgreement = OrderAgreement::findOne($id);
        $agreementGoodsQuery = AgreementGoods::find()->alias('ag')
            ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
            ->with('goodsRelation')
            ->with('goods')
            ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1]);
        //采购单
        if (isset($request['admin_id'])) {
            $agreementGoodsQuery->andFilterWhere(['inquiry_admin_id' => $request['admin_id']]);
        }
        if (isset($request['original_company']) && $request['original_company']) {
            $agreementGoodsQuery->andWhere(['like', 'original_company', $request['original_company']]);
        }
        $agreementGoods = $agreementGoodsQuery->orderBy('serial')->all();
        $inquiryGoods = InquiryGoods::find()->where(['order_id' => $orderAgreement->order_id])->indexBy('goods_id')->all();
        $purchaseGoods = PurchaseGoods::find()->where(['order_id' => $orderAgreement->order_id, 'order_agreement_id' => $id])->asArray()->all();
        $purchaseGoods = ArrayHelper::index($purchaseGoods, null, 'goods_id');

        $date = date('ymd_');
        $orderI = OrderPurchase::find()->where(['like', 'purchase_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->purchase_sn, '_');
            $str = substr($orderI->purchase_sn, $num + 1);
            $number = sprintf("%02d", $str + 1);
        } else {
            $number = '01';
        }

        $data = [];
        $data['orderAgreement'] = $orderAgreement;
        $data['agreementGoods'] = $agreementGoods;
        $data['model'] = new OrderAgreement();
        $data['number'] = $number;
        $data['inquiryGoods'] = $inquiryGoods;
        $data['purchaseGoods'] = $purchaseGoods;
        $data['order'] = Order::findOne($orderAgreement->order_id);
        return $this->render('detail', $data);
    }

    /**
     * 一键走最低
     */
    public function actionLow($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title' => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('price asc')->one();
            if ($inquiry) {
                $agreementGoods->price = $inquiry->price;
                $agreementGoods->tax_price = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id = $inquiry->admin_id;
                $agreementGoods->relevance_id = $inquiry->id;
                $agreementGoods->delivery_time = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键最短
     */
    public function actionShort($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title' => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('delivery_time asc')->one();
            if ($inquiry) {
                $agreementGoods->price = $inquiry->price;
                $agreementGoods->tax_price = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id = $inquiry->admin_id;
                $agreementGoods->relevance_id = $inquiry->id;
                $agreementGoods->delivery_time = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键优选
     */
    public function actionBetter($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title' => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where([
                'good_id' => $agreementGoods->goods_id,
                'is_better' => Inquiry::IS_BETTER_YES,
                'is_confirm_better' => 1
            ])->one();
            if ($inquiry) {
                $agreementGoods->price = $inquiry->price;
                $agreementGoods->tax_price = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id = $inquiry->admin_id;
                $agreementGoods->relevance_id = $inquiry->id;
                $agreementGoods->delivery_time = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键最新
     */
    public function actionNew($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title' => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $agreementGoods->goods_id])->orderBy('created_at Desc')->one();
            if ($inquiry) {
                $agreementGoods->price = $inquiry->price;
                $agreementGoods->tax_price = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $agreementGoods->all_price = $agreementGoods->number * $inquiry->price;
                $agreementGoods->all_tax_price = $agreementGoods->number * $agreementGoods->tax_price;
                $agreementGoods->inquiry_admin_id = $inquiry->admin_id;
                $agreementGoods->relevance_id = $inquiry->id;
                $agreementGoods->delivery_time = $inquiry->delivery_time;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键走库存
     */
    public function actionStock($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where([
            'order_agreement_id' => $id,
            'purchase_is_show' => AgreementGoods::IS_SHOW_YES
        ])->all();
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $stock = Stock::find()->where(['good_id' => $agreementGoods->goods_id])->one();
            if ($stock) {
                $agreementGoods->purchase_number = $agreementGoods->order_number > $stock->number ? $agreementGoods->order_number - $stock->number : 0;
                $agreementGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['detail', 'id' => $id]);
    }

    /**
     * 一键恢复
     */
    public function actionRecover($id)
    {
        $agreementGoodsList = AgreementGoods::find()->where(['order_agreement_id' => $id, 'is_deleted' => 0])->all();

        foreach ($agreementGoodsList as $key => $agreementGoods) {
            $agreementGoodsBak = AgreementGoodsBak::find()->where(['order_agreement_id' => $id, 'agreement_goods_id' => $agreementGoods->id])->one();
            if ($agreementGoodsBak) {
                $agreementGoods->tax_rate = $agreementGoodsBak->tax_rate;
                $agreementGoods->price = $agreementGoodsBak->price;
                $agreementGoods->tax_price = $agreementGoodsBak->tax_price;
                $agreementGoods->all_price = $agreementGoodsBak->all_price;
                $agreementGoods->all_tax_price = $agreementGoodsBak->all_tax_price;
                $agreementGoods->purchase_number = $agreementGoodsBak->purchase_number;
                $agreementGoods->delivery_time = $agreementGoodsBak->delivery_time;
                $agreementGoods->purchase_is_show = AgreementGoods::IS_SHOW_YES;
                $agreementGoods->order_number = $agreementGoods->number;
                $agreementGoods->save();
            }
        }

        $orderAgreement = OrderAgreement::findOne($id);
        $orderAgreement->is_merge = OrderAgreement::IS_MERGE_NO;
        $orderAgreement->is_all_stock = OrderAgreement::IS_ALL_STOCK_NO;
        $orderAgreement->save();

        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }

    /**一键合并
     * @param $id
     * @return \yii\web\Response
     */
    public function actionMerge($id)
    {
        $orderAgreement = OrderAgreement::findOne($id);
        $agreementGoodsList = AgreementGoods::find()->where([
            'order_agreement_id' => $id,
            'is_deleted' => 0,
            'purchase_is_show' => AgreementGoods::IS_SHOW_YES,
        ])->all();
        $goods_ids = [];
        $remainIds = [];
        $repetitionIds = [];
        foreach ($agreementGoodsList as $key => $agreementGoods) {
            if (in_array($agreementGoods->goods_id, $goods_ids)) {
                $repetitionIds[] = $agreementGoods->id;
            } else {
                $remainIds[] = $agreementGoods->id;
                $goods_ids[] = $agreementGoods->goods_id;
            }
        }

        //剩下的数据
        $remainList = AgreementGoods::find()->where(['id' => $remainIds])->indexBy('goods_id')->all();
        //需要合并的数据
        $repetitionList = AgreementGoods::find()->where(['id' => $repetitionIds])->all();
        foreach ($remainList as $remain) {
            foreach ($repetitionList as $key => $record) {
                if ($record->goods_id == $remain->goods_id) {
                    //合并数据
                    $remain->purchase_number += $record->purchase_number;
                    $remain->order_number += $record->order_number;
                    $remain->save();

                    //重复数据不显示
                    $record->purchase_is_show = AgreementGoods::IS_SHOW_NO;
                    $record->save();
                }
            }
        }

        $orderAgreement->is_merge = OrderAgreement::IS_MERGE_YES;
        $orderAgreement->save();

        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }
}
