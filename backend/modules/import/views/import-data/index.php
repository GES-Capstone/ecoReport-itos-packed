<?php
/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */
/* @var $type string */
/* @var $templates array */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;


$this->title = 'Importación de Datos';
$this->params['breadcrumbs'][] = $this->title;

$types = [
    'company' => 'Compañías',
    'machinery' => 'Maquinaria/Flota',
    'component' => 'Componentes'
];
?>

<div class="import-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Importación de Datos</h5>
        </div>
        <div class="card-body">
            <!-- Navegación entre tipos -->
            <ul class="nav nav-pills nav-fill mb-4">
                <li class="nav-item">
                    <a class="nav-link <?= $type == 'company' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['index', 'type' => 'company']) ?>">
                        1. Compañías
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == 'machinery' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['index', 'type' => 'machinery']) ?>">
                        2. Maquinaria/Flota
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == 'component' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['index', 'type' => 'component']) ?>">
                        3. Componentes
                    </a>
                </li>
            </ul>

            <!-- Explicación del tipo actual -->
            <div class="alert alert-info">
                <h5>Importando: <?= $types[$type] ?></h5>
                <p class="mb-0">Descargue la plantilla, complete los datos y súbala para importar.</p>
            </div>

            <!-- Descargar plantilla -->
            <div class="mb-4">
                <?= Html::a(
                    '<i class="bi bi-download"></i> Descargar Plantilla',
                    $templates[$type],
                    ['class' => 'btn btn-success']
                ) ?>
            </div>

            <!-- Formulario de importación -->
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'type')->hiddenInput(['value' => $type])->label(false) ?>

            <div class="mb-3">
                <?= $form->field($model, 'excelFile')->fileInput(['class' => 'form-control']) ?>
            </div>

            <div class="mb-3">
                <?= Html::submitButton('Importar', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <!-- Ayuda según el tipo -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Instrucciones</h5>
                </div>
                <div class="card-body">
                    <?php if ($type == 'company'): ?>
                        <ol>
                            <li>Descargue la plantilla Excel para importar compañías</li>
                            <li>Complete los datos de las compañías (columnas A y B son obligatorias):
                                <ul>
                                    <li><strong>Compania</strong>: Nombre de la compañía</li>
                                    <li><strong>Location (lat,lng)</strong>: Ubicación en formato "latitud,longitud"</li>
                                </ul>
                            </li>
                            <li>Guarde el archivo y súbalo usando el formulario</li>
                        </ol>
                    <?php elseif ($type == 'machinery'): ?>
                        <ol>
                            <li>Descargue la plantilla Excel para importar maquinaria</li>
                            <li>Complete los datos (columnas principales obligatorias):
                                <ul>
                                    <li><strong>Tag Equipo (ID)</strong>: Identificador único del equipo</li>
                                    <li><strong>Compania</strong>: Nombre de una compañía existente</li>
                                    <li><strong>Flota</strong>: Nombre de la flota</li>
                                    <li><strong>Marca</strong>: Marca del equipo</li>
                                    <li><strong>Modelo</strong>: Modelo del equipo</li>
                                </ul>
                            </li>
                            <li>Guarde el archivo y súbalo usando el formulario</li>
                        </ol>
                    <?php elseif ($type == 'component'): ?>
                        <ol>
                            <li>Descargue la plantilla Excel para importar componentes</li>
                            <li>Complete los datos (columnas principales obligatorias):
                                <ul>
                                    <li><strong>Tag Maquinaria</strong>: Tag de una maquinaria existente</li>
                                    <li><strong>Tag Componente</strong>: Identificador único del componente</li>
                                    <li><strong>Nombre Componente</strong>: Nombre descriptivo</li>
                                </ul>
                            </li>
                            <li>Guarde el archivo y súbalo usando el formulario</li>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>