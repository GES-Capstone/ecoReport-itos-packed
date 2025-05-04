<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MiningGroup */
/* @var $location common\models\Location */

$this->title = 'Create Mining Group';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mining-group-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if (Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-<?= Yii::$app->session->hasFlash('error') ? 'danger' : 'success' ?>">
            <i class="glyphicon glyphicon-<?= Yii::$app->session->hasFlash('error') ? 'exclamation-sign' : 'ok-circle' ?>"></i>
            <?= Yii::$app->session->hasFlash('error') ?: Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <div class="mining-group-form">
        <?php $form = ActiveForm::begin(['id' => 'mining-group-form']); ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="glyphicon glyphicon-briefcase"></i> Mining Group Information</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'name')->textInput([
                            'maxlength' => true,
                            'placeholder' => 'Enter mining group name'
                        ]) ?>
                        
                        <?= $form->field($model, 'ges_name')->textInput([
                            'maxlength' => true,
                            'placeholder' => 'Enter GES name'
                        ]) ?>
                        
                        <?= $form->field($model, 'description')->textarea([
                            'rows' => 2,
                            'placeholder' => 'Brief description'
                        ]) ?>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'email')->textInput([
                                    'maxlength' => true,
                                    'type' => 'email',
                                    'placeholder' => 'contact@example.com'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'phone')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => '+1 (123) 456-7890'
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'commercial_address')->textarea([
                                    'rows' => 2,
                                    'placeholder' => 'Commercial address'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'operational_address')->textarea([
                                    'rows' => 2,
                                    'placeholder' => 'Operational address'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="divider">
                
                <div class="row location-section">
                    <div class="col-md-12">
                        <label><i class="glyphicon glyphicon-map-marker"></i> Geographic Coordinates</label>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($location, 'latitude', [
                            'template' => '{label}{input}{error}',
                            'options' => ['class' => 'form-group compact-form-group']
                        ])->textInput([
                            'maxlength' => true,
                            'placeholder' => 'e.g. 37.7749',
                            'type' => 'number',
                            'step' => 'any'
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($location, 'longitude', [
                            'template' => '{label}{input}{error}',
                            'options' => ['class' => 'form-group compact-form-group']
                        ])->textInput([
                            'maxlength' => true,
                            'placeholder' => 'e.g. -122.4194',
                            'type' => 'number',
                            'step' => 'any'
                        ]) ?>
                    </div>
                </div>
                
                <div class="form-group buttons-container">
                    <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Save', [
                        'class' => 'btn btn-success',
                        'id' => 'save-group-button'
                    ]) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-remove"></i> Cancel', 
                        ['/site/index'], 
                        ['class' => 'btn btn-default']
                    ) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
.mining-group-create {
    margin-bottom: 20px;
}
.panel {
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.panel-heading {
    background-color: #f8f8f8;
    border-bottom: 1px solid #eee;
    padding: 10px 15px;
}
.panel-body {
    padding: 15px;
}
.form-group {
    margin-bottom: 10px;
}
.compact-form-group {
    margin-bottom: 5px;
}
.form-control {
    padding: 6px 10px;
}
.buttons-container {
    margin-top: 15px;
    padding-top: 10px;
}
.divider {
    margin: 10px 0;
    border-top: 1px solid #eee;
}
.location-section {
    margin-top: 5px;
}
.alert {
    padding: 10px;
    margin-bottom: 15px;
}
/* Compact labels */
.control-label {
    font-weight: 600;
    font-size: 13px;
}
/* Reduced padding on textareas */
textarea.form-control {
    padding: 5px 8px;
}
</style>

<script>
$(document).ready(function() {
    $('#mining-group-form').on('beforeSubmit', function() {
        $('#save-group-button').prop('disabled', true).html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Saving...');
        return true;
    });
});
</script>