<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\import\models\ExcelUploadForm */

$this->title = 'Import Mining Groups and Companies';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="importacion-minera-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>The Excel file must contain the following columns:</p>
    <ul>
        <li>Column A: Name of the mining group</li>
        <li>Column B: Name of the company</li>
    </ul>

    <div class="panel panel-default">
        <div class="panel-heading">Select file</div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'excelFile')->fileInput(['accept' => '.xlsx, .xls'])->label('Excel File') ?>

            <div class="form-group">
                <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>