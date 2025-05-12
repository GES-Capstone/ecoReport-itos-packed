<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;
use common\models\User;
use yii\widgets\ActiveForm;
use yii\rbac\Role;

$this->registerCssFile('@web/css/alert.css', ['depends' => [\yii\web\YiiAsset::class]]);
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

        <div class="row mb-4">
            <?php if ($isAdmin): ?>
                <div class="col-md-3">
                    <?= Html::dropDownList('group_user_mining', Yii::$app->request->get('group_user_mining'), $groupOptions, [
                        'class' => 'form-select',
                        'prompt' => 'Filtrar por grupo minero...'
                    ]) ?>
                </div>
            <?php endif; ?>

            <div class="col-md-3">
                <?= Html::dropDownList('role_filter', Yii::$app->request->get('role_filter'), $rolesList, [
                    'class' => 'form-select',
                    'prompt' => 'Filtrar por rol...'
                ]) ?>
            </div>

            <!-- Nuevo dropdown para filtrar por estado -->
            <div class="col-md-2">
                <?= Html::dropDownList('status_filter', Yii::$app->request->get('status_filter', User::STATUS_ACTIVE), $statusOptions, [
                    'class' => 'form-select'
                ]) ?>
            </div>

            <div class="col-md-2">
                <?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary w-100']) ?>
            </div>

            <div class="col-md-2">
                <?= Html::a('Resetear', ['home/edit'], ['class' => 'btn btn-secondary w-100']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
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
                                        <a href="<?= Url::to(['home/update', 'id' => $user->id]) ?>" class="btn btn-primary btn-md px-4">Editar</a>
                                    <?php endif; ?>

                                    <?php if ($user->status == User::STATUS_ACTIVE): ?>
                                        <?= Html::a('Eliminar', ['home/delete', 'id' => $user->id], [
                                            'class' => 'btn btn-danger btn-md px-4',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de que deseas eliminar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php elseif ($user->status == User::STATUS_NOT_ACTIVE): ?>
                                        <?= Html::a('Activar', ['home/activate', 'id' => $user->id], [
                                            'class' => 'btn btn-success btn-md px-4',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de que deseas activar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php elseif ($user->status == User::STATUS_DELETED): ?>
                                        <?= Html::a('Restaurar', ['home/restore', 'id' => $user->id], [
                                            'class' => 'btn btn-success btn-md px-4',
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

    </div>
</div>