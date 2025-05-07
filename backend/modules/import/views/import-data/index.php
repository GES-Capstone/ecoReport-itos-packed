<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */
$this->title = 'Import Data';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .import-container {
        max-width: 850px;
        margin: 0 auto;
        font-family: 'Segoe UI', 'Roboto', sans-serif;
    }
    .page-header {
        margin-bottom: 2rem;
    }
    .page-title {
        color: #006d77;
        font-weight: 600;
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }
    .header-underline {
        width: 60px;
        height: 4px;
        background-color: #006d77;
        margin-bottom: 1.5rem;
    }
    .teal-panel {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .panel-header {
        background-color: #006d77;
        color: white;
        padding: 1.2rem 1.5rem;
        font-size: 1.2rem;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .panel-header i {
        margin-right: 10px;
    }
    .panel-body {
        padding: 1.8rem;
    }
    .info-box {
        background-color: rgba(0,109,119,0.07);
        border-left: 4px solid #006d77;
        padding: 1.5rem;
        border-radius: 0 8px 8px 0;
    }
    .info-title {
        color: #006d77;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    .info-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .info-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.8rem;
        font-size: 0.95rem;
    }
    .info-list li:before {
        content: "•";
        color: #006d77;
        position: absolute;
        left: 0;
        top: 0;
        font-size: 1.2rem;
        font-weight: bold;
    }
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
        transition: all 0.2s ease;
    }
    .upload-icon {
        display: block;
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        color: #006d77;
    }
    .upload-text {
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .button-container {
        text-align: center;
    }
    .btn-teal {
        background-color: #006d77;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-teal:hover {
        background-color: #005561;
        box-shadow: 0 4px 12px rgba(0,109,119,0.3);
    }
    .file-hint {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
</style>

<div class="import-container">
    <div class="page-header">
        <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
        <div class="header-underline"></div>
    </div>
    
    <div class="teal-panel">
        <div class="panel-header">
            Important Information
        </div>
        <div class="panel-body">
            <div class="info-box">
                <div class="info-title">File Import Instructions</div>
                <ul class="info-list">
                    <li>File must be in Excel format (.xlsx)</li>
                    <li>First row must contain column headers</li>
                    <li>Column A: Company Name</li>
                    <li>Column B: Location (format: latitude,longitude)</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="teal-panel">
        <div class="panel-header">
            Upload Excel File
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="upload-area">
                <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="upload-text">Select an Excel file to import</div>
                
                <?= $form->field($model, 'excelFile')->fileInput([
                    'accept' => '.xlsx',
                    'class' => 'form-control'
                ])->label(false) ?>
                
                <div class="file-hint">Supported format: .xlsx</div>
            </div>
            
            <div class="button-container">
                <?= Html::submitButton('Upload & Process', [
                    'class' => 'btn-teal'
                ]) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>