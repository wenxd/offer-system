<?php

namespace app\extend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

class Bar extends Widget
{
    public $buttons = [];

    public $template = "{refresh} {create} {delete}";

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initDefaultButtons();
        $buttons = $this->renderDataCellContent();

        return "<div class='btn-group'>{$buttons}</div>";
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent()
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                return $this->buttons[$name] instanceof \Closure ? call_user_func($this->buttons[$name]) : $this->buttons[$name];
            } else {
                return '';
            }

        }, $this->template);
    }

    /**
     * 生成默认按钮
     *
     */
    protected function initDefaultButtons()
    {
        // if (! isset($this->buttons['refresh'])) {
        //     $this->buttons['refresh'] = function () {
        //         return Html::a('<i class="fa fa-refresh"></i> 刷新', Url::to(['refresh']), [
        //             'data-pjax' => '0',
        //             'class'     => 'btn btn-default btn-flat refresh',
        //         ]);
        //     };
        // }


        if (! isset($this->buttons['sort'])) {
            $this->buttons['sort'] = function () {
                ActiveForm::begin([
                    'action' => Url::to(['sort']),
                    'options' => ['name' => 'sort', 'method' => 'POST']
                ]);
                ActiveForm::end();
                return Html::a('<i class="fa fa-sort"></i> 排序', 'javascript: void(0)', [
                    'data-pjax' => '0',
                    'class'     => 'btn btn-default btn-flat multi-sort',
                ]);
            };
        }

        if (! isset($this->buttons['create'])) {
            $this->buttons['create'] = function () {
                return Html::a('<i class="fa fa-plus"></i> 创建', Url::to(['create']), [
                    'data-pjax' => '0',
                    'class'     => 'btn btn-primary btn-flat',
                ]);
            };
        }

        if (! isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function () {
                return Html::a('<i class="fa fa-trash"></i> 删除', Url::to(['delete']), [
                    'data-pjax'    => '0',
                    'data-confirm' => '确认删除吗？',
                    'class' => 'btn btn-danger btn-flat multi-delete',
                ]);
            };
        }
    }
}