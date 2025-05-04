<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stats array */
/* @var $fileName string */

$this->title = 'Import Results';
$this->params['breadcrumbs'][] = ['label' => 'Import Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$totalProcessed = $stats['companies_created'] + $stats['companies_updated'];
$hasErrors = count($stats['errors']) > 0;
?>

<div class="import-data-result">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="alert alert-<?= $hasErrors ? 'warning' : 'success' ?>">
        <h4><i class="glyphicon glyphicon-<?= $hasErrors ? 'warning-sign' : 'ok-circle' ?>"></i> File: <?= Html::encode($fileName) ?></h4>
        <p>The import process has <?= $hasErrors ? 'completed with some errors' : 'completed successfully' ?>.</p>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-stats"></i> Summary</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th class="col-md-4">Companies created:</th>
                                <td class="col-md-2"><?= $stats['companies_created'] ?></td>
                            </tr>
                            <tr>
                                <th>Companies updated:</th>
                                <td><?= $stats['companies_updated'] ?></td>
                            </tr>
                            <tr>
                                <th>Locations created:</th>
                                <td><?= $stats['locations_created'] ?></td>
                            </tr>
                            <tr class="success">
                                <th>Total processed:</th>
                                <td><strong><?= $totalProcessed ?></strong></td>
                            </tr>
                            <tr class="<?= $hasErrors ? 'danger' : 'success' ?>">
                                <th>Errors found:</th>
                                <td><strong><?= count($stats['errors']) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="stats-summary">
                        <div class="text-center">
                            <div class="stats-circle <?= $hasErrors ? 'partial' : 'complete' ?>">
                                <span class="stats-number"><?= $totalProcessed ?></span>
                                <span class="stats-label">Items</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($hasErrors): ?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-exclamation-sign"></i> Errors</h3>
        </div>
        <div class="panel-body">
            <div class="alert alert-danger">
                <p><strong>The following errors were encountered during import:</strong></p>
            </div>
            <ul class="error-list">
                <?php foreach ($stats['errors'] as $error): ?>
                    <li><?= Html::encode($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="form-group buttons-container">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Back', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Import Another File', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<style>
.import-data-result {
    margin-bottom: 30px;
}
.buttons-container {
    margin-top: 20px;
    padding: 15px 0;
    border-top: 1px solid #eee;
}
.panel {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    margin-bottom: 25px;
}
.panel-heading {
    background-color: #f8f8f8;
}
.error-list {
    margin-top: 10px;
    padding-left: 20px;
}
.error-list li {
    padding: 5px 0;
    color: #a94442;
}
.stats-summary {
    padding: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}
.stats-circle {
    border-radius: 50%;
    width: 150px;
    height: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
}
.stats-circle.complete {
    background-color: #5cb85c;
}
.stats-circle.partial {
    background-color: #f0ad4e;
}
.stats-number {
    font-size: 48px;
    font-weight: bold;
}
.stats-label {
    font-size: 16px;
    text-transform: uppercase;
}
</style>