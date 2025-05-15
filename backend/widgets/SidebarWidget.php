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
                'class' => 'sidebar bg-white shadow-sm position-fixed h-100 overflow-auto offcanvas offcanvas-start',
                'id' => 'sidebar',
                'tabindex' => '-1',
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
                'class' => 'logo h-100 mw-100',
            ]),
            ['class' => 'sidebar-header d-flex justify-content-center align-items-center p-3 border-bottom object-fit-contain']
        );
    }

    protected function renderMenu()
    {
        return SidebarMenuWidget::widget() .
            Html::tag(
                'div',
                Html::tag('div', UserMenuWidget::renderCompactUserBlock(), ['class' => 'd-block d-md-none']),
                ['class' => 'd-flex justify-content-center p-4 mt-auto border-top']
            );
    }
}
