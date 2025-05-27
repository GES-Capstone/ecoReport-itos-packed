<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$this->title = Yii::t('backend', 'User Creation');
$this->registerJsFile('@web/js/user/user-create.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $messages) {
    $class = "alert alert-" . ($key == 'error' ? 'danger' : $key);
    $messages = is_array($messages) ? $messages : [$messages];

    foreach ($messages as $message) {
        echo "<div class=\"{$class} mb-3\">{$message}</div>";
    }
}
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card-header bg-primary text-white text-center position-relative pt-3" style="height: 60px;">
    <a href="<?= Yii::$app->urlManager->createUrl(['/home/edit']) ?>" class="position-absolute start-0 ms-3 text-white" style="text-decoration: none;">
        <i class="fa fa-arrow-left fa-lg"></i>
    </a>
    <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
</div>

<div class="row g-3 pt-3">
    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3"><?= Yii::t('backend', 'User Data') ?></h5>
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'First Name'), 'class' => 'form-control']) ?>
            <?= $form->field($model, 'middlename')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Middle Name'), 'class' => 'form-control']) ?>
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Last Name'), 'class' => 'form-control']) ?>
            <?= $form->field($model, 'profession')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Profession'), 'class' => 'form-control']) ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Username'), 'class' => 'form-control']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => Yii::t('backend', 'Email Address'), 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3"><?= Yii::t('backend', 'Mining Group, Role and Status') ?></h5>

            <?php if (Yii::$app->user->can('administrator')): ?>
                <?= Html::hiddenInput('selected_mining_group_id', '', ['id' => 'selected_mining_group_id']) ?>

                <?= $form->field($modelGM, 'ges_name')->widget(AutoComplete::classname(), [
                    'clientOptions' => [
                        'source' => yii\helpers\Url::to(['home/search-groups']),
                        'minLength' => 1,
                        'autoFill' => true,
                        'select' => new JsExpression("function(event, ui) {
                            this.value = ui.item.value;
                            $('#selected_mining_group_id').val(ui.item.id);
                            return false;
                        }"),
                    ],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('backend', 'Type here to look for existing Mining Groups...'),
                    ]
                ])->label(Yii::t('backend', 'Mining Group'))->hint('<small class="text-muted">' .
                    Yii::t('backend', 'If the group already exists, the user will be assigned to that group. If it\'s new, it will be created automatically.') . '</small>') ?>
            <?php endif; ?>

            <?= $form->field($model, 'roles')->radioList($roles, [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return '<div class="form-check">'
                        . Html::radio($name, $checked, [
                            'value'        => $value,
                            'label'        => $label,
                            'class'        => 'form-check-input',
                            'labelOptions' => ['class' => 'form-check-label'],
                        ])
                        . '</div>';
                }
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList([
                2 => Yii::t('backend', 'Active'),
                1 => Yii::t('backend', 'Inactive'),
                3 => Yii::t('backend', 'Deleted')
            ], [
                'prompt' => Yii::t('backend', 'Select Status'),
                'class' => 'form-select'
            ]) ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 p-3 rounded-3">
            <h5 class="fw-bold mb-3"><?= Yii::t('backend', 'Additional Information') ?></h5>

            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fa fa-info-circle me-2"></i><?= Yii::t('backend', 'Reminder') ?>:</h6>
                <p class="mb-0"><?= Yii::t('backend', 'Complete all the required fields to create a user') ?></p>
            </div>

            <ul class="list-group mt-3">
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span><?= Yii::t('backend', 'All Users must belong to a Mining Group') ?></span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span><?= Yii::t('backend', 'At least one role must be assigned') ?></span>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <span><?= Yii::t('backend', 'Passwords must contain at least 6 characters') ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="form-group text-center mt-4">
    <?= Html::submitButton(Yii::t('backend', 'Create User'), ['class' => 'btn btn-success px-5 py-2']) ?>
    <?= Html::a(Yii::t('backend', 'Cancel'), ['home/edit'], ['class' => 'btn btn-outline-secondary px-5 py-2 ms-2']) ?>
</div>

<?php ActiveForm::end(); ?>