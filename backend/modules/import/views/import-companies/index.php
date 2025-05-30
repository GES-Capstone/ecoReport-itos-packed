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
/* @var $templateUrl string */
/* @var $miningGroupId int|null */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Importación de Compañías';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="import-companies-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        
    </div>

    <!-- Información principal -->
    <div class="card border-primary mb-4">
        <div class="card-body bg-primary bg-opacity-10">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title text-primary mb-2">
                        <i class="bi bi-upload"></i> Importar Compañías desde Excel
                    </h5>
                    <p class="card-text mb-0 text-muted">
                        Cargue múltiples compañías de una vez usando nuestra plantilla Excel
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <?= Html::a(
                        '<i class="bi bi-download"></i> Descargar Plantilla',
                        $templateUrl,
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
                            'class' => 'dropzone-form'
                        ]
                    ]); ?>

                    <?= $form->field($model, 'type')->hiddenInput(['value' => 'company'])->label(false) ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'excelFile')->fileInput([
                            'class' => 'form-control form-control-lg',
                            'accept' => '.xlsx,.xls',
                            'id' => 'excel-file-input'
                        ])->label('Seleccione el archivo Excel de compañías') ?>
                        

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
                        <i class="bi bi-list-check"></i> Instrucciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="step-list">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">1</span>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Descargar plantilla</strong><br>
                                <small class="text-muted">Haga clic en el botón verde</small>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">2</span>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Complete los datos</strong><br>
                                <small class="text-muted">Llene las columnas requeridas</small>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">3</span>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Suba el archivo</strong><br>
                                <small class="text-muted">Use el formulario de la izquierda</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-primary">
                        <i class="bi bi-exclamation-triangle"></i> Datos Requeridos
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <span class="badge bg-danger">Requerido</span>
                            <strong>Compania</strong><br>
                            <small class="text-muted">Nombre de la compañía</small>
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-danger">Requerido</span>
                            <strong>Location</strong><br>
                            <small class="text-muted">Formato: latitud,longitud<br>
                            Ejemplo: -33.4569,-70.6483</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>