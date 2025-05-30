<?php

use common\models\Company;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;
use common\models\User;
use yii\widgets\ActiveForm;

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
                <div class="col-md-2">
                    <?= Html::dropDownList('group_user_mining', Yii::$app->request->get('group_user_mining'), $groupOptions, [
                        'class' => 'form-select',
                        'prompt' => Yii::t('backend', 'Filter by mining group...'),
                    ]) ?>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('administrator')): ?>
                <div class="col-md-2">
                    <?= Html::dropDownList('companies', Yii::$app->request->get('companies'), $companyOptions, [
                        'class' => 'form-select',
                        'prompt' => Yii::t('backend', 'Filter by company...'),
                    ]) ?>
                </div>
            <?php endif; ?>

            <div class="col-md-2">
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

            <div class="col-md-1">
                <?= Html::submitButton(Yii::t('backend', 'Filter'), ['class' => 'btn btn-primary w-100']) ?>
            </div>

            <div class="col-md-1">
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
                        <?php if (Yii::$app->user->can('super-administrator')): ?><th><?= Yii::t('backend', 'Mining Group') ?></th><?php endif; ?>
                        <th><?= Yii::t('backend', 'Company') ?></th>
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
                            <?php if (Yii::$app->user->can('super-administrator')): ?>
                                <td>
                                    <?php
                                    $miningGroup = MiningGroup::findOne($user->mining_group_id);
                                    $groupName = $miningGroup->name ?? $miningGroup->ges_name ?? 'Sin grupo';
                                    echo Html::encode($groupName);
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php
                                echo $user->isAdministrator
                                    ? '-'
                                    : Html::encode(
                                        Company::findOne($user->company_id)->name
                                            ?? Yii::t('backend', 'Not assigned')
                                    );
                                ?></td>
                            <td>
                                <?php
                                $roles = Yii::$app->authManager->getRolesByUser($user->id);
                                if (!empty($roles)) {
                                    echo implode(', ', ArrayHelper::getColumn($roles, 'name'));
                                } else {
                                    echo '-';
                                }
                                ?></td>
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

                                    <?php if (
                                        $user->status === User::STATUS_ACTIVE
                                        && !$user->isSuperAdministrator
                                        && (Yii::$app->user->can('super-administrator') || !$user->isAdministrator)
                                    ): ?>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-md px-4 delete-user-btn"
                                            data-url="<?= Url::to(['users/delete', 'id' => $user->id]) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirm-delete-modal">
                                            <?= Yii::t('backend', 'Delete') ?>
                                        </button>
                                    <?php elseif ($user->status == User::STATUS_DELETED): ?>
                                        <button
                                            type="button"
                                            class="btn btn-success btn-md px-4 restore-user-btn"
                                            data-url="<?= Url::to(['users/restore', 'id' => $user->id]) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirm-restore-modal">
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

    <?= $this->render('//layouts/_confirmModals') ?>

</div>