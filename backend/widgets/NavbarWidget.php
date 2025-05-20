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
        $logoUrl = $miningGroup && $miningGroup->getLogo() ? $miningGroup->getLogo() : Yii::getAlias('@web/img/default-logo.png');
        return Html::tag(
            'nav',
            Html::tag(
                'div',
                $this->renderToggle() .
                    $this->renderBrand($logoUrl) .
                    $this->renderMenu(),
                ['class' => 'container-fluid']
            ),
            ['class' => 'navbar navbar-expand-md navbar-dark bg-primary shadow-sm fixed-top']
        );
    }

    protected function renderToggle()
    {
        return Html::button(
            Html::tag('span', '', ['class' => 'navbar-toggler-icon']),
            [
                'class' => 'btn btn-link me-3 text-white',
                'id' => 'desktopSidebarToggle',
                'type' => 'button',
                'title' => 'Toggle Sidebar',
                'aria-label' => 'Toggle navigation',
                'data-bs-toggle' => 'offcanvas',
                'data-bs-target' => '#sidebar',
                'aria-controls' => 'sidebar',
            ]
        );
    }


    protected function renderBrand($logoUrl)
    {
        return Html::a(
            Html::img($logoUrl, [
                'alt' => 'Company Logo',
                'height' => '40',
                'onerror' => "this.src='" . Yii::getAlias('@web/img/default-logo.png') . "'",
            ]),
            Yii::$app->homeUrl,
            ['class' => 'navbar-brand']
        );
    }

    protected function renderMenu()
    {
        return Html::tag(
            'div',
            $this->renderNavItems() .
                UserMenuWidget::widget(),
            [
                'class' => 'd-none d-md-flex w-100 align-items-center justify-content-between',
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
