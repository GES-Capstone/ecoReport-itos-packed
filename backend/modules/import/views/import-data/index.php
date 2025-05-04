<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */

$this->title = 'Import Data';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="import-data-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="alert alert-info">
        <p><strong>Instructions:</strong></p>
        <ul>
            <li>File must be in Excel format (.xlsx or .xls)</li>
            <li>First row must contain headers</li>
            <li>Column A: Company Name</li>
            <li>Column B: Location (format: latitude,longitude)</li>
        </ul>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Upload Excel File</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'import-form'
                ]
            ]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'excelFile')
                        ->fileInput([
                            'accept' => '.xlsx, .xls',
                            'class' => 'form-control file-input'
                        ])
                        ->hint('Select an Excel file (.xlsx or .xls)') ?>
                </div>
            </div>

            <div class="form-group mt-3">
                <?= Html::submitButton(
                    '<i class="glyphicon glyphicon-upload"></i> Upload and Process', 
                    ['class' => 'btn btn-primary btn-lg']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
.import-form {
    padding: 10px 0;
}
.file-input {
    padding: 10px;
    margin-bottom: 15px;
}
.mt-3 {
    margin-top: 20px;
}
.panel {
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.panel-heading {
    background-color: #f8f8f8;
    border-bottom: 1px solid #eee;
}
.alert-info {
    background-color: #e8f4fd;
    border-color: #bce8f1;
}
</style>