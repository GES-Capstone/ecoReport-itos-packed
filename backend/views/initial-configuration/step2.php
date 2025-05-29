<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="container mt-5">
    <p class="text-center fw-bold display-4 my-4">
        <?= Yii::t('backend', 'Select a company to edit or create a new one') ?>
    </p>

    <div class="d-flex flex-column flex-md-row gap-4">
        <div class="card flex-fill shadow-sm">
            <div class="card-body">
                <div class="mb-4 text-center">
                    <?= Html::beginForm(['initial-configuration/step2'], 'get') ?>
                    <?= Html::dropDownList(
                        'company_id',
                        Yii::$app->request->get('company_id'),
                        ArrayHelper::map($companies, 'id', 'name'),
                        [
                            'prompt' => Yii::t('backend', 'Select a company...'),
                            'class' => 'form-select w-75 d-inline-block',
                            'id' => 'select-company',
                        ]
                    ) ?>
                    <?= Html::submitButton(Yii::t('backend', 'Edit'), ['class' => 'btn btn-primary ms-2']) ?>
                    <?= Html::a(Yii::t('backend', 'Create New'), ['initial-configuration/step2'], ['class' => 'btn btn-success ms-2']) ?>
                    <?= Html::endForm() ?>
                </div>

                <?php if ($modelCompany): ?>
                    <?php $form = ActiveForm::begin([
                        'id' => 'mining-process-form',
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>

                    <?= $form->field($modelCompany, 'name')->textInput([
                        'maxlength' => 255,
                        'id' => 'input-process-name',
                        'placeholder' => Yii::t('backend', 'Enter process name'),
                    ]) ?>

                    <?= $form->field($modelCompany, 'description')->textarea([
                        'rows' => 4,
                        'placeholder' => Yii::t('backend', 'Enter a brief description of the process'),
                    ]) ?>

                    <?= $form->field($modelLocation, 'location_url')->textInput([
                        'placeholder' => Yii::t('backend', 'Example: -12.0464,-77.0428'),
                    ]) ?>

                    <div class="text-center mt-4">
                        <?= Html::a(Yii::t('backend', 'Back'), ['initial-configuration/step1'], ['class' => 'btn btn-secondary px-4']) ?>
                        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success px-4']) ?>

                        <?php if ($config->step >= 3): ?>
                            <?= Html::a(Yii::t('backend', 'Next'), ['initial-configuration/step3'], ['class' => 'btn btn-primary px-4']) ?>
                        <?php endif; ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card flex-fill shadow-sm">
            <div class="card-body">
                <div class="text-center mb-3 fw-bold h4"><?= Yii::t('backend', 'Current Hierarchy') ?></div>

                <div class="d-flex flex-column align-items-center">
                    <div class="card text-white bg-primary mb-3" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title"><?= Yii::t('backend', 'Company') ?></h5>
                            <p class="card-text fs-5" id="company-name">
                                <?= $modelCompany->name ? Html::encode($modelCompany->name) : Yii::t('backend', 'N/A') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$companyData = [];
foreach ($companies as $company) {
    $companyData[$company->id] = $company->name;
}
$companyJson = json_encode($companyData);
$js = <<<JS
    const companyMap = $companyJson;

    $('#select-company').on('change', function() {
        const selectedId = $(this).val();
        const selectedName = companyMap[selectedId] || 'N/A';
        $('#company-name').text(selectedName);

        // Limpia el input del nombre para no confundir
        $('#input-process-name').val('');
    });

    $('#input-process-name').on('input', function() {
        const val = $(this).val().trim();
        if (val.length > 0) {
            $('#company-name').text(val);
        } else {
            const selectedId = $('#select-company').val();
            $('#company-name').text(companyMap[selectedId] || 'N/A');
        }
    });
JS;

$this->registerJs($js);
?>