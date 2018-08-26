<?php
namespace app\extend\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view-layer'])) {
            $this->buttons['view-layer'] = function ($url, $model, $key) {
                return Html::a('<i class="fa fa-eye"></i> 查看', 'javascript:;', [
                    'class'   => "btn btn-info btn-xs btn-flat",
                    'onclick' => 'alert(1);',
                ]);
            };
        }

        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                return Html::a('<i class="fa fa-eye"></i> 查看', $url, [
                    'class'     => "btn btn-info btn-xs btn-flat",
                ]);
            };
        }

        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                return Html::a('<i class="fa fa-edit"></i> 修改', $url, [
                    'class' => "btn btn-warning btn-xs btn-flat"
                ]);
            };
        }

        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                return Html::a('<i class="fa fa-trash"></i> 删除', $url, [
                    'class'        => "btn btn-danger btn-xs btn-flat",
                    'data-confirm' => "您确定删除此记录吗？",
                    'data-method'  => 'post',
                    'data-pjax'    => '0',
                ]);
            };
        }
    }
}
