<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use app\models\Admin;
use app\models\AuthAssignment;
use app\models\OrderAgreementSearch;


/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待收款页面';
$this->params['breadcrumbs'][] = $this->title;

$use_admin = AuthAssignment::find()->where(['item_name' => '财务'])->all();
$adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');
$adminList = Admin::find()->where(['id' => $adminIds])->all();
$admins = [];
foreach ($adminList as $key => $admin) {
    $admins[$admin->id] = $admin->username;
}
$userId   = Yii::$app->user->identity->id;

?>
<div class="box table-responsive">
    <div class="box-header">
        <?= Html::button('刷新', [
            'class'   => 'btn btn-info upload',
            'name'    => 'submit-button',
            'onclick' => 'javascript:location.reload();',
        ])?>
        <?= Html::a('复位', Url::to(['index']), [
            'class'   => 'btn btn-success',
        ])?>
    </div>
    <div class="box-body">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                [
                    'attribute' => 'agreement_sn',
                    'label'     => '收入合同单号',
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a($model->agreement_sn, Url::to(['order-agreement/view', 'id' => $model->id]));
                    }
                ],
                'payment_price',
                'payment_ratio',
                'remain_price',
                [
                    'attribute'      => 'payment_at',
                    'contentOptions' => ['style'=>'min-width: 150px;'],
                    'filter'    => DateRangePicker::widget([
                        'name'  => 'OrderAgreementSearch[payment_at]',
                        'value' => Yii::$app->request->get('OrderAgreementSearch')['payment_at'],
                    ]),
                    'value'     => function($model){
                        return substr($model->payment_at, 0, 10);
                    }
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
        <table id="example2" class="table table-bordered table-hover">
            <tbody>
            <tr style="background-color: #acccb9">
                <td colspan="12" rowspan="2">汇总统计</td>
                <td>总金额</td>
                <td>待收款总金额</td>
            </tr>
            <tr style="background-color: #acccb9">
                <td class="sta_price"></td>
                <td class="sta_tax_price"></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?=Html::jsFile('@web/js/jquery-3.2.1.min.js')?>
<script type="text/javascript" src="./js/layer.js"></script>
<script type="text/javascript">
    init();

    function init() {
        var sta_price = 0;
        var sta_tax_price = 0;
        $('#w1').find('tbody').find('tr').each(function (i, e) {
            var price = parseFloat($(e).children('td').eq(2).text());
            if (price) {
                sta_price += price;
            }
            var remain_price = parseFloat($(e).children('td').eq(4).text());
            if (remain_price) {
                sta_tax_price += remain_price;
            }
        });

        $('.sta_price').text(sta_price.toFixed(2));
        $('.sta_tax_price').text(sta_tax_price.toFixed(2));
    }
</script>
