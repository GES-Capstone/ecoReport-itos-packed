<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model \backend\models\LoginForm */

$this->title = Yii::t('backend', 'Sign In');
$this->params['body-class'] = 'login-page';
?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-4">
        <div class="text-center mb-4">
            <h3><?= Html::encode($this->title) ?></h3>
            <p class="text-muted"><?= Yii::t('backend', 'Sign in to start your session') ?></p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'username', [
                    'inputTemplate' => '<div class="input-group mb-3">{input}<span class="input-group-text"><i class="fas fa-user"></i></span></div>',
                ]) ?>

                <?= $form->field($model, 'password', [
                    'inputTemplate' => '<div class="input-group mb-3">{input}<span class="input-group-text"><i class="fas fa-lock"></i></span></div>',
                ])->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <?= Html::submitButton(
                    Yii::t('backend', 'Sign In') . ' <i class="fas fa-arrow-right fa-sm"></i>',
                    ['class' => 'btn btn-primary w-100', 'name' => 'login-button']
                ) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>