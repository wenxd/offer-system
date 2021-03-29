<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Order;
use app\models\Customer;
use kartik\datetime\DateTimePicker;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
/* @var $number */

if ($model->isNewRecord) {
    $model->created_at = date('Y-m-d');
    $model->order_type = 1;
//    $model->order_sn = 'D' . date('ymd__') . $number;
    $model->order_sn = 'D' . date('ymd');
    $model->manage_name = Yii::$app->user->identity->username;
}


?>

<div class="box">

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <span class="base_order_sn" style="display: none"><?= $model->order_sn ?></span>
        <?= $form->field($model, 'order_type')->radioList(Order::$orderType, ['class' => 'radio']) ?>

        <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'customer_id')->widget(\kartik\select2\Select2::className(), [
//            'options' => ['placeholder' => '请输入客户名称/用途'],
            'pluginOptions' => [
                'id' => new JsExpression("function(rs) {
                return rs.taskId;
            }"),
                'multiple' => false,
                'allowClear' => true,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['search/get-customer-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) {
                return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) {
             return markup; }'),
                'templateResult' => new JsExpression('function(res) {
             return res.text; }'),
                'templateSelection' => new JsExpression('function (res) {
             return res.text; }'),
            ]
        ])->label('客户名称/用途') ?>

        <?= $form->field($model, 'customer_short_name')->textInput(['readonly' => true])->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'manage_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'first_party_id')->widget(\kartik\select2\Select2::className(), [
//            'options' => ['placeholder' => '请输入甲方采办人名称'],
            'pluginOptions' => [
                'id' => new JsExpression("function(rs) {
                return rs.taskId;
            }"),
//                'placeholder' => '搜索中 ...',
                'multiple' => false,
                'allowClear' => false,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['search/get-first-party-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) {
                return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) {
             return markup; }'),
                'templateResult' => new JsExpression('function(res) {
             return res.text; }'),
                'templateSelection' => new JsExpression('function (res) {
             return res.text; }'),
            ]
        ]) ?>

        <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'created_at')->widget(DateTimePicker::className(), [
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'startView' => 2,  //其实范围（0：日  1：天 2：年）
                'maxView' => 2,  //最大选择范围（年）
                'minView' => 2,  //最小选择范围（年）
            ]
        ]); ?>
    </div>

    <div class="box-footer">
        <?= Html::Button($model->isNewRecord ? '创建' : '更新', [
                'class' => 'btn btn-success created',
                'name' => 'submit-button']
        ) ?>
        <?= Html::a('<i class="fa fa-reply"></i> 返回', Url::to(['index']), [
            'class' => 'btn btn-default btn-flat',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?= Html::jsFile('@web/js/jquery-3.2.1.min.js') ?>
<script type="text/javascript" src="./js/layer.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var base_order_sn = $('.base_order_sn').html();
        $('input:radio').change(function (e) {
            var short_name = $('#order-customer_short_name').val();
            var first = base_order_sn.slice(0, 8);
            var end = base_order_sn.slice(8);
            var order_sn = first + short_name + end;
            if ($("input:radio:checked").val() == 1) {
                $('#order-order_sn').val(order_sn);
            } else {
                $('#order-order_sn').val('F' + order_sn.substring(1));
            }
        });

        init();

        function init() {
            var temp_id = '<?=$_GET['temp_id'] ?? ''?>';
            if (temp_id) {
                $("input[type='radio']").eq(1).attr('checked', 'checked');
                var short_name = $('#order-customer_short_name').val();
                var first = base_order_sn.slice(0, 8);
                var end = base_order_sn.slice(8);
                var order_sn = first + short_name + end;
                if ($("input:radio:checked").val() == 1) {
                    $('#order-order_sn').val(order_sn);
                } else {
                    $('#order-order_sn').val('F' + order_sn.substring(1));
                }
            }
        }

        $('#order-customer_id').change(function () {
            create_order_sn();
            return;
            var id = $(this).val();
            $.ajax({
                type: "get",
                url: "?r=customer/info",
                data: {id: id},
                dataType: 'JSON',
                success: function (res) {
                    var base_order_sn = $('#order-order_sn').val();
                    if (res && res.code == 200) {
                        $('#order-customer_short_name').val(res.data.short_name);
                        create_order_sn();
                        // base_order_sn = base_order_sn.split('_');
                        // var first = base_order_sn[0];
                        // var end   = base_order_sn[2];
                        // var order_sn = first + '_' + res.data.short_name + '_' + end;
                        // $('#order-order_sn').val(order_sn);
                    } else {
                        $('#order-customer_short_name').val('');
                        $('#order-order_sn').val(base_order_sn);
                    }
                }
            });
        });

        $('#order-first_party_id').change(function () {
            create_order_sn();
        });

        $('#order-project_name').bind('input propertychange', function () {
            create_order_sn();
        });

        /*生成订单编号*/
        function create_order_sn() {
            var order_sn = base_order_sn;
            // 客户名称用途
            var customer = $('#order-customer_id').find("option:selected").text();
            if (customer) {
                order_sn += '_' + customer;
            }
            // 甲方采办人
            var first_party = $('#order-first_party_id').find("option:selected").text();
            if (first_party) {
                order_sn += '_' + first_party;
            }
            // 项目名称
            var project_name = $('#order-project_name').val();
            if (project_name) {
                order_sn += '_' + project_name;
            }
            $('#order-order_sn').val(order_sn);
        }

        $('.created').on('click', function () {
            var parameter = '';

            var order_type = $('#order-order_type').find('input:checked').val();
            parameter += 'order_type=' + order_type + '&';
            var order_sn = $('#order-order_sn').val();
            if (order_sn === '') {
                layer.msg('请输入订单编号', {time: 2000});
                return false;
            }
            // 订单编号去重
            var status = false;
            $.ajax({
                type: "get",
                url: "<?=Url::to(['search/show-order-sn'])?>",
                data: {order_sn: order_sn},
                dataType: 'JSON',
                async: false,
                success: function (res) {
                    if (res && res.code == 200) {
                        status = true;
                    }
                }
            });
            if (status) {
                layer.msg('订单编号重复', {time: 2000});
                return false;
            }

            parameter += 'order_sn=' + order_sn + '&';
            var customer_id = $('#order-customer_id').val();
            if (customer_id == 0) {
                layer.msg('请选择客户名称', {time: 2000});
                return false;
            }
            parameter += 'customer_id=' + customer_id + '&';
            var manage_name = $('#order-manage_name').val();
            if (manage_name === '') {
                layer.msg('请输入订单管理员名称', {time: 2000});
                return false;
            }
            parameter += 'manage_name=' + manage_name + '&';

            var first_party_id = $('#order-first_party_id').val();
            console.log(first_party_id);
            if (first_party_id === '') {
                layer.msg('请输入甲方采办人名称', {time: 2000});
                return false;
            }
            parameter += 'first_party_id=' + first_party_id + '&';

            var project_name = $('#order-project_name').val();
            if (project_name === '') {
                layer.msg('请输入项目名称', {time: 2000});
                return false;
            }
            parameter += 'project_name=' + project_name + '&';
            // var provide_date = $('#order-provide_date').val();
            // if (provide_date === ''){
            //     layer.msg('请输入报价截止日期', {time:2000});
            //     return false;
            // }
            // parameter += 'provide_date=' + provide_date + '&';
            var created_at = $('#order-created_at').val();
            parameter += 'created_at=' + created_at;

            var temp_id = '<?=$_GET['temp_id'] ?? ''?>';
            if (order_type == 0 && temp_id) {
                parameter += '&temp_id=' + temp_id;
                location.replace("?r=order/direct-inquiry&" + encodeURI(parameter));
            } else {
                location.replace("?r=order/generate&" + encodeURI(parameter));
            }
        });
    });
</script>
