<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $users array */
/* @var $groups array */
/* @var $userId int|null */
/* @var $groupId int|null */

$this->title = 'Assign Mining Group to User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mining-group-assign">

    <div class="panel panel-default">

        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">User</label>
                        <?= Html::dropDownList('user_id', $userId, $users, [
                            'class' => 'form-control',
                            'prompt' => 'Select User',
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Mining Group</label>
                        <?= Html::dropDownList('group_id', $groupId, $groups, [
                            'class' => 'form-control',
                            'prompt' => 'Select Mining Group',
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Assign', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Create New Mining Group', ['create'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['/site/index'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
</div>