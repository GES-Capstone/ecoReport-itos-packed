<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Seleccionar Compañía';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Selecciona una compañía para editar o crea una nueva</h2>

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="mb-4">
        <div class="text-center">
            <?= Html::submitButton('Crear Nueva Compañía', [
            'name' => 'create_new_company',
            'value' => 1,
            'class' => 'btn btn-success ms-2'
            ]) ?>
        </div>
        <?= Html::label('Compañía', 'selected_company_id', ['class' => 'form-label']) ?>
        <?= Html::dropDownList(
            'selected_company_id',
            null,
            ArrayHelper::map($companies, 'id', 'name'),
            [
                'prompt' => 'Selecciona una compañía...',
                'class' => 'form-select',
            ]
        ) ?>
    </div>

    <div class="text-center">
        <?= Html::a('Volver', ['initial-configuration/step1'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton('Editar Compañía Seleccionada', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Siguiente', ['initial-configuration/step3'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
