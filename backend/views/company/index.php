<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('backend', 'Company Management');
?>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><?= Html::encode($this->title) ?></h5>

        <button class="btn btn-outline-secondary d-block d-md-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtersCollapse"
            aria-expanded="false"
            aria-controls="filtersCollapse">
            <?= Yii::t('backend', 'Filters') ?>
        </button>

        <div class="d-flex">
            <?= Html::a(
                Yii::t('backend', 'Create Company'),
                ['company/create'],
                ['class' => 'btn btn-success ms-2']
            ) ?>
        </div>
    </div>

    <div class="collapse d-md-block" id="filtersCollapse">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['company/index'],
            'options' => ['class' => 'card-body']
        ]); ?>
        <div class="row gx-3 gy-2 align-items-end">
            <?php if (Yii::$app->user->can('super-administrator')): ?>
                <div class="col-12 col-md-2 w-25">
                    <?= Html::dropDownList(
                        'mining_group_filter',
                        $currentFilters['mining_group_filter'],
                        $groupOptions,
                        [
                            'class' => 'form-select',
                            'prompt' => Yii::t('backend', 'Filter by group...')
                        ]
                    ) ?>
                </div>
            <?php endif; ?>

            <div class="col-12 col-sm-6 col-md-2">
                <?= Html::submitButton(
                    Yii::t('backend', 'Filter'),
                    ['class' => 'btn btn-primary w-100']
                ) ?>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <?= Html::a(
                    Yii::t('backend', 'Clear'),
                    ['company/index'],
                    ['class' => 'btn btn-secondary w-100']
                ) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered table-hover align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    <?php if (Yii::$app->user->can('super-administrator')): ?><th><?= Yii::t('backend', 'Mining Group') ?></th><?php endif; ?>
                    <th><?= Yii::t('backend', 'Name') ?></th>
                    <th><?= Yii::t('backend', 'Commercial Address') ?></th>
                    <th><?= Yii::t('backend', 'Operational Address') ?></th>
                    <th><?= Yii::t('backend', 'Phone') ?></th>
                    <th><?= Yii::t('backend', 'Email') ?></th>
                    <th><?= Yii::t('backend', 'Location') ?></th>
                    <th><?= Yii::t('backend', 'Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <?php if (Yii::$app->user->can('super-administrator')): ?>
                            <td>
                                <?= Html::encode(
                                    $company->miningGroup
                                        ? $company->miningGroup->name
                                        : Yii::t('backend', '—')
                                ) ?>
                            </td>
                        <?php endif; ?>
                        <td><?= Html::encode($company->name) ?></td>
                        <td><?= Html::encode($company->commercial_address ?: '—') ?></td>
                        <td><?= Html::encode($company->operational_address ?: '—') ?></td>
                        <td><?= Html::encode($company->phone ?: '—') ?></td>
                        <td><?= Html::encode($company->email ?: '—') ?></td>
                        <td><?= Html::encode($company->location?->name ?: '—') ?></td>
                        <td class="d-flex justify-content-center gap-1">
                            <?= Html::a('Edit', ['company/update', 'id' => $company->id], ['class' => 'btn btn-primary btn-sm']) ?>
                            <?php if (Yii::$app->user->can('super-administrator')): ?>
                                <button type="button"
                                    class="btn btn-danger btn-sm delete-company-btn"
                                    data-url="<?= Url::to(['company/delete', 'id' => $company->id]) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirm-delete-modal">
                                    <?= Yii::t('backend', 'Delete') ?>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-block d-md-none p-3">
        <div class="row g-3">
            <?php foreach ($companies as $company): ?>
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-1"><?= Html::encode($company->name) ?></h6>
                            <p class="mb-1 small">
                                <i class="bi bi-geo-alt-fill"></i>
                                <?= Html::encode($company->commercial_address ?: '—') ?>
                            </p>
                            <p class="mb-1 small">
                                <i class="bi bi-telephone-fill"></i>
                                <?= Html::encode($company->phone ?: '—') ?>
                            </p>
                            <p class="mb-1 small">
                                <i class="bi bi-envelope-fill"></i>
                                <?= Html::encode($company->email ?: '—') ?>
                            </p>
                            <p class="mb-1 small text-muted">
                                <?= Yii::t('backend', 'Location') ?>: <?= Html::encode($company->location?->name ?: '—') ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <?= Html::a('Edit', ['company/update', 'id' => $company->id], ['class' => 'btn btn-primary btn-sm flex-fill me-1']) ?>
                            <?php if (Yii::$app->user->can('super-administrator')): ?>
                                <button type="button"
                                    class="btn btn-danger btn-sm flex-fill ms-1 delete-company-btn"
                                    data-url="<?= Url::to(['company/delete', 'id' => $company->id]) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirm-delete-modal">
                                    <?= Yii::t('backend', 'Delete') ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?= $this->render('//layouts/_confirmModals') ?>
</div>