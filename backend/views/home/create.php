<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use common\models\User;

$this->title = 'Creación de Usuario';
?>

<div class="card">
    <div class="card-header bg-primary text-white text-center position-relative" style="height: 60px;">
        <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>" class="position-absolute start-0 ms-3 text-white" style="text-decoration: none;">
            <i class="fa fa-arrow-left fa-lg"></i>
        </a>
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
    </div>

    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>
        
        <?php
        // Mostrar errores de modelo o mensajes flash (pero no ambos si son errores)
        $hasModelErrors = $model->hasErrors() || (isset($modelGM) && $modelGM->hasErrors());
        
        if ($hasModelErrors) {
            echo '<div class="alert alert-danger mb-4">';
            echo '<h5><i class="fa fa-exclamation-triangle me-2"></i> Por favor corrija los siguientes errores:</h5>';
            echo '<ul class="mb-0">';
            
            foreach ($model->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "<li><strong>{$model->getAttributeLabel($attribute)}</strong>: {$error}</li>";
                }
            }
            
            if (isset($modelGM) && $modelGM->hasErrors()) {
                foreach ($modelGM->getErrors() as $attribute => $errors) {
                    foreach ($errors as $error) {
                        echo "<li><strong>{$modelGM->getAttributeLabel($attribute)}</strong>: {$error}</li>";
                    }
                }
            }
            
            echo '</ul></div>';
        }

        // Mostrar mensajes flash (excepto errores cuando ya hay errores de modelo)
        foreach (Yii::$app->session->getAllFlashes() as $key => $messages) {
            if ($hasModelErrors && $key == 'error') continue;
            
            $class = "alert alert-" . ($key == 'error' ? 'danger' : $key);
            $messages = is_array($messages) ? $messages : [$messages];
            
            foreach ($messages as $message) {
                echo "<div class=\"{$class} mb-3\">{$message}</div>";
            }
        }
        ?>

        <div class="row g-3">
            <!-- Columna 1: Datos del Usuario -->
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <h5 class="fw-bold mb-3">Datos del Usuario</h5>
                    
                    <?= $form->field($model, 'firstname', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Primer Nombre']) ?>
                    
                    <?= $form->field($model, 'middlename', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Segundo Nombre']) ?>
                    
                    <?= $form->field($model, 'lastname', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Apellido']) ?>
                    
                    <?= $form->field($model, 'username')->textInput([
                        'maxlength' => true, 
                        'placeholder' => 'Nombre de Usuario'
                    ])->hint('<small class="text-muted">Si no se proporciona, se usará el email como nombre de usuario.</small>') ?>
                    
                    <?= $form->field($model, 'email', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Correo Electrónico']) ?>
                    
                    <?= $form->field($model, 'password', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->passwordInput(['maxlength' => true, 'placeholder' => 'Contraseña']) ?>
                </div>
            </div>

            <!-- Columna 2: Roles y Capacidades -->
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <h5 class="fw-bold mb-3">Roles y Capacidades</h5>
                    
                    <?php if (Yii::$app->user->can('administrator')): ?>
                        <?= Html::hiddenInput('selected_mining_group_id', '', ['id' => 'selected_mining_group_id']) ?>
                        
                        <?= $form->field($modelGM, 'ges_name', [
                            'labelOptions' => ['class' => 'required-indicator']
                        ])->widget(AutoComplete::classname(), [
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
                    
                    <?= $form->field($model, 'roles', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->checkboxList($roles, [
                        'item' => function($index, $label, $name, $checked, $value) {
                            return '<div class="mb-2">' . Html::checkbox($name, $checked, [
                                'value' => $value,
                                'label' => $label,
                                'labelOptions' => ['style' => 'margin-left: 8px;'],
                            ]) . '</div>';
                        }
                    ]) ?>
                    
                    <?= $form->field($model, 'status', [
                        'labelOptions' => ['class' => 'required-indicator']
                    ])->dropDownList([
                        User::STATUS_ACTIVE => 'Activo', 
                        User::STATUS_NOT_ACTIVE => 'Inactivo', 
                        User::STATUS_DELETED => 'Eliminado'
                    ], [
                        'prompt' => 'Seleccione el Estado',
                        'class' => 'form-select'
                    ]) ?>
                </div>
            </div>

            <!-- Columna 3: Información adicional -->
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <h5 class="fw-bold mb-3">Información adicional</h5>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fa fa-info-circle me-2"></i>Recordatorio:</h6>
                        <p class="mb-0">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
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
    </div>
</div>

<style>
    
.required-indicator:after {
    content: " *";
    color: #dc3545;
}
</style>