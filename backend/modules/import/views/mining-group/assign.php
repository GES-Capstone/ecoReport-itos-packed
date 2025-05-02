<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $users array */
/* @var $groups array */
/* @var $userId int|null */
/* @var $groupId int|null */

$this->title = 'Asignar Grupo Minero a Usuario';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mining-group-assign">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Asignar Grupo Minero</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Usuario</label>
                        <?= Html::dropDownList('user_id', $userId, $users, [
                            'class' => 'form-control',
                            'prompt' => 'Seleccionar Usuario',
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Grupo Minero</label>
                        <?= Html::dropDownList('group_id', $groupId, $groups, [
                            'class' => 'form-control',
                            'prompt' => 'Seleccionar Grupo Minero',
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Asignar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Crear Nuevo Grupo Minero', ['create'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancelar', ['/site/index'], ['class' => 'btn btn-default']) ?>
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