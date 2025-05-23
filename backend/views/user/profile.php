<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\filekit\widget\Upload;

$this->title = Yii::t('backend', 'My Profile');
$this->registerCssFile('@web/css/profile.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="container-profile">

    <div class="card-main">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center" style="height: 60px; position: relative;">
            <a href="<?= Yii::$app->urlManager->createUrl(['/']) ?>"
                class="text-white position-absolute start-0 ms-3"
                style="text-decoration: none;">
                <i class="fa fa-arrow-left fa-lg"></i>
            </a>
            <h5 class="mb-0 text-center"><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="card-body">

            <div class="me-4 text-center p-3">
                <?= Html::img($model->getAvatar('/img/anonymous.png'), [
                    'id' => 'current-avatar',
                    'class' => 'img-thumbnail rounded-circle',
                    'style' => 'width: 120px; height: 120px; object-fit: cover; cursor: zoom-in;',
                    'alt' => 'Avatar'
                ]) ?>
            </div>


            <div class="flex-grow-1 p-3">
                <div class="mb-3">
                    <?= Html::button(Yii::t('backend', 'Upload New Image'), [
                        'class' => 'btn btn-primary w-100 mb-2',
                        'id' => 'upload-btn',
                        'style' => 'background-color: #0d6efd; border-color: #0b5ed7;'
                    ]) ?>
                    <?= Html::button(Yii::t('backend', 'Change Password'), [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'change-password-btn',
                        'style' => 'background-color: #5aa9f8; color: white; border: none;'
                    ]) ?>
                    <?= Html::button(Yii::t('backend', 'Change Username'), [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'change-username-btn',
                        'style' => 'background-color: #1c5d99; color: white; border: none;'
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <p><strong><?= Yii::t('backend', 'Username') ?>:</strong> <?= Html::encode($modelProfile->username) ?></p>
            <p><strong><?= Yii::t('backend', 'Name') ?>:</strong> <?= Html::encode(Yii::$app->user->identity->publicIdentity) ?></p>
            <p><strong><?= Yii::t('backend', 'Profession') ?>:</strong> <?= Html::encode($model->profession ?: '-') ?></p>
            <p><strong><?= Yii::t('backend', 'Email') ?>:</strong> <?= Html::encode($modelProfile->email) ?></p>

        </div>
    </div>
    <div id="upload-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center  mb-3" style="height: 60px; position: relative;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Upload New Image') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($model, 'picture')->widget(Upload::class, ['url' => ['avatar-upload']]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success  mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="change-password-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center  mb-3"
            style="height: 60px; position: relative; background-color: #5aa9f8;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change Password') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($modelAccount, 'current_password')->passwordInput(['placeholder' => Yii::t('backend', 'Current Password')]) ?>
            <?= $form->field($modelAccount, 'password')->passwordInput(['placeholder' => Yii::t('backend', 'New Password')]) ?>
            <?= $form->field($modelAccount, 'password_confirm')->passwordInput(['placeholder' => Yii::t('backend', 'Repeat New Password')]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success  mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="change-username-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center  mb-3"
            style="height: 60px; position: relative; background-color: #1c5d99;">
            <h5 class="mb-0 text-center w-100"><?= Yii::t('backend', 'Change Username') ?></h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($modelProfile, 'username')->textInput(['placeholder' => Yii::t('backend', 'New Username')]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton(Yii::t('backend', 'Save Changes'), ['class' => 'btn btn-success  mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<div id="avatar-modal" class="modal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); justify-content:center; align-items:center; z-index:9999;">
    <span id="close-avatar-modal" style="position:absolute; top:20px; right:30px; color:white; font-size:30px; cursor:pointer;">&times;</span>
    <img id="avatar-modal-img" src="" style="max-width:90%; max-height:90%; border-radius:10px;" />
</div>

<?php
$this->registerJs(<<<JS
function toggleVisibility(targetId, others) {
    const target = document.getElementById(targetId);
    const isVisible = target.style.display === 'block';
    

    others.forEach(id => {
        document.getElementById(id).style.display = 'none';
    });


    target.style.display = isVisible ? 'none' : 'block';
}


document.getElementById('upload-btn').addEventListener('click', function () {
    toggleVisibility('upload-wrapper', ['change-password-wrapper', 'change-username-wrapper']);
});

document.getElementById('change-password-btn').addEventListener('click', function () {
    toggleVisibility('change-password-wrapper', ['upload-wrapper', 'change-username-wrapper']);
});


document.getElementById('change-username-btn').addEventListener('click', function () {
    toggleVisibility('change-username-wrapper', ['upload-wrapper', 'change-password-wrapper']);
});


document.getElementById('current-avatar').addEventListener('click', function () {
    const modal = document.getElementById('avatar-modal');
    const modalImg = document.getElementById('avatar-modal-img');
    modalImg.src = this.src;
    modal.style.display = 'flex';
});


document.getElementById('close-avatar-modal').addEventListener('click', function () {
    document.getElementById('avatar-modal').style.display = 'none';
});


document.getElementById('avatar-modal').addEventListener('click', function (e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        document.getElementById('loading-overlay').style.display = 'flex';
    });
});

window.addEventListener('load', () => {
    document.getElementById('loading-overlay').style.display = 'none';
});

JS);
?>