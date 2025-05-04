<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */

$this->title = 'Importar Datos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="import-data-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="alert alert-info">
        <p><strong>Instrucciones:</strong></p>
        <ul>
            <li>El archivo debe estar en formato Excel (.xlsx o .xls)</li>
            <li>La primera fila debe contener los encabezados</li>
            <li>Columna A: Nombre de la Compañía</li>
            <li>Columna B: Ubicación (formato: latitud,longitud)</li>
        </ul>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Subir archivo Excel</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'excelFile')->fileInput(['accept' => '.xlsx, .xls']) ?>

            <div class="form-group">
                <?= Html::submitButton('Subir y Procesar', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>