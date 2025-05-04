<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stats array */
/* @var $fileName string */

$this->title = 'Resultados de Importación';
$this->params['breadcrumbs'][] = ['label' => 'Importar Datos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$totalProcessed = $stats['companies_created'] + $stats['companies_updated'];
$hasErrors = count($stats['errors']) > 0;
?>

<div class="import-data-result">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="alert alert-<?= $hasErrors ? 'warning' : 'success' ?>">
        <h4>Archivo: <?= Html::encode($fileName) ?></h4>
        <p>El proceso de importación ha finalizado <?= $hasErrors ? 'con algunos errores' : 'exitosamente' ?>.</p>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Resumen</h3>
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Compañías creadas:</th>
                        <td><?= $stats['companies_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Compañías actualizadas:</th>
                        <td><?= $stats['companies_updated'] ?></td>
                    </tr>
                    <tr>
                        <th>Ubicaciones creadas:</th>
                        <td><?= $stats['locations_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Total procesado:</th>
                        <td><?= $totalProcessed ?></td>
                    </tr>
                    <tr>
                        <th>Errores encontrados:</th>
                        <td><?= count($stats['errors']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($hasErrors): ?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Errores</h3>
        </div>
        <div class="panel-body">
            <ul>
                <?php foreach ($stats['errors'] as $error): ?>
                    <li><?= Html::encode($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="form-group">
        <?= Html::a('Regresar', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('Realizar otra importación', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>