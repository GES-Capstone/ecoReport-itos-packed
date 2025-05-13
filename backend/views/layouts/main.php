<?php

use backend\widgets\NavbarWidget;
use backend\widgets\SidebarWidget;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@backend/views/layouts/base.php');

?>

<div class="layout-wrapper">
    <?= NavbarWidget::widget() ?>
    <div class="d-flex min-vh-100">
        <?= SidebarWidget::widget() ?>

        <main class="main-content flex-grow-1">
            <div class="container-fluid p-4">
                <?= $this->render('//layouts/alerts') ?>
                <?= $content ?>
            </div>
        </main>
    </div>
</div>

<?php $this->endContent(); ?>