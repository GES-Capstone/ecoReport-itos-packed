<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use common\models\MiningGroup;
use rmrevin\yii\fontawesome\FAS;

class NavbarWidget extends Widget
{
    public function run()
    {
        $miningGroup = MiningGroup::findOne(['id' => Yii::$app->user->identity->mining_group_id]);
        $logoUrl = $miningGroup ? $miningGroup->getLogo() : Yii::getAlias('@web/img/default-logo.png');

        return Html::tag(
            'nav',
            Html::tag(
                'div',
                $this->renderToggle() .
                    $this->renderBrand($logoUrl) .
                    $this->renderMenu(),
                ['class' => 'container-fluid']
            ),
            ['class' => 'navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top']
        );
    }

    protected function renderToggle()
    {
        return Html::button(
            Html::tag('span', '', ['class' => 'navbar-toggler-icon']),
            [
                'class' => 'navbar-toggler me-2 order-first',
                'type' => 'button',
                'id' => 'mobileSidebarToggle',
                'title' => 'Toggle Sidebar',
                'aria-label' => 'Toggle navigation'
            ]
        ) . Html::button(
            Html::tag('span', '', ['class' => 'navbar-toggler-icon']),
            [
                'class' => 'btn btn-link d-none d-lg-block order-lg-0 me-3 sidebar-toggle text-white',
                'id' => 'desktopSidebarToggle',
                'type' => 'button',
                'title' => 'Toggle Sidebar',
                'aria-label' => 'Toggle navigation',
            ]
        );
    }


    protected function renderBrand($logoUrl)
    {
        return Html::a(
            Html::img($logoUrl, [
                'alt' => 'Company Logo',
                'height' => '40',
                'onerror' => "this.src='" . Yii::getAlias('@web/img/default-logo.png') . "'"
            ]),
            Yii::$app->homeUrl,
            ['class' => 'navbar-brand order-lg-1 mx-lg-0 mx-auto']
        );
    }

    protected function renderMenu()
    {
        return Html::tag(
            'div',
            $this->renderNavItems() .
                UserMenuWidget::widget(),
            [
                'class' => 'collapse navbar-collapse order-lg-2',
                'id' => 'mainNavbar'
            ]
        );
    }

    protected function renderNavItems()
    {
        return Html::ul([
            [
                'label' => FAS::icon('home') . Html::tag('span', Yii::t('backend', 'Home'), ['class' => 'ms-1 d-none d-sm-inline']),
                'url' => ['/home'],
                'options' => ['class' => 'nav-item']
            ]
        ], [
            'class' => 'navbar-nav me-auto',
            'itemOptions' => ['class' => 'nav-item'],
            'encode' => false,
            'item' => function ($item) {
                return Html::a($item['label'], $item['url'], ['class' => 'nav-link']);
            }
        ]);
    }
}
