<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FAS;
use rmrevin\yii\fontawesome\FAR;

class SidebarMenuWidget extends Widget
{
    public function run()
    {
        return Html::tag(
            'nav',
            Html::ul($this->getMenuItems(), [
                'class' => 'nav flex-column',
                'encode' => false,
                'itemOptions' => ['class' => 'nav-item'],
                'item' => function ($item) {
                    return $this->renderItem($item);
                }
            ]),
            ['class' => 'p-3']
        );
    }

    protected function getMenuItems()
    {
        $items = [
            [
                'label' => FAS::icon('home') . Html::tag('span', Yii::t('backend', 'Home'), ['class' => 'ms-2']),
                'url' => ['/home'],
                'active' => Yii::$app->controller->route === '/home'
            ]
        ];

        if (Yii::$app->user->can('createUser')) {
            $items[] = [
                'label' => FAS::icon('users-cog') . Html::tag('span', Yii::t('backend', 'Manage Users'), ['class' => 'ms-2']) .
                    FAS::icon('angle-down', ['class' => 'submenu-toggle']),
                'url' => '#',
                'items' => [
                    [
                        'label' => FAR::icon('edit') . Html::tag('span', Yii::t('backend', 'Edit Users'), ['class' => 'ms-2']),
                        'url' => ['/users/edit']
                    ],
                    [
                        'label' => FAR::icon('plus-square') . Html::tag('span', Yii::t('backend', 'Create Users'), ['class' => 'ms-2']),
                        'url' => ['/users/create']
                    ]
                ]
            ];
        }

        if (Yii::$app->user->can('administrator')) {
            $items[] = [
                'label' => FAS::icon('users-cog') . Html::tag('span', Yii::t('backend', 'Manage Mining Groups'), ['class' => 'ms-2']) .
                    FAS::icon('angle-down', ['class' => 'submenu-toggle']),
                'url' => '#',
                'items' => [
                    [
                        'label' => FAR::icon('edit') . Html::tag('span', Yii::t('backend', 'Edit Mining Group'), ['class' => 'ms-2']),
                        'url' => ['/mining-group']
                    ],
                    [
                        'label' => FAR::icon('plus-square') . Html::tag('span', Yii::t('backend', 'Create Mining Group'), ['class' => 'ms-2']),
                        'url' => ['/mining-group/create']
                    ]
                ]
            ];
        }

        return $items;
    }

    protected function renderItem($item)
    {
        $content = Html::a($item['label'], $item['url'], [
            'class' => 'nav-link d-flex align-items-center' . (isset($item['active']) && $item['active'] ? 'active' : '')
        ]);

        if (!empty($item['items'])) {
            $content .= Html::tag(
                'ul',
                implode('', array_map([$this, 'renderSubItem'], $item['items'])),
                ['class' => 'submenu ps-3 list-unstyled']
            );
            return Html::tag('li', $content, ['class' => 'has-submenu mb-2']);
        }

        return Html::tag('li', $content, ['class' => 'mb-2']);
    }

    protected function renderSubItem($item)
    {
        return Html::tag(
            'li',
            Html::a($item['label'], $item['url'], ['class' => 'nav-link']),
            ['class' => 'nav-item']
        );
    }
}
