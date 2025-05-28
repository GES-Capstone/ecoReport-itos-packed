<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;
use common\models\User;
use yii\widgets\ActiveForm;
use yii\rbac\Role;

$this->registerCssFile('@web/css/alert.css', ['depends' => [\yii\web\YiiAsset::class]]);

$this->title = Yii::t('backend', 'User Management');
?>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['users/edit'],
]); ?>

<div class="card shadow-sm mb-4">
    <div class="card-header text-center">
        <h5 class="fw-bold mb-0"><?= Yii::t('backend', 'Users') ?></h5>
    </div>
    <div class="card-body">

        <div class="row mb-4">
            <?php if (Yii::$app->user->can('super-administrator')): ?>
                <div class="col-md-3">
                    <?= Html::dropDownList('group_user_mining', Yii::$app->request->get('group_user_mining'), $groupOptions, [
                        'class' => 'form-select',
                        'prompt' => Yii::t('backend', 'Filter by mining group...'),
                    ]) ?>
                </div>
            <?php endif; ?>

            <div class="col-md-3">
                <?= Html::dropDownList('role_filter', Yii::$app->request->get('role_filter'), $rolesList, [
                    'class' => 'form-select',
                    'prompt' => Yii::t('backend', 'Filter by role...'),
                ]) ?>
            </div>

            <div class="col-md-2">
                <?= Html::dropDownList('status_filter', Yii::$app->request->get('status_filter', User::STATUS_ACTIVE), $statusOptions, [
                    'class' => 'form-select'
                ]) ?>
            </div>

            <div class="col-md-2">
                <?= Html::submitButton(Yii::t('backend', 'Filter'), ['class' => 'btn btn-primary w-100']) ?>
            </div>

            <div class="col-md-2">
                <?= Html::a(Yii::t('backend', 'Clear'), ['users/edit'], ['class' => 'btn btn-secondary w-100']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th><?= Yii::t('backend', 'Name') ?></th>
                        <th><?= Yii::t('backend', 'Email') ?></th>
                        <th><?= Yii::t('backend', 'Mining Group') ?></th>
                        <th><?= Yii::t('backend', 'Roles') ?></th>
                        <th><?= Yii::t('backend', 'Status') ?></th>
                        <th><?= Yii::t('backend', 'Actions') ?></th>
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
                                    User::STATUS_NOT_ACTIVE => '<span class="badge bg-warning">' . Yii::t('backend', 'Inactive') . '</span>',
                                    User::STATUS_ACTIVE     => '<span class="badge bg-success">' . Yii::t('backend', 'Active') . '</span>',
                                    User::STATUS_DELETED    => '<span class="badge bg-danger">' . Yii::t('backend', 'Deleted') . '</span>',
                                ];
                                echo $statusLabels[$user->status] ?? '';
                                ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if ($user->status == User::STATUS_ACTIVE || $user->status == User::STATUS_NOT_ACTIVE): ?>
                                        <a href="<?= Url::to(['users/update', 'id' => $user->id]) ?>"
                                            class="btn btn-primary btn-md px-4">
                                            <?= Yii::t('backend', 'Edit') ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($user->status == User::STATUS_ACTIVE): ?>
                                        <button type="button" class="btn btn-danger btn-md px-4 delete-btn"
                                            data-id="<?= $user->id ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <?= Yii::t('backend', 'Delete') ?>
                                        </button>
                                    <?php elseif ($user->status == User::STATUS_NOT_ACTIVE): ?>
                                        <?= Html::a(Yii::t('backend', 'Activate'), ['users/activate', 'id' => $user->id], [
                                            'class' => 'btn btn-success btn-md px-4',
                                            'data' => [
                                                'confirm' => Yii::t('backend', 'Are you sure that you want to activate this user?'),
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php elseif ($user->status == User::STATUS_DELETED): ?>
                                        <button type="button" class="btn btn-success btn-md px-4 restore-btn"
                                            data-id="<?= $user->id ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#restoreModal">
                                            <?= Yii::t('backend', 'Restore') ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><?= Yii::t('backend', 'Confirm Deletion') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= Yii::t('backend', 'Are you sure you want to delete this user?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= Yii::t('backend', 'Cancel') ?></button>
                    <button type="button" class="btn btn-danger" id="confirmDelete"><?= Yii::t('backend', 'Delete') ?></button>
                </div>
            </div>
        </div>
    </div>


    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel"><?= Yii::t('backend', 'Confirm Restoration') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= Yii::t('backend', 'Are you sure you want to restore this user?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= Yii::t('backend', 'Cancel') ?></button>
                    <button type="button" class="btn btn-success" id="confirmRestore"><?= Yii::t('backend', 'Restore') ?></button>
                </div>
            </div>
        </div>
    </div>

</div>