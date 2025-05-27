<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Area;
?>

<div class="container mt-5">
   <p class="text-center fw-bold display-4 my-4"><?= Yii::t('backend', 'Select an area to edit or create a new one') ?></p>

   <div class="d-flex flex-column flex-md-row gap-4">
       
       <div class="card flex-fill shadow-sm">
           <div class="card-body">
                <div class="mb-4 text-center ">
                    <?= Html::beginForm(['initial-configuration/step4'], 'get') ?>
                        <?= Html::dropDownList(
                            'area_id',
                            Yii::$app->request->get('area_id'),
                            ArrayHelper::map($areas, 'id', 'name'),
                            [
                                'prompt' => Yii::t('backend', 'Select an area...'),
                                'class' => 'form-select w-75 d-inline-block'
                            ]
                        ) ?>
                        <?= Html::submitButton(Yii::t('backend', 'Edit'), ['class' => 'btn btn-primary ms-2']) ?>
                        <?= Html::a(Yii::t('backend', 'Create New'), ['initial-configuration/step4'], ['class' => 'btn btn-success ms-2']) ?>
                    <?= Html::endForm() ?>
                </div>

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($modelArea, 'mining_process_id')->dropDownList(
                    ArrayHelper::map($processes, 'id', function($model) {
                        return ($model->company->name ?? Yii::t('backend', 'No company')) . ' - ' . $model->name;
                    }),
                    [
                        'prompt' => Yii::t('backend', 'Select a mining process...'),
                        'id' => 'select-mining-process',
                    ]
                ) ?>


                <?= $form->field($modelArea, 'name')->textInput([
                    'maxlength' => 255,
                    'placeholder' => Yii::t('backend', 'Enter area name'),
                    'id' => 'input-area-name',
                ]) ?>

                <?= $form->field($modelArea, 'description')->textarea([
                    'rows' => 4,
                    'placeholder' => Yii::t('backend', 'Enter a brief description of the area'),
                ]) ?>

                <?= $form->field($modelLocation, 'location_url')->textInput([
                    'placeholder' => Yii::t('backend', 'Example: -12.0464,-77.0428'),
                ]) ?>

                <div class="text-center mt-4">
                    <?= Html::a(Yii::t('backend', 'Back'), ['initial-configuration/step3'], ['class' => 'btn btn-secondary px-4']) ?>
                    <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success px-4']) ?>
                    <?php if ($config->step >= 5): ?>
                        <?= Html::a(Yii::t('backend', 'Next'), ['initial-configuration/step5'], ['class' => 'btn btn-primary px-4']) ?>
                    <?php endif; ?>
                </div>

                <?php ActiveForm::end(); ?>
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
                               <?= $modelArea->miningProcess && $modelArea->miningProcess->company ? Html::encode($modelArea->miningProcess->company->name) : Yii::t('backend', 'N/A') ?>
                           </p>
                       </div>
                   </div>

                   <div style="font-size: 2.5rem; line-height: 1; color: #0d6efd;">&#8595;</div>

                   <div class="card text-white bg-info mb-3" style="width: 18rem;">
                       <div class="card-body">
                           <h5 class="card-title"><?= Yii::t('backend', 'Mining Process') ?></h5>
                           <p class="card-text fs-5" id="process-name">
                               <?= $modelArea->miningProcess ? Html::encode($modelArea->miningProcess->name) : Yii::t('backend', 'N/A') ?>
                           </p>
                       </div>
                   </div>

                   <div style="font-size: 2.5rem; line-height: 1; color: #0dcaf0;">&#8595;</div>

                   <div class="card text-white bg-warning mb-3" style="width: 18rem;">
                       <div class="card-body">
                           <h5 class="card-title"><?= Yii::t('backend', 'Area') ?></h5>
                           <p class="card-text fs-5" id="area-name">
                               <?= $modelArea->name ?: Yii::t('backend', 'N/A') ?>
                           </p>
                       </div>
                   </div>

               </div>
           </div>
       </div>

   </div>
</div>

<?php
$processData = [];
foreach ($processes as $process) {
    $processData[$process->id] = [
        'processName' => $process->name,
        'companyName' => $process->company ? $process->company->name : Yii::t('backend', 'No company'),
    ];
}
$processJson = json_encode($processData);
$js = <<<JS
    const processMap = $processJson;

    $('#select-mining-process').on('change', function() {
        const selectedId = $(this).val();
        if (selectedId && processMap[selectedId]) {
            $('#process-name').text(processMap[selectedId].processName);
            $('#company-name').text(processMap[selectedId].companyName);
        } else {
            $('#process-name').text('N/A');
            $('#company-name').text('N/A');
        }
    });

    $('#input-area-name').on('input', function() {
        const val = $(this).val();
        $('#area-name').text(val ? val : 'N/A');
    });
JS;

$this->registerJs($js);
?>
