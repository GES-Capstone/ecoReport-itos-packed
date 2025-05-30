<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\filekit\widget\Upload;

$this->title = Yii::t('backend', 'Edit Company') . ': ' . Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-update">
    <div class="card shadow-sm mb-4 mx-auto mt-4" style="max-width:850px;">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
        </div>

        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <div class="row mb-3">
                <div class="col-11 col-md-3 order-1 order-md-2 mb-3 mb-md-0">
                    <?= $form->field($model, 'picture')->widget(
                        Upload::class,
                        ['url' => ['company-logo-upload', 'id' => $model->id]]
                    )->label(false) ?>
                </div>
                <div class="col-12 col-md-9 order-2 order-md-1 align-self-start mx-auto">
                    <div class="alert alert-info large mb-2 text-center" data-timeout="1000000">
                        <?= Yii::t('backend', 'Click the + to change the company logo, or the x to remove the image and change it.') ?>
                    </div>
                    <?php if (Yii::$app->user->can('super-administrator')): ?>
                        <?= $form->field($model, 'mining_group_id')
                            ->dropDownList(
                                $groupOptions,
                                [
                                    'prompt' => Yii::t('backend', 'Select Mining Group'),
                                    'class' => 'form-select',
                                ]
                            )
                            ->label(Yii::t('backend', 'Mining Group')) ?>
                    <?php endif; ?>
                </div>
            </div>

            <?= $form->field($model, 'name')->textInput(['maxlength' => 40]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 2, 'maxlength' => 255]) ?>
            <?= $form->field($model, 'commercial_address')->textInput(['maxlength' => 100]) ?>
            <?= $form->field($model, 'operational_address')->textInput(['maxlength' => 100]) ?>
            <?= $form->field($model, 'phone')->textInput(['maxlength' => 50]) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => 100]) ?>
            <?= $form->field($model, 'location_id')
                ->textInput([
                    'placeholder' => '-26.27977828529281, -69.04173385275139'
                ])
                ->label(Yii::t('backend', 'Coordinates (lat, lng)')) ?>

        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton(
                Yii::t('backend', 'Save Changes'),
                ['class' => 'btn btn-success me-2']
            ) ?>
            <?= Html::a(
                Yii::t('backend', 'Cancel'),
                ['index'],
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>