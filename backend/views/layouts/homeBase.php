<?php
/**
 * Layout para "Nueva Carga"
 * @var yii\web\View $this
 * @var string $content
 */

use backend\assets\BackendAsset;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Html;
use rmrevin\yii\fontawesome\FAS;
use backend\widgets\MainSidebarMenu;
use common\models\TimelineEvent;
use rmrevin\yii\fontawesome\FAR;
use backend\modules\system\models\SystemLog;

$this->beginContent('@backend/views/layouts/configurationHomeBase.php');

BackendAsset::register($this);

$keyStorage = Yii::$app->keyStorage;
?>

<div class="wrapper">

    <?php
    NavBar::begin([
        'renderInnerContainer' => false,
        'options' => [
            'class' => [
                'main-header',
                'navbar',
                'navbar-expand',
                'navbar-dark',
                'bg-success',
                $keyStorage->get('adminlte.navbar-no-border') ? 'border-bottom-0' : null,
                $keyStorage->get('adminlte.navbar-small-text') ? 'text-sm' : null,
            ],
        ],
    ]);

    echo Nav::widget([
        'options' => ['class' => ['navbar-nav']],
        'encodeLabels' => false,
        'items' => [
            [
                'label' => FAS::icon('ellipsis-v', ['class' => 'navbar-icon-btn']),
                'url' => '#',
                'options' => [
                    'data' => ['widget' => 'pushmenu'],
                    'role' => 'button',
                    'class' => 'nav-link',
                ]
            ],
            [
                'label' => FAS::icon('home', ['class' => 'navbar-icon-btn']),
                'url' => ['/home/index'],
                'options' => [
                    'class' => 'nav-link',
                ],
            ],
        ]
    ]);
    
    echo Nav::widget([
        'options' => ['class' => ['navbar-nav', 'ms-auto']],
        'encodeLabels' => false,
        'items' => [
            '<li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                    ' . Html::img(Yii::$app->user->identity->userProfile->getAvatar('/img/anonymous.png'), [
                        'class' => 'img-circle elevation-2 bg-white user-image',
                        'alt' => 'User image'
                    ]) . '
                    ' . Html::tag('span', Yii::$app->user->identity->publicIdentity, ['class' => 'd-none d-md-inline']) . '
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header text-center bg-primary text-white py-3">
                        ' . Html::img(Yii::$app->user->identity->userProfile->getAvatar('/img/anonymous.png'), [
                            'class' => 'img-circle elevation-2 bg-white mb-2',
                            'alt' => 'User image',
                            'style' => 'width: 60px; height: 60px;',
                        ]) . '
                        <p class="mb-0">' . Yii::$app->user->identity->publicIdentity . '</p>
                        <small>' . Yii::t('backend', 'Member since {0, date, short}', Yii::$app->user->identity->created_at) . '</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="px-3 pb-2">
                        <div class="d-flex justify-content-between">
                            ' . Html::a(Yii::t('backend', 'Profile'), ['/sign-in/profile'], ['class' => 'btn btn-outline-secondary btn-sm']) . '
                            ' . Html::a(Yii::t('backend', 'Account'), ['/sign-in/account'], ['class' => 'btn btn-outline-secondary btn-sm']) . '
                            ' . Html::a(Yii::t('backend', 'Logout'), ['/sign-in/logout'], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post']) . '
                        </div>
                    </li>
                </ul>
            </li>',
        ]
    ]);

    NavBar::end();
    ?>


<aside class="main-sidebar sidebar-dark-primary elevation-4 <?php echo $keyStorage->get('adminlte.sidebar-no-expand') ? 'sidebar-no-expand' : null ?>">

<div class="sidebar">

    <nav class="mt-2">
        <?php echo MainSidebarMenu::widget([
            'options' => [
                'class' => [
                    'nav',
                    'nav-pills',
                    'nav-sidebar',
                    'flex-column',
                    $keyStorage->get('adminlte.sidebar-small-text') ? 'text-sm' : null,
                    $keyStorage->get('adminlte.sidebar-flat') ? 'nav-flat' : null,
                    $keyStorage->get('adminlte.sidebar-legacy') ? 'nav-legacy' : null,
                    $keyStorage->get('adminlte.sidebar-compact') ? 'nav-compact' : null,
                    $keyStorage->get('adminlte.sidebar-child-indent') ? 'nav-child-indent' : null,
                ],
                'data' => [
                    'widget' => 'treeview',
                    'accordion' => 'false'
                ],
                'role' => 'menu',
            ],
            'items' => [    
                [
                    'label' => Html::img('@web/img/Logo-EcoReport.jpg', [
                        'style' => 'max-width: 100%; height: auto; margin: 1rem auto; display: block;',
                        'alt' => 'Logo',
                    ]),
                    'encode' => false,
                    'options' => ['class' => 'text-center'],
                ],
                [
                    'label' => Yii::t('backend', 'Gestionar Usuarios'),
                    'icon' => FAS::icon('users-cog', ['class' => ['nav-icon']]),
                    'url' => '#',
                    'options' => ['class' => 'nav-item has-treeview'],
                    'items' => [
                        [
                            'label' => Yii::t('backend', 'Editar Usuario'),
                            'url' => ['/home/edit'],
                            'icon' => FAR::icon('edit', ['class' => ['nav-icon']]),
                            'visible' => Yii::$app->user->can('manager') || Yii::$app->user->can('administrator'),
                        ],
                        [
                            'label' => Yii::t('backend', 'Crear Usuario'),
                            'url' => ['/home/create'],
                            'icon' => FAR::icon('plus-square', ['class' => ['nav-icon']]),
                            'visible' => Yii::$app->user->can('manager') || Yii::$app->user->can('administrator'),
                        ],
                    ],
                ],
            ],
        ]) ?>
    </nav>

</div>

</aside>
    <div class="content-wrapper p-4">
        <?= $this->render('//layouts/alerts') ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endContent(); ?>

<style>
.navbar-icon-btn {
    font-size: 1.5rem; 
    height: 70px;    
    display: flex;
    align-items: center;
    justify-content: center;
}
.navbar-nav .nav-link {
    height: 70px; 
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 1rem;
}
</style>
