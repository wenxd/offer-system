<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/24
 * Time: 14:02
 */
namespace app\controllers;

use app\models\AuthAssignment;
use app\models\InquiryGoods;
use app\models\OrderGoods;
use app\models\QuoteRecord;
use app\models\SystemNotice;
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

    /*
     * 已废弃
     */
    public function actionUpdate($id)
    {
        if (! $id) throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));

        $model = Order::findOne($id);
        if (! $model) throw new BadRequestHttpException(yii::t('app', "Cannot find model by $id"));

        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                yii::$app->getSession()->setFlash('error', $err);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
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

        $data = [];
        $data['orderInquiry'] = $orderInquiry;
        $inquiryGoods = InquiryGoods::find()->where([
            'inquiry_sn' => $orderInquiry->inquiry_sn,
            'order_id'   => $orderInquiry->order_id,
            'is_deleted' => InquiryGoods::IS_DELETED_NO])->all();
        $data['inquiryGoods'] = $inquiryGoods;
        $data['orderGoods']   = $orderGoods;

        return $this->render('view', $data);
    }

    //询价确认接口
    public function actionConfirm($id)
    {
        $info = InquiryGoods::findOne($id);
        $info->is_inquiry = InquiryGoods::IS_INQUIRY_YES;
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
}
