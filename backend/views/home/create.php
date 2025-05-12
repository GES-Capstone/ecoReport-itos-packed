<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Creación de Usuario';
$this->registerCssFile('@web/css/createUser.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>


    <?php $form = ActiveForm::begin(); ?>
    <div class="card-header bg-primary text-white text-center position-relative" style="height: 60px;">
        <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>" 
        class="position-absolute start-0 ms-3 text-white" 
        style="text-decoration: none;">
            <i class="fa fa-arrow-left fa-lg"></i>
        </a>
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
    </div>


    <div class="row g-3">


        <div class="col-md-4">
            <div class="card h-100 p-3">
                <h5 class="fw-bold mb-3">Datos del Usuario</h5>
                <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'placeholder' => 'Primer Nombre']) ?>
                <?= $form->field($model, 'middlename')->textInput(['maxlength' => true, 'placeholder' => 'Segundo Nombre']) ?>
                <?= $form->field($model, 'lastname')->textInput(['maxlength' => true, 'placeholder' => 'Apellido']) ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Nombre de Usuario']) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Correo Electrónico']) ?>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Contraseña']) ?>
            </div>
        </div>


        <div class="col-md-4">
            <div class="card h-100 p-3">
                <h5 class="fw-bold mb-3">Roles y Capacidades</h5>
                <?php if (Yii::$app->user->can('administrator')): ?>
                    <?= $form->field($modelGM, 'ges_name')->textInput(['maxlength' => true]) ?>
                <?php endif; ?>
                <?= $form->field($model, 'roles')->checkboxList($roles, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return '<div class="custom-checkbox">' .
                            Html::checkbox($name, $checked, [
                                'value' => $value,
                                'label' => $label,
                                'labelOptions' => ['style' => 'margin-left: 8px;'],
                            ]) .
                        '</div>';
                    }
                ]) ?>
                 <?= $form->field($model, 'status')->dropDownList([2 => 'Activo', 1 => 'Inactivo', 3 => 'Eliminado'], ['prompt' => 'Seleccione el Estado']) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 p-3">
                <h5 class="fw-bold mb-3">Otras opciones</h5>
                <p class="text-muted">Puedes agregar campos adicionales aquí si es necesario.</p>
            </div>
        </div>
    </div>


    <div class="form-group text-center mt-4">
        <?= Html::submitButton('Crear Usuario', ['class' => 'btn btn-success px-5 py-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

