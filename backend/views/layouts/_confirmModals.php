<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** 
 * @var string $deleteUrlPattern
 * @var string $restoreUrlPattern
 */
?>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirm-delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('backend', 'Confirm Deletion') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= Yii::t('backend', 'Are you sure you want to delete this item?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= Yii::t('backend', 'Cancel') ?>
                </button>
                <a href="#" id="confirm-delete-btn" class="btn btn-danger">
                    <?= Yii::t('backend', 'Delete') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="confirm-restore-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('backend', 'Confirm Restoration') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= Yii::t('backend', 'Are you sure you want to restore this item?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= Yii::t('backend', 'Cancel') ?>
                </button>
                <a href="#" id="confirm-restore-btn" class="btn btn-success">
                    <?= Yii::t('backend', 'Restore') ?>
                </a>
            </div>
        </div>
    </div>
</div>