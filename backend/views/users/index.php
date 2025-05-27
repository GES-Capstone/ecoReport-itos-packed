<?php

/** @var yii\web\View $this */

$this->title = Yii::t('backend', 'Home Dashboard');

$user = Yii::$app->user->identity;
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4"><?= Yii::t('backend', 'Welcome to the Dashboard') ?></h1>
        <p class="display-5"><?= Yii::t('backend', 'Hello') . ", " . $user->publicIdentity ?></p>
        <p class="lead text-muted"><?= Yii::t('backend', "Here's a quick overview of your system.") ?></p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-people display-4 text-primary"></i>
                    </div>
                    <h5 class="card-title"><?= Yii::t('backend', 'Users') ?></h5>
                    <p class="card-text text-muted"><?= Yii::t('backend', 'Manage system users, roles, and access control.') ?></p>
                    <a href="<?= \yii\helpers\Url::to(['/user']) ?>" class="btn btn-outline-primary"><?= Yii::t('backend', 'View Users') ?></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-folder2-open display-4 text-success"></i>
                    </div>
                    <h5 class="card-title"><?= Yii::t('backend', 'Content') ?></h5>
                    <p class="card-text text-muted"><?= Yii::t('backend', 'Create and organize articles, pages, and other content types.') ?></p>
                    <a href="<?= \yii\helpers\Url::to(['/content']) ?>" class="btn btn-outline-success"><?= Yii::t('backend', 'Manage Content') ?></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-gear display-4 text-warning"></i>
                    </div>
                    <h5 class="card-title"><?= Yii::t('backend', 'System Settings') ?></h5>
                    <p class="card-text text-muted"><?= Yii::t('backend', 'Configure the backend and manage system-wide settings.') ?></p>
                    <a href="<?= \yii\helpers\Url::to(['/system']) ?>" class="btn btn-outline-warning"><?= Yii::t('backend', 'System Config') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>