<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;


class SidebarWidget extends Widget
{
    public function run()
    {
        return Html::tag(
            'aside',
            $this->renderHeader() .
                $this->renderMenu(),
            [
                'class' => 'sidebar bg-white shadow-sm position-fixed h-100 overflow-auto',
                'id' => 'sidebar'
            ]
        );
    }

    protected function renderHeader()
    {
        return Html::tag(
            'div',
            Html::tag('img', '', [
                'src' => Yii::getAlias('@web/img/Logo-EcoReport.jpg'),
                'alt' => 'EcoReport Logo',
                'class' => 'logo ',
            ]),
            ['class' => 'sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom']
        );
    }

    protected function renderMenu()
    {
        return SidebarMenuWidget::widget();
    }
}
