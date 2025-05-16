<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$this->title = 'Creación de Usuario';
?>

<?php
// Mostrar mensajes flash
foreach (Yii::$app->session->getAllFlashes() as $key => $messages) {
    $class = "alert alert-" . ($key == 'error' ? 'danger' : $key);
    $messages = is_array($messages) ? $messages : [$messages];

    foreach ($messages as $message) {
        echo "<div class=\"{$class} mb-3\">{$message}</div>";
    }
}
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card-header bg-primary text-white text-center position-relative pt-3" style="height: 60px;">
    <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>" class="position-absolute start-0 ms-3 text-white" style="text-decoration: none;">
        <i class="fa fa-arrow-left fa-lg"></i>
    </a>
    <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
</div>

<div class="row g-3 pt-3">
    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3">Datos del Usuario</h5>
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'placeholder' => 'Primer Nombre', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'middlename')->textInput(['maxlength' => true, 'placeholder' => 'Segundo Nombre', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true, 'placeholder' => 'Apellido', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Nombre de Usuario', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Correo Electrónico', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Contraseña', 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3">Roles y Capacidades</h5>

            <?php if (Yii::$app->user->can('administrator')): ?>
                <!-- Campo oculto para el ID del grupo seleccionado -->
                <?= Html::hiddenInput('selected_mining_group_id', '', ['id' => 'selected_mining_group_id']) ?>

                <!-- Campo de autocompletado para búsqueda de grupos mineros -->
                <?= $form->field($modelGM, 'ges_name')->widget(AutoComplete::classname(), [
                    'clientOptions' => [
                        'source' => yii\helpers\Url::to(['home/search-groups']),
                        'minLength' => 1,
                        'autoFill' => true,
                        'select' => new JsExpression("function(event, ui) {
                            this.value = ui.item.value;
                            $('#selected_mining_group_id').val(ui.item.id);
                            return false;
                        }"),
                    ],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Escribe para buscar grupos existentes...',
                    ]
                ])->label('Grupo Minero')->hint('<small class="text-muted">Si el grupo ya existe, el usuario será asignado a ese grupo. Si es nuevo, se creará automáticamente.</small>') ?>
            <?php endif; ?>

            <?= $form->field($model, 'roles')->checkboxList($roles, [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return '<div class="form-check">' . Html::checkbox($name, $checked, [
                        'value' => $value,
                        'label' => $label,
                        'class' => 'form-check-input',
                        'labelOptions' => ['class' => 'form-check-label'],
                    ]) . '</div>';
                }
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList([
                2 => 'Activo',
                1 => 'Inactivo',
                3 => 'Eliminado'
            ], ['prompt' => 'Seleccione el Estado', 'class' => 'form-select']) ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3">Información adicional</h5>

            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fa fa-info-circle me-2"></i>Recordatorio:</h6>
                <p class="mb-0">Complete todos los campos requeridos para crear un usuario.</p>
            </div>

            <ul class="list-group mt-3">
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span>Todos los usuarios deben pertenecer a un grupo minero</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span>Al menos un rol debe ser asignado</span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span>Las contraseñas deben tener al menos 6 caracteres</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="form-group text-center mt-4">
    <?= Html::submitButton('Crear Usuario', ['class' => 'btn btn-success px-5 py-2']) ?>
    <?= Html::a('Cancelar', ['home/edit'], ['class' => 'btn btn-outline-secondary px-5 py-2 ms-2']) ?>
</div>

<?php ActiveForm::end(); ?>