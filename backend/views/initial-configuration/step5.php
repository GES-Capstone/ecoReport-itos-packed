<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Area;

?>

<div class="container mt-5">
    <p class="text-center fw-bold display-4 my-4"><?= Yii::t('backend', 'Select a fleet to edit or create a new fleet') ?></p>

    <div class="mb-4 text-center">
        <?= Html::beginForm(['initial-configuration/step5'], 'get') ?>
            <?= Html::dropDownList(
                'fleet_id',
                Yii::$app->request->get('fleet_id'),
                ArrayHelper::map($fleetList, 'id', fn($fleet) => $fleet->name . ' (' . ($fleet->area->name ?? Yii::t('backend', 'No area')) . ')'),
                ['prompt' => Yii::t('backend', 'Select a fleet...'), 'class' => 'form-select w-50 d-inline-block']
            ) ?>
            <?= Html::submitButton(Yii::t('backend', 'Edit'), ['class' => 'btn btn-primary ms-2']) ?>
            <?= Html::a(Yii::t('backend', 'Create New'), ['initial-configuration/step5'], ['class' => 'btn btn-success ms-2']) ?>
        <?= Html::endForm() ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4 h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-bold h4 text-center mb-4"><?= Yii::t('backend', 'Fleet Details') ?></div>
                        <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($modelFleet, 'area_id')->dropDownList(
                            ArrayHelper::map(
                                $areaList,
                                'id',
                                function ($area) {
                                    $process = $area->miningProcess;
                                    $company = $process?->company->name ?? Yii::t('backend', 'No company');
                                    $processName = $process->name ?? Yii::t('backend', 'No process');
                                    return "$company - $processName - $area->name";
                                }
                            ),
                            ['prompt' => Yii::t('backend', 'Select an area...')],
                            ['id' => 'fleet-area_id']
                        ) ?>

                        <?= $form->field($modelFleet, 'name')->textInput([
                            'maxlength' => 255,
                            'placeholder' => Yii::t('backend', 'Enter fleet name'),
                            'id' => 'fleet-name',
                        ]) ?>

                        <?= $form->field($modelFleet, 'description')->textarea([
                            'rows' => 4,
                            'placeholder' => Yii::t('backend', 'Enter a brief description of the fleet'),
                        ]) ?>

                        <?= $form->field($modelLocation, 'location_url')->textInput([
                            'placeholder' => Yii::t('backend', 'Example: -12.0464,-77.0428'),
                        ]) ?>
                    </div>

                    <div class="text-center mt-4">
                        <?= Html::a(Yii::t('backend', 'Back'), ['initial-configuration/step4'], ['class' => 'btn btn-secondary px-4']) ?>
                        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success px-4']) ?>
                        <?php if ($config->step >= 6): ?>
                            <?= Html::a(Yii::t('backend', 'Next'), ['initial-configuration/step6'], ['class' => 'btn btn-primary px-4']) ?>
                        <?php endif; ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- Jerarquía visual -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="fw-bold h4 mb-4"><?= Yii::t('backend', 'Current Hierarchy') ?></div>

                    <div class="d-flex flex-column align-items-center">
                        <div class="card text-white bg-primary mb-3" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?= Yii::t('backend', 'Company') ?></h5>
                                <p class="card-text fs-5" id="card-company-name">
                                    <?= $modelFleet->area->miningProcess->company->name ?? Yii::t('backend', 'N/A') ?>
                                </p>
                            </div>
                        </div>

                        <div style="font-size: 2.5rem;">&#8595;</div>

                        <div class="card text-white bg-info mb-3" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?= Yii::t('backend', 'Mining Process') ?></h5>
                                <p class="card-text fs-5" id="card-process-name">
                                    <?= $modelFleet->area->miningProcess->name ?? Yii::t('backend', 'N/A') ?>
                                </p>
                            </div>
                        </div>

                        <div style="font-size: 2.5rem;">&#8595;</div>

                        <div class="card text-white bg-warning mb-3" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?= Yii::t('backend', 'Area') ?></h5>
                                <p class="card-text fs-5" id="card-area-name">
                                    <?= $modelFleet->area->name ?? Yii::t('backend', 'N/A') ?>
                                </p>
                            </div>
                        </div>

                        <div style="font-size: 2.5rem;">&#8595;</div>

                        <div class="card text-white bg-dark mb-3" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?= Yii::t('backend', 'Fleet') ?></h5>
                                <p class="card-text fs-5" id="card-fleet-name">
                                    <?= Html::encode($modelFleet->name ?? Yii::t('backend', 'N/A')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php
$areaData = [];
foreach ($areaList as $area) {
    $areaData[$area->id] = [
        'areaName' => $area->name,
        'processName' => $area->miningProcess->name ?? Yii::t('backend', 'N/A'),
        'companyName' => $area->miningProcess->company->name ?? Yii::t('backend', 'N/A'),
    ];
}
$areaJson = json_encode($areaData);
$js = <<<JS
const areaMap = $areaJson;

$('#fleet-area_id').on('change', function() {
    const id = $(this).val();
    if (areaMap[id]) {
        $('#card-area-name').text(areaMap[id].areaName);
        $('#card-process-name').text(areaMap[id].processName);
        $('#card-company-name').text(areaMap[id].companyName);
    } else {
        $('#card-area-name, #card-process-name, #card-company-name').text('N/A');
    }
});

// Actualizar nombre de flota en tarjeta
$('#fleet-name').on('input', function() {
    const val = $(this).val().trim();
    $('#card-fleet-name').text(val !== '' ? val : 'N/A');
});
JS;
$this->registerJs($js);
?>
