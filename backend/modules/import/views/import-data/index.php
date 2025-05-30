<?php
// JavaScript mínimo - solo cambiar botón a verde
$this->registerJs("
    $('#excel-file-input').on('change', function() {
        if (this.files[0]) {
            $('#submit-btn').removeClass('btn-primary').addClass('btn-success');
        }
    });
");
?><?php
/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */
/* @var $type string */
/* @var $templates array */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Importación de Equipos';
$this->params['breadcrumbs'][] = $this->title;

$types = [
    'machinery' => 'Equipos',
    'component' => 'Componentes'
];
?>

<div class="import-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="text-muted">
            <i class="bi bi-gear"></i> Gestión de Maquinaria
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Importación de Equipos y Componentes</h5>
        </div>
        <div class="card-body">
            <!-- Navegación entre tipos mejorada -->
            <div class="row mb-4">
                <div class="col-md-6 mx-auto">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a class="nav-link <?= $type == 'machinery' ? 'active' : '' ?>" 
                               href="<?= \yii\helpers\Url::to(['index', 'type' => 'machinery']) ?>">
                                <i class="bi bi-truck"></i> Equipos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $type == 'component' ? 'active' : '' ?>" 
                               href="<?= \yii\helpers\Url::to(['index', 'type' => 'component']) ?>">
                                <i class="bi bi-gear-fill"></i> Componentes
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Información principal - FIJO, no desaparece -->
            <div class="card border-primary mb-4">
                <div class="card-body bg-primary bg-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title text-primary mb-2">
                                <?php if ($type == 'machinery'): ?>
                                    <i class="bi bi-truck"></i> Importando: Equipos de Maquinaria
                                <?php else: ?>
                                    <i class="bi bi-gear-fill"></i> Importando: Componentes
                                <?php endif; ?>
                            </h5>
                            <p class="card-text mb-0 text-muted">
                                <?php if ($type == 'machinery'): ?>
                                    Cargue información de equipos y maquinaria para su flota minera
                                <?php else: ?>
                                    Importe componentes asociados a equipos existentes en el sistema
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?= Html::a(
                                '<i class="bi bi-download"></i> Descargar Plantilla',
                                $templates[$type],
                                [
                                    'class' => 'btn btn-success',
                                    'target' => '_blank'
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Formulario principal -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-cloud-upload"></i> Subir Archivo Excel
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php $form = ActiveForm::begin([
                                'options' => [
                                    'enctype' => 'multipart/form-data',
                                    'class' => 'upload-form'
                                ]
                            ]); ?>

                            <?= $form->field($model, 'type')->hiddenInput(['value' => $type])->label(false) ?>

                            <div class="mb-4">
                                <?= $form->field($model, 'excelFile')->fileInput([
                                    'class' => 'form-control form-control-lg',
                                    'accept' => '.xlsx,.xls',
                                    'id' => 'excel-file-input'
                                ])->label('Seleccione el archivo Excel de ' . strtolower($types[$type])) ?>
                                
                            </div>

                            <div class="d-grid gap-2">
                                <?= Html::submitButton(
                                    '<i class="bi bi-upload"></i> Procesar Importación', 
                                    [
                                        'class' => 'btn btn-primary btn-lg',
                                        'id' => 'submit-btn'
                                    ]
                                ) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

                <!-- Panel de instrucciones -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-list-check"></i> Campos Requeridos
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if ($type == 'machinery'): ?>
                                <div class="required-fields">
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Compañía</h6>
                                        <small class="text-muted">Nombre de la compañía</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Proceso Minero</h6>
                                        <small class="text-muted">Proceso minero asociado</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Familia Equipos</h6>
                                        <small class="text-muted">Familia del equipo</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Área</h6>
                                        <small class="text-muted">SEMI/MOVIL/FIJO</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Tag/Código</h6>
                                        <small class="text-muted">Identificador único</small>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6 class="text-success mb-2">
                                    <i class="bi bi-check-circle"></i> Campos Opcionales
                                </h6>
                                <small class="text-muted">
                                    Marca, Modelo, Inicio Operaciones, Vida Útil, 
                                    Proveedor, Costo, Ubicación
                                </small>
                                
                            <?php else: ?>
                                <div class="required-fields">
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Tag Maquinaria</h6>
                                        <small class="text-muted">Tag de equipo existente</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Tag Componente</h6>
                                        <small class="text-muted">Identificador único</small>
                                    </div>
                                    
                                    <div class="field-item mb-3">
                                        <span class="badge bg-danger mb-1">Requerido</span>
                                        <h6 class="mb-1">Nombre Componente</h6>
                                        <small class="text-muted">Nombre descriptivo</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


?>