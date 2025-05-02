<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="importacion-minera-form">
    <h1>Importar Grupos Mineros y Compañías</h1>

    <p>El archivo Excel debe contener las siguientes columnas:</p>
    <ul>
        <li>Columna A: Name of the mining group</li>
        <li>Columna B: Name of the company</li>

    </ul>

    <div class="panel panel-default">
        <div class="panel-heading">Seleccionar archivo</div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'excelFile')->fileInput(['accept' => '.xlsx, .xls'])->label('Archivo Excel') ?>

            <div class="form-group">
                <?= Html::submitButton('Importar', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>