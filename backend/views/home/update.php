<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;
use trntv\filekit\widget\Upload;

$this->title = 'Editar Usuario: ' . $model->username;
$this->registerCssFile('@web/css/profile.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="container-profile">
    <div class="card-main">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center" style="height: 60px; position: relative;">
            <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>"
                class="text-white position-absolute start-0 ms-3"
                style="text-decoration: none;">
                <i class="fa fa-arrow-left fa-lg"></i>
            </a>
            <h5 class="mb-0 text-center"><?= Html::encode($this->title) ?></h5>
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
                    <?= Html::button('Subir nueva imagen', [
                        'class' => 'btn btn-primary w-100 mb-2',
                        'id' => 'upload-btn',
                        'style' => 'background-color: #0d6efd; border-color: #0b5ed7;'
                    ]) ?>
                    <?= Html::button('Cambiar contraseña', [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'change-password-btn',
                        'style' => 'background-color: #5aa9f8; color: white; border: none;'
                    ]) ?>
                    <?= Html::button('Cambiar datos', [
                        'class' => 'btn w-100 mb-2',
                        'id' => 'edit-data-btn',
                        'style' => 'background-color: #1c5d99; color: white; border: none;'
                    ]) ?>
                    <?= Html::button('Actualizar rol', [
                        'class' => 'btn w-100',
                        'id' => 'edit-roles-btn',
                        'style' => 'background-color: #339af0; color: white; border: none;'
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="card-footer text-center">
            <p><strong><?= Yii::t('backend', 'Username') ?>:</strong> <?= Html::encode($model->username) ?></p>
            <p><strong><?= Yii::t('backend', 'Name') ?>:</strong> <?= Html::encode("{$modelProfile->firstname} {$modelProfile->lastname}" ?: '-') ?></p>
            <p><strong><?= Yii::t('backend', 'Email') ?>:</strong> <?= Html::encode($model->email) ?></p>
        </div>
    </div>

    <div id="upload-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="height: 60px; position: relative;">
            <h5 class="mb-0 text-center w-100">Subir nueva imagen</h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($modelProfile, 'picture')->widget(Upload::class, ['url' => ['avatar-upload']]) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton('Guardar cambios', ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <div id="change-password-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
            style="height: 60px; position: relative; background-color: #5aa9f8;">
            <h5 class="mb-0 text-center w-100">Cambiar contraseña</h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'options' => ['style' => 'text-align:center;']
            ]); ?>
            
            <?= Html::hiddenInput('change_password', '1') ?>
            
            <div class="text-center mt-3">
                <?= Html::submitButton('Generar nueva contraseña', ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <div id="user-data-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center"
            style="height: 60px; position: relative; background-color: #1c5d99;">
            <h5 class="mb-0 text-center w-100">Datos del Usuario</h5>
        </div>
        <div class="card-body-secondary-update">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($modelProfile, 'firstname')->textInput(['maxlength' => true, 'placeholder' => 'Nombre']) ?>
            <?= $form->field($modelProfile, 'lastname')->textInput(['maxlength' => true, 'placeholder' => 'Apellido']) ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Nombre de Usuario']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Correo Electrónico']) ?>
            <?= $form->field($model, 'status')->dropDownList([2 => 'Activo', 1 => 'Inactivo', 3 => 'Eliminado'], ['prompt' => 'Seleccione el Estado']) ?>
            <div class="text-center mt-3">
                <?= Html::submitButton('Guardar cambios', ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <div id="user-roles-wrapper" class="card-secondary" style="display: none;">
        <div class="card-header text-white d-flex align-items-center justify-content-center mb-3"
            style="height: 60px; position: relative; background-color: #339af0;">
            <h5 class="mb-0 text-center w-100">Cambiar Roles</h5>
        </div>
        <div class="card-body-secondary">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'roles')->checkboxList($roles, [
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
                <?= Html::submitButton('Guardar cambios', ['class' => 'btn btn-success mb-3']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="loading-overlay" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; justify-content: center; align-items: center;">
        <div style="color: white; font-size: 1.5rem; text-align: center;">
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
            </div>
            <div>Cargando...</div>
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
    toggleVisibility('upload-wrapper', ['change-password-wrapper', 'user-data-wrapper', 'user-roles-wrapper']);
});

document.getElementById('change-password-btn').addEventListener('click', function () {
    toggleVisibility('change-password-wrapper', ['upload-wrapper', 'user-data-wrapper', 'user-roles-wrapper']);
});

document.getElementById('edit-data-btn').addEventListener('click', function () {
    toggleVisibility('user-data-wrapper', ['upload-wrapper', 'change-password-wrapper', 'user-roles-wrapper']);
});

document.getElementById('edit-roles-btn').addEventListener('click', function () {
    toggleVisibility('user-roles-wrapper', ['upload-wrapper', 'change-password-wrapper', 'user-data-wrapper']);
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