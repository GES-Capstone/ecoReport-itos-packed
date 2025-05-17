<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;
use common\models\User;
use yii\widgets\ActiveForm;
use yii\rbac\Role;

$this->registerCssFile('@web/css/alert.css', ['depends' => [\yii\web\YiiAsset::class]]);

// Registramos CSS personalizado para hacerlo totalmente responsive
$customCss = <<<CSS
    /* Estilos responsive para toda la página */
    .card {
        overflow: hidden;
    }
    
    /* Estilos para filtros responsive */
    .filter-item {
        margin-bottom: 10px;
    }
    
    /* En escritorio, tabla normal */
    .user-table {
        width: 100%;
    }
    
    /* Estilos responsive para dispositivos móviles */
    @media (max-width: 767.98px) {
        /* Ocultar tabla en móviles */
        .desktop-only {
            display: none !important;
        }
        
        /* Mostrar tarjetas en móviles */
        .mobile-card {
            display: block !important;
            margin-bottom: 15px;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 0.25rem;
            padding: 10px;
            background-color: #fff;
        }
        
        .mobile-card .user-data {
            margin-bottom: 12px;
        }
        
        .mobile-card .user-label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .mobile-card .user-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .mobile-card .user-buttons .btn {
            flex: 1;
            min-width: 80px;
            margin-bottom: 5px;
        }
    }
    
    /* En móviles, mostrar tarjetas */
    @media (min-width: 768px) {
        .mobile-only {
            display: none !important;
        }
    }
CSS;

$this->registerCss($customCss);
?>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['home/edit'],
]); ?>

<div class="card shadow-sm mb-4">
    <div class="card-header text-center">
        <h5 class="fw-bold mb-0">Usuarios</h5>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-3">
            <?php if ($isAdmin): ?>
                <div class="col-md-3 col-sm-6 col-12 filter-item">
                    <?= Html::dropDownList('group_user_mining', Yii::$app->request->get('group_user_mining'), $groupOptions, [
                        'class' => 'form-select',
                        'prompt' => 'Filtrar por grupo minero...'
                    ]) ?>
                </div>
            <?php endif; ?>

            <div class="col-md-3 col-sm-6 col-12 filter-item">
                <?= Html::dropDownList('role_filter', Yii::$app->request->get('role_filter'), $rolesList, [
                    'class' => 'form-select',
                    'prompt' => 'Filtrar por rol...'
                ]) ?>
            </div>

            <div class="col-md-2 col-sm-6 col-12 filter-item">
                <?= Html::dropDownList('status_filter', Yii::$app->request->get('status_filter', User::STATUS_ACTIVE), $statusOptions, [
                    'class' => 'form-select'
                ]) ?>
            </div>

            <div class="col-md-2 col-sm-6 col-6 filter-item">
                <?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary w-100']) ?>
            </div>

            <div class="col-md-2 col-sm-6 col-6 filter-item">
                <?= Html::a('Resetear', ['home/edit'], ['class' => 'btn btn-secondary w-100']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <!-- Tabla para escritorio -->
        <div class="table-responsive desktop-only">
            <table class="table table-bordered table-hover align-middle text-center user-table">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Grupo Minero</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= Html::encode(($user->userProfile && trim("{$user->userProfile->firstname} {$user->userProfile->lastname}")) ? "{$user->userProfile->firstname} {$user->userProfile->lastname}" : '-') ?></td>
                            <td><?= Html::encode($user->email) ?></td>
                            <td>
                                <?php
                                    $miningGroup = MiningGroup::findOne($user->mining_group_id);
                                    $groupName = $miningGroup->name ?? $miningGroup->ges_name ?? 'Sin grupo';
                                    echo Html::encode($groupName);
                                ?>
                            </td>
                            <td>
                                <?php
                                    $roles = Yii::$app->authManager->getRolesByUser($user->id);
                                    if (!empty($roles)) {
                                        echo implode(', ', ArrayHelper::getColumn($roles, 'name'));
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $statusLabels = [
                                        User::STATUS_NOT_ACTIVE => '<span class="badge bg-warning">No Activo</span>',
                                        User::STATUS_ACTIVE => '<span class="badge bg-success">Activo</span>',
                                        User::STATUS_DELETED => '<span class="badge bg-danger">Eliminado</span>'
                                    ];
                                    echo $statusLabels[$user->status] ?? '';
                                ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if ($user->status == User::STATUS_ACTIVE || $user->status == User::STATUS_NOT_ACTIVE): ?>
                                        <a href="<?= Url::to(['home/update', 'id' => $user->id]) ?>" class="btn btn-primary btn-sm">Editar</a>
                                    <?php endif; ?>

                                    <?php if ($user->status == User::STATUS_ACTIVE): ?>
                                        <?= Html::a('Eliminar', ['home/delete', 'id' => $user->id], [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de que deseas eliminar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php elseif ($user->status == User::STATUS_NOT_ACTIVE): ?>
                                        <?= Html::a('Activar', ['home/activate', 'id' => $user->id], [
                                            'class' => 'btn btn-success btn-sm',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de que deseas activar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php elseif ($user->status == User::STATUS_DELETED): ?>
                                        <?= Html::a('Restaurar', ['home/restore', 'id' => $user->id], [
                                            'class' => 'btn btn-success btn-sm',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de que deseas restaurar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Vista de tarjetas para móvil -->
        <div class="mobile-only">
            <?php foreach ($users as $user): ?>
                <div class="mobile-card shadow-sm">
                    <div class="user-data">
                        <div class="user-label">Nombre:</div>
                        <div><?= Html::encode(($user->userProfile && trim("{$user->userProfile->firstname} {$user->userProfile->lastname}")) ? "{$user->userProfile->firstname} {$user->userProfile->lastname}" : '-') ?></div>
                    </div>
                    
                    <div class="user-data">
                        <div class="user-label">Correo:</div>
                        <div><?= Html::encode($user->email) ?></div>
                    </div>
                    
                    <div class="user-data">
                        <div class="user-label">Grupo Minero:</div>
                        <div>
                            <?php
                                $miningGroup = MiningGroup::findOne($user->mining_group_id);
                                $groupName = $miningGroup->name ?? $miningGroup->ges_name ?? 'Sin grupo';
                                echo Html::encode($groupName);
                            ?>
                        </div>
                    </div>
                    
                    <div class="user-data">
                        <div class="user-label">Roles:</div>
                        <div>
                            <?php
                                $roles = Yii::$app->authManager->getRolesByUser($user->id);
                                if (!empty($roles)) {
                                    echo implode(', ', ArrayHelper::getColumn($roles, 'name'));
                                } else {
                                    echo '-';
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="user-data">
                        <div class="user-label">Estado:</div>
                        <div>
                            <?php
                                $statusLabels = [
                                    User::STATUS_NOT_ACTIVE => '<span class="badge bg-warning">No Activo</span>',
                                    User::STATUS_ACTIVE => '<span class="badge bg-success">Activo</span>',
                                    User::STATUS_DELETED => '<span class="badge bg-danger">Eliminado</span>'
                                ];
                                echo $statusLabels[$user->status] ?? '';
                            ?>
                        </div>
                    </div>
                    
                    <div class="user-buttons">
                        <?php if ($user->status == User::STATUS_ACTIVE || $user->status == User::STATUS_NOT_ACTIVE): ?>
                            <a href="<?= Url::to(['home/update', 'id' => $user->id]) ?>" class="btn btn-primary">Editar</a>
                        <?php endif; ?>

                        <?php if ($user->status == User::STATUS_ACTIVE): ?>
                            <?= Html::a('Eliminar', ['home/delete', 'id' => $user->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => '¿Estás seguro de que deseas eliminar este usuario?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php elseif ($user->status == User::STATUS_NOT_ACTIVE): ?>
                            <?= Html::a('Activar', ['home/activate', 'id' => $user->id], [
                                'class' => 'btn btn-success',
                                'data' => [
                                    'confirm' => '¿Estás seguro de que deseas activar este usuario?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php elseif ($user->status == User::STATUS_DELETED): ?>
                            <?= Html::a('Restaurar', ['home/restore', 'id' => $user->id], [
                                'class' => 'btn btn-success',
                                'data' => [
                                    'confirm' => '¿Estás seguro de que deseas restaurar este usuario?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>