<?php
use yii\helpers\Html;
?>

<div class="importort_result">
    <h1>Resultado de la Importación</h1>

    <div class="alert alert-success">
        <p>El proceso de importación ha finalizado.</p>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Estadísticas</div>
        <div class="panel-body">
            <ul>
                <li><strong>Grupos Mineros creados:</strong> <?= $stats['groups_created'] ?></li>
                <li><strong>Grupos Mineros existentes:</strong> <?= $stats['groups_existing'] ?></li>
                <li><strong>Compañías creadas:</strong> <?= $stats['companies_created'] ?></li>
                <li><strong>Compañías actualizadas:</strong> <?= $stats['companies_updated'] ?></li>
            </ul>
        </div>
    </div>

    <?php if (!empty($stats['errors'])): ?>
        <div class="panel panel-danger">
            <div class="panel-heading">Errores</div>
            <div class="panel-body">
                <ul>
                    <?php foreach ($stats['errores'] as $error): ?>
                        <li><?= Html::encode($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::a('Volver', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>