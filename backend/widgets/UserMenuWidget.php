<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FAS;

class UserMenuWidget extends Widget
{
    public function run()
    {
        $user = Yii::$app->user->identity;

        return Html::tag(
            'ul',
            Html::tag(
                'li',
                $this->renderDropdown($user),
                ['class' => 'nav-item dropdown']
            ),
            ['class' => 'navbar-nav']
        );
    }

    protected function renderDropdown($user)
    {
        return Html::a(
            $this->renderUserThumbnail($user) .
                Html::tag('span', $user->publicIdentity, ['class' => 'ms-1']),
            '#',
            [
                'class' => 'nav-link dropdown-toggle d-flex align-items-center',
                'id' => 'userDropdown',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'aria-expanded' => 'false'
            ]
        ) . $this->renderDropdownMenu($user);
    }

    protected function renderUserThumbnail($user)
    {
        return Html::img(
            $user->userProfile->getAvatar('/img/anonymous.png'),
            [
                'class' => 'rounded-circle me-2',
                'width' => '32',
                'height' => '32',
                'style' => 'object-fit: cover;',
                'alt' => 'User image'
            ]
        );
    }

    protected function renderDropdownMenu($user)
    {
        return Html::tag(
            'div',
            $this->renderUserInfo($user) .
                $this->renderMenuItems(),
            [
                'class' => 'dropdown-menu dropdown-menu-end shadow',
                'aria-labelledby' => 'userDropdown'
            ]
        );
    }

    protected function renderUserInfo($user)
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                $this->renderUserThumbnail($user) .
                    Html::tag(
                        'div',
                        Html::tag('h6', $user->publicIdentity, ['class' => 'mb-0']) .
                            Html::tag('small', $user->email, ['class' => 'text-muted']),
                        ['class' => 'ms-3']
                    ),
                ['class' => 'd-flex align-items-center mb-3']
            ),
            ['class' => 'px-4 py-3 border-bottom']
        );
    }

    protected function renderMenuItems()
    {
        return Html::a(
            FAS::icon('user') . ' ' . Yii::t('backend', 'Profile'),
            ['/user/profile'],
            ['class' => 'dropdown-item py-2']
        ) . Html::a(
            FAS::icon('sign-out-alt') . ' ' . Yii::t('backend', 'Logout'),
            ['/sign-in/logout'],
            [
                'class' => 'dropdown-item py-2',
                'data-method' => 'post'
            ]
        );
    }

    public static function renderCompactUserBlock()
    {
        $user = Yii::$app->user->identity;

        return Html::tag(
            'div',
            Html::img(
                $user->userProfile->getAvatar('/img/anonymous.png'),
                [
                    'class' => 'rounded-circle',
                    'width' => '40',
                    'height' => '40',
                    'style' => 'object-fit: cover;',
                    'alt' => 'User image'
                ]
            ) .
                Html::a(
                    Html::tag('div', $user->publicIdentity, ['class' => 'fw-bold ms-2 text-decoration-none']),
                    ['/user/profile'],
                    ['class' => 'ms-2 text-dark text-decoration-none flex-grow-1 d-inline-block align-middle']
                ) .
                Html::a(
                    FAS::icon('sign-out-alt'),
                    ['/sign-in/logout'],
                    [
                        'class' => 'btn btn-sm btn-outline-danger ms-auto',
                        'style' => 'min-width: 36px;',
                        'data-method' => 'post',
                        'title' => Yii::t('backend', 'Logout'),
                    ]
                ),
            [
                'class' => 'd-flex align-items-center px-3 py-2',
                'style' => 'width: 250px; height: 20px;',
            ]
        );
    }
}
