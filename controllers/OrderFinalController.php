<?php

namespace app\controllers;

use app\models\AgreementGoods;
use app\models\FinalGoodsData;
use app\models\GoodsRelation;
use app\models\Inquiry;
use app\models\OrderAgreement;
use app\models\OrderQuote;
use app\models\QuoteGoods;
use app\models\SystemConfig;
use Yii;
use app\models\OrderFinal;
use app\models\OrderFinalSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\FinalGoods;
use app\models\InquiryGoods;
use app\models\Order;
use app\models\OrderGoods;
use app\models\OrderFinalQuoteSearch;
use app\models\OrderPurchase;
use app\models\PurchaseGoods;

/**
 * OrderFinalController implements the CRUD actions for OrderFinal model.
 */
class OrderFinalController extends BaseController
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
     * Lists all OrderFinal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderFinalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderFinal model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $key = 0, $download = false)
    {
        $orderFinal = $this->findModel($id);
        $where = ['order_id'       => $orderFinal->order_id, 'order_final_id' => $orderFinal->id];
        FinalGoods::updateAll(['key' => $key], $where);
        $finalGoods = FinalGoods::find()->where($where)->orderBy('serial')->all();
        if ($download) {
            $final_sn = $finalGoods[0]->final_sn ?? false;
            $fileName = "成本单-{$final_sn}.csv";
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            $fp = fopen('php://output', 'a');//打开output流
            $rowData = [
                '序号', '零件号', '厂家号', '中文描述', '英文描述', '原厂家', '原厂家备注', '单位',
                '数量', '供应商', '询价员', '税率', '发行含税单价', '发行含税总价', '未税单价',
                '含税单价', '未税总价', '含税总价', '货期', '更新时间', '创建时间', '关联询价记录', '询价ID'];
            mb_convert_variables('GBK', 'UTF-8', $rowData);
            fputcsv($fp, $rowData);
            foreach ($finalGoods as $item) {
                $rowData = [];
                $rowData[] = $item->serial;
                $rowData[] = $item->goods->goods_number . ' ' . $item->goods->material_code;
                $rowData[] = $item->goods->goods_number_b;
                $rowData[] = $item->goods->description;
                $rowData[] = $item->goods->description_en;
                $rowData[] = $item->goods->original_company;
                $rowData[] = $item->goods->original_company_remark;
                $rowData[] = $item->goods->unit;
                $rowData[] = $item->number;
                $rowData[] = $item->inquiry->supplier->name;
                $rowData[] = $item->inquiry->admin->username;
                $rowData[] = $item->tax;
                $publish_tax_price = number_format($item->goods->publish_price * (1 + $item->tax/100), 2, '.', '');
                $rowData[] = $publish_tax_price;
                $rowData[] = $publish_tax_price * $item->number;
                $rowData[] = $item->price;
                $rowData[] = $item->tax_price;
                $rowData[] = $item->number * $item->price;
                $rowData[] = $item->number * $item->tax_price;
                $rowData[] = $item->delivery_time;
                $rowData[] = substr($item->goods->updated_at, 0 , 10);
                $rowData[] = substr($item->goods->created_at, 0 , 10);
                $rowData[] = $item->inquiry ? '是' : '否';
                $rowData[] = $item->relevance_id;
                mb_convert_variables('GBK', 'UTF-8', $rowData);
                fputcsv($fp, $rowData);
            }
            unset($rowData);//释放变量的内存
            ob_flush();
            flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            fclose($fp);
            die;
        }

        return $this->render('view2', [
            'model'      => $orderFinal,
            'finalGoods' => $finalGoods
        ]);
    }

    /**
     * Creates a new OrderFinal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderFinal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderFinal model.
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
     * Deletes an existing OrderFinal model.
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
     * Finds the OrderFinal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderFinal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderFinal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //生成关联数据
    public function actionRelevance()
    {
        $params = Yii::$app->request->post();

        $orderGoods = OrderGoods::find()->where([
            'order_id'     => $params['order_id'],
            'goods_id'     => $params['goods_id'],
            'serial'       => $params['serial'],
        ])->one();

        $finalGoods = FinalGoods::find()->where([
            'order_id'     => $params['order_id'],
            'goods_id'     => $params['goods_id'],
            'key'          => $params['key'],
            'serial'       => $params['serial'],
        ])->one();

        if (!$finalGoods) {
            $finalGoods = new FinalGoods();
            $finalGoods->order_id     = $params['order_id'];
            $finalGoods->goods_id     = $params['goods_id'];
            $finalGoods->key          = $params['key'];
            $finalGoods->serial       = $params['serial'];
            $finalGoods->number       = $orderGoods ? $orderGoods->number : 0;
        }
        //更新最新为准
        $finalGoods->relevance_id = $params['select_id'];

        if ($finalGoods->save()) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $finalGoods->getErrors()]);
        }
    }

    /**保存成本单
     * @return false|string
     */
    public function actionSaveOrder()
    {
        $params = Yii::$app->request->post();

        $order = Order::findOne($params['order_id']);

        $orderFinal                 = new OrderFinal();
        $orderFinal->final_sn       = $params['final_sn'];
        $orderFinal->order_id       = $params['order_id'];
        if ($order) {
            $orderFinal->customer_id = $order->customer_id;
        }
        $orderFinal->goods_info     = json_encode($params['goods_ids']);
        $orderFinal->admin_id       = Yii::$app->user->identity->id;
        if ($orderFinal->save()) {
            foreach ($params['goods_info'] as $key => $value) {
                $finalGoods = FinalGoods::find()->where([
                    'order_id'  => $params['order_id'],
                    'key'       => $params['key'],
                    'serial'    => $value['serial'],
                    'goods_id'  => $value['goods_id'],
                ])->one();
                if ($finalGoods) {
                    $finalGoods->order_final_id = $orderFinal->id;
                    $finalGoods->final_sn       = $orderFinal->final_sn;
                    $finalGoods->tax            = $value['tax'];
                    $finalGoods->price          = $value['price'];
                    $finalGoods->tax_price      = $value['tax_price'];
                    $finalGoods->all_price      = $value['all_price'];
                    $finalGoods->all_tax_price  = $value['all_tax_price'];
                    $finalGoods->delivery_time  = $value['delivery_time'];
                    $finalGoods->save();
                }
            }
            $order->is_final = Order::IS_FINAL_YES;
            $order->save();
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderFinal->getErrors()]);
        }
    }

    public function actionDetail($id)
    {
        $orderFinal    = OrderFinal::findOne($id);
        $order         = Order::findOne($orderFinal->order_id);
        $finalGoods    = FinalGoods::find()->where(['order_final_id' => $id])->orderBy('serial asc')->all();
        $inquiryGoods  = InquiryGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        $quoteGoods    = QuoteGoods::find()->where(['order_id' => $order->id, 'order_final_id' => $id])->indexBy('goods_id')->all();
        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();

        $date = date('ymd_');
        $orderI = OrderQuote::find()->where(['like', 'quote_sn', $date])->orderBy('created_at Desc')->one();
        if ($orderI) {
            $num = strrpos($orderI->quote_sn, '_');
            $str = substr($orderI->quote_sn, $num+1);
            $number = sprintf("%02d", $str+1);
        } else {
            $number = '01';
        }

        $purchaseGoods  = PurchaseGoods::find()
            ->where(['order_id' => $orderFinal->order_id])
            ->indexBy('goods_id')
            ->all();

        $data = [];
        $data['order']          = $order;
        $data['orderGoods']     = $orderGoods;
        $data['orderFinal']     = $orderFinal;
        $data['finalGoods']     = $finalGoods;
        $data['inquiryGoods']   = $inquiryGoods;
        $data['quoteGoods']     = $quoteGoods;
        $data['model']          = new OrderQuote();
        $data['number']         = $number;
        $data['purchaseGoods']  = $purchaseGoods;

        return $this->render('detail', $data);
    }

    /**成本单直接生成采购单的页面
     * @param $id
     * @return string
     */
    public function actionCreatePurchase($id, $type = 'order')
    {
        $request = Yii::$app->request->get();
        $orderFinal      = OrderFinal::findOne($id);
        $order           = Order::findOne($orderFinal->order_id);
        if ($type == 'strategy') {
            //判断是否有原始数据
            $finalGoodsQuery = FinalGoodsData::find()->alias('fg')
                ->select('fg.*')->leftJoin('goods g', 'fg.goods_id=g.id')
                ->with('goodsRelation')->with('goods')
                ->leftJoin('inquiry i', 'fg.relevance_id=i.id')
                ->where(['order_final_id' => $id, 'purchase_is_show' => FinalGoods::IS_SHOW_YES])->all();
            //如果没有则添加
            if (empty($finalGoodsQuery)) {
                $finalGoodsQuery = FinalGoods::find()
                    ->from('final_goods fg')
                    ->select('fg.*')->leftJoin('goods g', 'fg.goods_id=g.id')
                    ->leftJoin('inquiry i', 'fg.relevance_id=i.id')
                    ->where(['order_final_id' => $id, 'purchase_is_show' => FinalGoods::IS_SHOW_YES])->all();
                //保存到原始数据表
                $FinalGoodsData = new FinalGoodsData();
                foreach ($finalGoodsQuery as $item) {
                    $FinalGoodsData->isNewRecord = true;
                    $FinalGoodsData->setAttributes($item->toArray());
                    $FinalGoodsData->save() && $FinalGoodsData->id = 0;
                }
            }
            //展示原始数据
            if(Yii::$app->request->isPost) {
                try {
                    $post = Yii::$app->request->post('goods_info', []);
                    $transaction = Yii::$app->db->beginTransaction();
                    FinalGoods::deleteAll(['order_final_id' => $id, 'is_deleted' => 0, 'purchase_is_show' => 1]);
                    $agreementGoodsNews = [];
                    foreach ($finalGoodsQuery as $good) {
                        $item = $good->toArray();
                        unset($item['id']);
                        $item['top_goods_number'] = isset($good->goods->goods_number) ? $good->goods->goods_number : '';
                        //需要拆分
                        if (in_array($item['goods_id'], $post)) {
                            $good->belong_to = '';
                            $data = GoodsRelation::getGoodsSonPriceFinal($item, []);
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
                        if (!isset($goodsNew['tax']) && isset($goodsNew['tax_rate'])) {
                            $goodsNew['tax'] = $goodsNew['tax_rate'] ? $goodsNew['tax_rate'] : 13;
                        }
                        if (!isset($goodsNew['tax_rate'])) {
                            $goodsNew['tax_rate'] = $goodsNew['tax'] ? $goodsNew['tax'] : 13;
                        }
                        $goodsNew['tax_price'] = $goodsNew['price'] * (1 + $goodsNew['tax_rate'] / 100);//'含税单价',
                        $goodsNew['all_price'] = $goodsNew['number'] * $goodsNew['price'];
                        $goodsNew['all_tax_price'] = $goodsNew['number'] * $goodsNew['tax_price'];
                        $goodsNews[$goods_id] = $goodsNew;
                    }
                    $model = new FinalGoods();
                    foreach ($goodsNews as $item) {
                        $model->isNewRecord = true;
                        $model->setAttributes($item);
                        if (!$model->save()) {
                            return json_encode(['code' => 500, 'msg' => 'Goods数据添加失败']);
                        }
                        $model->id = 0;
                    }
                    $transaction->commit();
                    return json_encode(['code' => 200, 'msg' => '修改策略成功']);
                } catch (\Exception $e) {
                    return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
                }
            }
        } else {
            $finalGoodsQuery = FinalGoods::find()
                ->from('final_goods fg')
                ->select('fg.*')->leftJoin('goods g', 'fg.goods_id=g.id')
                ->leftJoin('inquiry i', 'fg.relevance_id=i.id')
                ->where(['order_final_id' => $id, 'purchase_is_show' => FinalGoods::IS_SHOW_YES]);
            if (isset($request['admin_id'])) {
                $finalGoodsQuery->andFilterWhere(['i.admin_id' => $request['admin_id']]);
            }
            if (isset($request['original_company']) && $request['original_company']) {
                $finalGoodsQuery->andWhere(['like', 'g.original_company', $request['original_company']]);
            }
            $finalGoodsQuery = $finalGoodsQuery->all();
        }
        $inquiryGoods  = InquiryGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();
        $purchaseGoods  = PurchaseGoods::find()
            ->where(['order_id' => $orderFinal->order_id])
            ->indexBy('goods_id')
            ->all();

//        $orderGoods    = OrderGoods::find()->where(['order_id' => $order->id])->indexBy('goods_id')->all();

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
        $data['orderFinal']     = $orderFinal;
        $data['finalGoods']     = $finalGoodsQuery;
        $data['model']          = new OrderPurchase();
        $data['number']         = $number;
        $data['inquiryGoods']   = $inquiryGoods;
        $data['purchaseGoods']  = $purchaseGoods;
        $data['order']          =   $order;
        if ($type == 'strategy') {
            return $this->render('strategy', $data);
        }
        return $this->render('create-purchase', $data);
    }

    /**保存为采购单的动作(非项目订单直接生成采购单)
     * @return false|string
     */
    public function actionSavePurchase()
    {
        $params = Yii::$app->request->post();
        $orderFinal = OrderFinal::findOne($params['order_final_id']);
//        $orderFinal->is_purchase = OrderFinal::IS_PURCHASE_YES;
        $orderFinal->save();
        $orderPurchase = OrderPurchase::findOne(['purchase_sn' => $params['purchase_sn']]);
        if (!$orderPurchase) {
            $orderPurchase                     = new OrderPurchase();
            $orderPurchase->purchase_sn        = $params['purchase_sn'];
            $orderPurchase->order_id           = $orderFinal->order_id;
            $orderPurchase->order_agreement_id = 0;
            $orderPurchase->goods_info         = json_encode([], JSON_UNESCAPED_UNICODE);
            $orderPurchase->end_date           = $params['end_date'];
            $orderPurchase->admin_id           = $params['admin_id'];
        }
        $orderPurchase->is_agreement = 0;
        $orderPurchase->is_complete = 0;
        if ($orderPurchase->save()) {
            foreach ($params['goods_info'] as $item) {
                $finalGoods = FinalGoods::findOne($item['final_goods_id']);
                if ($finalGoods) {
                    $purchaseGoods = new PurchaseGoods();

                    $purchaseGoods->order_id            = $orderFinal->order_id;
                    $purchaseGoods->order_final_id      = $orderFinal->id;
                    $purchaseGoods->order_agreement_id  = 0;
                    $purchaseGoods->order_purchase_id   = $orderPurchase->primaryKey;
                    $purchaseGoods->order_purchase_sn   = $orderPurchase->purchase_sn;
                    $purchaseGoods->serial              = $finalGoods->serial;
                    $purchaseGoods->goods_id            = $finalGoods->goods_id;
                    $purchaseGoods->type                = $finalGoods->type;
                    $purchaseGoods->relevance_id        = $finalGoods->relevance_id;
                    $purchaseGoods->number              = $finalGoods->number;
                    $purchaseGoods->tax_rate            = $finalGoods->tax;
                    $purchaseGoods->price               = $finalGoods->price;
                    $purchaseGoods->tax_price           = $finalGoods->tax_price;
                    $purchaseGoods->all_price           = $item['number'] * $finalGoods->price;
                    $purchaseGoods->all_tax_price       = $item['number'] * $finalGoods->tax_price;
                    $purchaseGoods->fixed_price         = $finalGoods->price;
                    $purchaseGoods->fixed_tax_price     = $finalGoods->tax_price;
                    $purchaseGoods->fixed_number        = $item['number'];
                    $purchaseGoods->inquiry_admin_id    = $params['admin_id'];
                    $purchaseGoods->delivery_time       = $item['delivery_time'];

                    //$purchaseGoods->agreement_sn        = $orderAgreement->order_id;
                    $purchaseGoods->save();
                }
            }
            //判断是否全部生成采购单
            $finalGoodsCount = FinalGoods::find()->where(['order_final_id' => $params['order_final_id'], 'purchase_is_show' => FinalGoods::IS_SHOW_YES])->count();
            $purchaseGoodsCount = PurchaseGoods::find()->where(['order_final_id' => $params['order_final_id']])->count();
            if ($finalGoodsCount == $purchaseGoodsCount) {
                $orderFinal->is_purchase = OrderFinal::IS_PURCHASE_YES;
                $orderFinal->save();
            }
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => $orderPurchase->getErrors()]);
        }
    }

    /**
     * 关联成本订单
     * @return false|string
     */
    public function actionRelevancePurchase()
    {
        $params = Yii::$app->request->post();

        $inquiry = Inquiry::findOne($params['inquiry_id']);
        $finalGoods = FinalGoods::findOne($params['final_goods_id']);

        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();

        $finalGoods->price              = $inquiry->price;
        $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax/100), 2, '.', '');
        $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
        $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
        $finalGoods->delivery_time      = $inquiry->delivery_time;
        $finalGoods->relevance_id       = $inquiry->id;

        if ($finalGoods->save()) {
            // todo  修改存在的报价单 成本单对应多个报价单
            $where = [
                'order_id' => $finalGoods->order_id,
                'order_final_id' => $finalGoods->order_final_id,
                'goods_id' => $finalGoods->goods_id
            ];
            $update_data = [
                'price' => $finalGoods->price,
                'tax_price' => $finalGoods->tax_price,
                'all_price' => $finalGoods->all_price,
                'all_tax_price' => $finalGoods->all_tax_price,
                'delivery_time' => $finalGoods->delivery_time,
                'relevance_id' => $finalGoods->relevance_id,
            ];
            QuoteGoods::updateAll($update_data, $where);
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        }
    }

    public function actionMerge($id)
    {
        $orderFinal = OrderFinal::findOne($id);
        $finalGoodsList = FinalGoods::find()->where(['order_final_id' => $id])->all();

        $goods_ids     = [];
        $more_goods_id = [];
        foreach ($finalGoodsList as $key => $finalGoods) {
            if (in_array($finalGoods->goods_id, $goods_ids)) {
                $more_goods_id[] = $finalGoods->goods_id;
            } else {
                $goods_ids[] = $finalGoods->goods_id;
            }
        }

        foreach ($more_goods_id as $goods_id) {
            $finalGoods = FinalGoods::find()->where([
                'order_final_id'    => $id,
                'is_deleted'        => 0,
                'goods_id'          => $goods_id,
                'purchase_is_show'  => FinalGoods::IS_SHOW_YES,
            ])->one();

            $purchase_number = $finalGoods->number;

            $finalGoods->purchase_is_show = FinalGoods::IS_SHOW_NO;
            $finalGoods->save();

            $lastFinalGoods = FinalGoods::find()->where([
                'order_final_id'    => $id,
                'is_deleted'        => 0,
                'goods_id'          => $goods_id,
                'purchase_is_show'  => FinalGoods::IS_SHOW_YES,
            ])->one();
            $lastFinalGoods->number += $purchase_number;
            $lastFinalGoods->save();
        }
        $orderFinal->is_merge = OrderFinal::IS_MERGE_YES;
        $orderFinal->save();
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['index']);
    }

    /**
     * 一键最低
     */
    public function actionLow($id)
    {
        $finalGoodsList = FinalGoods::find()->where([
            'order_final_id'    => $id,
            'is_deleted'        => 0,
            'purchase_is_show'  => FinalGoods::IS_SHOW_YES
        ])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($finalGoodsList as $key => $finalGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $finalGoods->goods_id])->orderBy('price asc')->one();
            if ($inquiry) {
                $finalGoods->price              = $inquiry->price;
                $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
                $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
                $finalGoods->relevance_id       = $inquiry->id;
                $finalGoods->delivery_time      = $inquiry->delivery_time;
                $finalGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['create-purchase', 'id' => $id]);
    }

    /**
     * 一键最短
     */
    public function actionShort($id)
    {
        $finalGoodsList = FinalGoods::find()->where([
            'order_final_id'    => $id,
            'is_deleted'        => 0,
            'purchase_is_show'  => FinalGoods::IS_SHOW_YES
        ])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($finalGoodsList as $key => $finalGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $finalGoods->goods_id])->orderBy('delivery_time asc')->one();
            if ($inquiry) {
                $finalGoods->price              = $inquiry->price;
                $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
                $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
                $finalGoods->relevance_id       = $inquiry->id;
                $finalGoods->delivery_time      = $inquiry->delivery_time;
                $finalGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['create-purchase', 'id' => $id]);
    }

    /**
     * 一键优选
     */
    public function actionBetter($id)
    {
        $finalGoodsList = FinalGoods::find()->where([
            'order_final_id'    => $id,
            'is_deleted'        => 0,
            'purchase_is_show'  => FinalGoods::IS_SHOW_YES
        ])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($finalGoodsList as $key => $finalGoods) {
            $inquiry = Inquiry::find()->where([
                'good_id'           => $finalGoods->goods_id,
                'is_better'         => Inquiry::IS_BETTER_YES,
                'is_confirm_better' => 1
            ])->one();
            if ($inquiry) {
                $finalGoods->price              = $inquiry->price;
                $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
                $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
                $finalGoods->relevance_id       = $inquiry->id;
                $finalGoods->delivery_time      = $inquiry->delivery_time;
                $finalGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['create-purchase', 'id' => $id]);
    }

    /**
     * 一键最新
     */
    public function actionNew($id)
    {
        $finalGoodsList = FinalGoods::find()->where([
            'order_final_id'    => $id,
            'is_deleted'        => 0,
            'purchase_is_show'  => FinalGoods::IS_SHOW_YES
        ])->all();
        $system_tax = SystemConfig::find()->select('value')->where([
            'is_deleted' => SystemConfig::IS_DELETED_NO,
            'title'      => SystemConfig::TITLE_TAX,
        ])->scalar();
        foreach ($finalGoodsList as $key => $finalGoods) {
            $inquiry = Inquiry::find()->where(['good_id' => $finalGoods->goods_id])->orderBy('created_at Desc')->one();
            if ($inquiry) {
                $finalGoods->price              = $inquiry->price;
                $finalGoods->tax_price          = number_format($inquiry->price * (1 + $system_tax / 100), 2, '.', '');
                $finalGoods->all_price          = $finalGoods->number * $inquiry->price;
                $finalGoods->all_tax_price      = $finalGoods->number * $finalGoods->tax_price;
                $finalGoods->relevance_id       = $inquiry->id;
                $finalGoods->delivery_time      = $inquiry->delivery_time;
                $finalGoods->save();
            }
        }
        yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        return $this->redirect(['create-purchase', 'id' => $id]);
    }
}
