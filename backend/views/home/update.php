<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MiningGroup;

$this->title = 'Editar Usuario: ' . $model->username;
$this->registerCssFile('@web/css/user-create.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="position-absolute top-0 start-0 m-3">
            <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>" class="btn btn-link p-0" style="text-decoration: none;">
                <i class="fa fa-arrow-left fa-lg"></i>
            </a>
        </div>
        <div class="text-center mb-4">
            <h3 class="fw-bold"><?= Html::encode($this->title) ?></h3>
        </div>

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="fw-bold">Datos del Usuario</h5>
                    <div class="mb-3">
                        <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Nombre de Usuario']) ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Correo Electrónico']) ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Nueva Contraseña'])->hint('Deja en blanco si no quieres cambiarla.') ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->field($model, 'status')->dropDownList([2 => 'Activo', 1 => 'Inactivo', 3 => 'Eliminado'], ['prompt' => 'Seleccione el Estado']) ?>
                    </div>
                    <?php if (Yii::$app->user->can('administrator')): ?>
                        <div class="mb-3">
                            <?php if ($gm): ?>
                                <?= Html::activeHiddenInput($model, 'mining_group_id') ?>
                                <?= $form->field($gm, 'name')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                            <?php else: ?>
                                <?= $form->field($model, 'mining_group_id')->dropDownList(
                                    ArrayHelper::map(
                                        MiningGroup::find()->all(), 
                                        'id', 
                                        'ges_name'
                                    ),
                                    ['prompt' => 'Selecciona un grupo minero']
                                ) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="fw-bold">Roles y Capacidades</h5>
                    <div class="mb-3">
                        <?= $form->field($model, 'roles')->checkboxList($roles, [
                            'item' => function($index, $label, $name, $checked, $value) {
                                return '<div class="custom-checkbox">' . 
                                    Html::checkbox($name, $checked, [
                                        'value' => $value,
                                        'label' => $label,
                                        'labelOptions' => ['style' => 'margin-left: 8px;'],
                                    ]) . 
                                '</div>';
                            }
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group text-center mt-4">
            <div class="d-flex flex-column align-items-center">
                <?= Html::submitButton('Actualizar Usuario', ['class' => 'btn btn-primary', 'style' => 'width: 20%; font-size: 14px; padding: 8px 16px; margin-bottom: 10px;']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
