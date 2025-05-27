<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="container mt-4">
    <p class="text-center fw-bold display-4 my-4"><?= Yii::t('backend', 'Select a Company to Edit or Create a New One') ?></h2>

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="mb-4">
        <div class="text-center">
            <?= Html::submitButton(Yii::t('backend', 'Create New Company'), [
            'name' => 'create_new_company',
            'value' => 1,
            'class' => 'btn btn-success ms-2'
            ]) ?>
        </div>
         <?= Html::label(Yii::t('backend', 'Company'), 'selected_company_id', ['class' => 'form-label']) ?>
        <?= Html::dropDownList(
            'selected_company_id',
            null,
            ArrayHelper::map($companies, 'id', 'name'),
            [
                'prompt' => Yii::t('backend', 'Select a company...'),
                'class' => 'form-select',
            ]
        ) ?>
    </div>

    <div class="text-center">
        <?= Html::a(Yii::t('backend', 'Back'), ['initial-configuration/step1'], ['class' => 'btn btn-secondary px-4']) ?>
        <?= Html::submitButton(Yii::t('backend', 'Edit Selected Company'), ['class' => 'btn btn-primary']) ?>
        <?php if ($config->step >= 3): ?>
            <?= Html::a(Yii::t('backend', 'Next'), ['initial-configuration/step3'], ['class' => 'btn btn-primary px-4']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
