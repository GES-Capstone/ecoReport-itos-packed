<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\filekit\widget\Upload;

$this->title = Yii::t('backend', 'Editing User') . ": " . $model->username;
$this->registerCssFile('@web/css/profile.css', ['depends' => [\yii\web\YiiAsset::class]]);
$this->registerJsFile('@web/js/user/user-edit.js', ['depends' => \backend\assets\BackendAsset::class]);
?>

<div class="container-profile">
    <div class="card-main">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center" style="height: 60px; position: relative;">
            <a href="<?= Yii::$app->urlManager->createUrl(['/users/edit']) ?>"
                class="text-white position-absolute start-0 ms-3"
                style="text-decoration: none;">
                <i class="fa fa-arrow-left fa-lg"></i>
            </a>
            <h5 class="mb-0 text-center"><?= Yii::t('backend', 'Edit User') . ": " . $model->username ?></h5>
        </div>
        <div class="card-body">

            <div class="me-4 text-center p-3">
                <?= Html::img($modelProfile->getAvatar('/img/anonymous.png'), [
                    'id' => 'current-avatar',
                    'class' => 'img-thumbnail rounded-circle',
                    'style' => 'width: 120px; height: 120px; object-fit: cover; cursor: zoom-in;',
                    'alt' => 'Avatar'
                ]) ?>
            </div>

            <div class="flex-grow-1">
                <div class="mb-3 px-4">
                    <?= Html::button(Yii::t('backend', 'Update Profile Picture'), [
                        'class' => 'btn btn-primary w-100 mb-2',
                        'id' => 'upload-btn',
                        'style' => 'background-color: #0d6efd; border-color: #0b5ed7;'
                    ]) ?>
                    <?= Html::button(Yii::t('backend', 'Change Password'), [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'change-password-btn',
                        'style' => 'background-color: #5aa9f8; color: white; border: none;'
                    ]) ?>
                    <?php if (Yii::$app->user->can('super-administrator')): ?>
                        <?= Html::button(Yii::t('backend', 'View Current Password'), [
                            'class' => 'btn btn-warning w-100 mb-2',
                            'id'    => 'view-password-btn',
                            'style' => 'background-color: #ffc107; color: #212529; border: none;'
                        ]) ?>
                    <?php endif; ?>
                    <?= Html::button(Yii::t('backend', 'Update Profile'), [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'edit-data-btn',
                        'style' => 'background-color: #1c5d99; color: white; border: none;'
                    ]) ?>
                    <?= Html::button(Yii::t('backend', 'Update Role'), [
                        'class' => 'btn w-100',
                        'id' => 'edit-roles-btn',
                        'style' => 'background-color: #339af0; color: white; border: none;'
                    ]) ?>

                    <?php if (Yii::$app->user->can('changePermissions')): ?>
                        <?= Html::button(Yii::t('backend', 'Update Permissions'), [
                            'class' => 'btn w-100 mt-2',
                            'id' => 'edit-permissions-btn',
                            'style' => 'background-color: #228be6; color: white; border: none;'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-footer text-center">
            <p><strong><?= Yii::t('backend', 'Username') ?>:</strong> <?= Html::encode($model->username) ?></p>
            <p><strong><?= Yii::t('backend', 'Name') ?>:</strong> <?= Html::encode("{$modelProfile->firstname} {$modelProfile->lastname}" ?: '-') ?></p>
            <p><strong><?= Yii::t('backend', 'Profession') ?>:</strong> <?= Html::encode($modelProfile->profession ?: '-') ?></p>
            <p><strong><?= Yii::t('backend', 'Email') ?>:</strong> <?= Html::encode($model->email) ?></p>
        </div>
    </div>

    <div id="upload-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="height: 60px; position: relative;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Upload a New Image') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($modelProfile, 'picture')->widget(Upload::class, [
                'url' => ['avatar-upload', 'id' => $modelProfile->user_id],
            ]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save changes'), ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="change-password-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
            style="height: 60px; position: relative; background-color: #5aa9f8;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change Password') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'options' => ['style' => 'text-align:center;']
            ]); ?>

            <?= Html::hiddenInput('change_password', '1') ?>

            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Generate a New Password'), ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="user-data-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center"
            style="height: 60px; position: relative; background-color: #1c5d99;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change User Details') ?></h5>
        </div>
        <div class="card-body-secondary-update">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($modelProfile, 'firstname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'First Name')]) ?>
            <?= $form->field($modelProfile, 'lastname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Last Name')]) ?>
            <?= $form->field($modelProfile, 'profession')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Profession')]) ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Username')]) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Email')]) ?>
            <?php
            if (
                ! $user->isSuperAdministrator
                && (
                    Yii::$app->user->can('super-administrator')
                    || ! $user->isAdministrator
                )
            ):
            ?>
                <?= $form->field($model, 'status')
                    ->dropDownList(
                        [
                            2    => Yii::t('backend', 'Active'),
                            1 => Yii::t('backend', 'Inactive'),
                            3    => Yii::t('backend', 'Deleted'),
                        ],
                        ['prompt' => Yii::t('backend', 'Select the Status')]
                    ) ?>
            <?php endif; ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="user-roles-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
            style="height: 60px; position: relative; background-color: #339af0;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change Roles') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'roles')->radioList($roles, [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return '<div class="custom-radio">' .
                        Html::radio($name, $checked, [
                            'value' => $value,
                            'label' => $label,
                            'labelOptions' => ['style' => 'margin-left: 8px;'],
                        ]) .
                        '</div>';
                }
            ]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (Yii::$app->user->can('super-administrator')): ?>
        <div id="view-password-wrapper" class="card-secondary"
            style="display: <?= $viewPassword ? 'block' : 'none' ?>;">
            <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
                style="height: 60px; background-color: #ffc107;">
                <h5 class="mb-0"><?= Yii::t('backend', 'Unlock Current Password') ?></h5>
            </div>
            <div class="card-body-secondary">
                <?php $form = ActiveForm::begin([
                    'method'  => 'post',
                    'options' => ['style' => 'text-align:center;']
                ]); ?>

                <?= Html::hiddenInput('view_password', '1') ?>
                <?= Html::passwordInput('admin_password', null, [
                    'class' => 'form-control mb-3',
                    'placeholder' => Yii::t('backend', 'Your super-admin password'),
                    'required'    => true,
                ]) ?>
                <?= Html::submitButton(Yii::t('backend', 'Unlock'), [
                    'class' => 'btn btn-warning'
                ]) ?>

                <?php ActiveForm::end(); ?>

                <?php if ($viewPassword): ?>
                    <div class="alert alert-info alert-dismissible fade show mt-3 text-center" role="alert" data-timeout="10000">
                        <?= Yii::t('backend', 'Current password: {pwd}', ['pwd' => $viewPassword]) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->user->can('changePermissions')): ?>
        <div id="user-permissions-wrapper" class="card-secondary" style="display: none;">
            <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
                style="height: 60px; position: relative; background-color: #228be6;">
                <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change Permissions') ?></h5>
            </div>
            <div class="card-body-secondary">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'permissions')->checkboxList($permissions, [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<div class="custom-checkbox">' .
                            Html::checkbox($name, $checked, [
                                'value' => $value,
                                'label' => $label,
                                'labelOptions' => ['style' => 'margin-left: 8px;'],
                            ]) .
                            '</div>';
                    }
                ]) ?>
                <div class="text-center mt-3">
                    <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success mb-3']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>


    <?php endif; ?>
</div>

<div id="avatar-modal" class="modal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); justify-content:center; align-items:center; z-index:9999;">
    <span id="close-avatar-modal" style="position:absolute; top:20px; right:30px; color:white; font-size:30px; cursor:pointer;">&times;</span>
    <img id="avatar-modal-img" src="" style="max-width:90%; max-height:90%; border-radius:10px;" />
</div>