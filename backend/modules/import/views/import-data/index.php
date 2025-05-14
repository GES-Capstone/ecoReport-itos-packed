<?php
/* @var $this yii\web\View */
/* @var $stats array */
/* @var $fileName string */
/* @var $type string */

use yii\helpers\Html;

$this->title = 'Resultados de Importación';
$this->params['breadcrumbs'][] = ['label' => 'Importación de Datos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Mapeo de nombres amigables para los tipos
$typeNames = [
    'company' => 'Compañías',
    'fleet' => 'Flotas',
    'machinery' => 'Maquinaria',
    'component' => 'Componentes'
];

$typeName = isset($typeNames[$type]) ? $typeNames[$type] : ucfirst($type);
?>

<div class="import-result">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-success">
        <p>
            El archivo <strong><?= Html::encode($fileName) ?></strong>
            ha sido procesado correctamente.
        </p>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resumen de importación de <?= Html::encode($typeName) ?></h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                <?php if (isset($stats['companies_created'])): ?>
                    <tr>
                        <th>Compañías creadas:</th>
                        <td><?= $stats['companies_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Compañías actualizadas:</th>
                        <td><?= $stats['companies_updated'] ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($stats['fleets_created'])): ?>
                    <tr>
                        <th>Flotas creadas:</th>
                        <td><?= $stats['fleets_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Flotas actualizadas:</th>
                        <td><?= $stats['fleets_updated'] ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($stats['machinery_created'])): ?>
                    <tr>
                        <th>Maquinaria creada:</th>
                        <td><?= $stats['machinery_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Maquinaria actualizada:</th>
                        <td><?= $stats['machinery_updated'] ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($stats['components_created'])): ?>
                    <tr>
                        <th>Componentes creados:</th>
                        <td><?= $stats['components_created'] ?></td>
                    </tr>
                    <tr>
                        <th>Componentes actualizados:</th>
                        <td><?= $stats['components_updated'] ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if (!empty($stats['errors'])): ?>
                <div class="alert alert-warning mt-3">
                    <h4>Errores encontrados:</h4>
                    <ul>
                        <?php foreach ($stats['errors'] as $error): ?>
                            <li><?= Html::encode($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <?= Html::a('Volver', ['index', 'type' => $type], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>