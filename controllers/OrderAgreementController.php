<?php

namespace app\controllers;

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
    SystemConfig};
use app\models\OrderAgreementSearch;
use yii\helpers\ArrayHelper;
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

    public function actionDetail($id, $type = 'order')
    {
        $request = Yii::$app->request->get();
        $orderAgreement = OrderAgreement::findOne($id);
        if ($type == 'strategy') {
            //判断是否有原始数据
            $agreementGoods = AgreementGoodsData::find()->alias('ag')
                ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
                ->with('goodsRelation')->with('goods')
                ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
                ->orderBy('serial')->all();
            //如果没有则添加
            if (empty($agreementGoods)) {
                $agreementGoods = AgreementGoods::find()->alias('ag')
                    ->select('ag.*')->leftJoin('goods g', 'ag.goods_id=g.id')
                    ->with('goodsRelation')->with('goods')
                    ->where(['order_agreement_id' => $id, 'ag.is_deleted' => 0, 'ag.purchase_is_show' => 1])
                    ->orderBy('serial')->all();
                //保存到原始数据表
                $AgreementGoodsDataModel = new AgreementGoodsData();
                foreach ($agreementGoods as $item) {
                    $AgreementGoodsDataModel->isNewRecord = true;
                    $AgreementGoodsDataModel->setAttributes($item->toArray());
                    $AgreementGoodsDataModel->save() && $AgreementGoodsDataModel->id = 0;
                }
            }
            //展示原始数据
            if(Yii::$app->request->isPost) {
                try {
                    $post = Yii::$app->request->post('goods_info', []);
                    $transaction = Yii::$app->db->beginTransaction();
                    AgreementGoods::deleteAll(['order_agreement_id' => $id, 'is_deleted' => 0, 'purchase_is_show' => 1]);
                    AgreementGoodsBak::deleteAll(['order_agreement_id' => $id]);
                    $agreementGoodsNews = [];
                    foreach ($agreementGoods as $good) {
                        $item = $good->toArray();
                        unset($item['id']);
                        $item['top_goods_number'] = isset($good->goods->goods_number) ? $good->goods->goods_number : '';
                        //需要拆分
                        if (in_array($item['goods_id'], $post)) {
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
                        $goodsNew['quote_delivery_time'] = $goodsNew['delivery_time'];
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
                        $model->id = 0;
                        $model_bak->isNewRecord = true;
                        $model_bak->setAttributes($item);
                        if (!$model_bak->save()) {
                            return json_encode(['code' => 500, 'msg' => 'GoodsBak数据添加失败']);
                        }
                        $model_bak->id = 0;
                    }
                    $transaction->commit();
                    return json_encode(['code' => 200, 'msg' => '修改策略成功']);
                } catch (\Exception $e) {
                    return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
                }

            }
        } else {
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
        if ($type == 'strategy') {
            return $this->render('strategy', $data);
        }
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
