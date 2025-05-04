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
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="glyphicon glyphicon-ok-circle"></i> <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="glyphicon glyphicon-exclamation-sign"></i> <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-link"></i> Assign User to Mining Group</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['id' => 'assignment-form']); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <i class="glyphicon glyphicon-user"></i> User
                        </label>
                        <?= Html::dropDownList('user_id', $userId, $users, [
                            'class' => 'form-control select2',
                            'prompt' => 'Select User',
                            'required' => true
                        ]) ?>
                        <div class="help-block">Select the user to assign to a mining group</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">
                            <i class="glyphicon glyphicon-briefcase"></i> Mining Group
                        </label>
                        <?= Html::dropDownList('group_id', $groupId, $groups, [
                            'class' => 'form-control select2',
                            'prompt' => 'Select Mining Group',
                            'required' => true
                        ]) ?>
                        <div class="help-block">Select the mining group to assign to the user</div>
                    </div>
                </div>
            </div>

            <div class="form-group buttons-container">
                <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> Assign', [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Create New Mining Group', 
                    ['create'], 
                    ['class' => 'btn btn-primary']
                ) ?>
                <?= Html::a('<i class="glyphicon glyphicon-remove"></i> Cancel', 
                    ['/site/index'], 
                    ['class' => 'btn btn-default']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
.mining-group-assign {
    margin-bottom: 30px;
}
.panel {
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}
.panel-heading {
    background-color: #f8f8f8;
    border-bottom: 1px solid #eee;
}
.form-group {
    margin-bottom: 20px;
}
.help-block {
    color: #737373;
    font-size: 12px;
    margin-top: 5px;
}
.buttons-container {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
.alert {
    margin-top: 20px;
    padding: 15px;
}
.select2 {
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    border-radius: 4px;
    border: 1px solid #ccc;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
.select2:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}
label i {
    margin-right: 5px;
}
</style>

<script>
// Add enhancements if jQuery is available
$(document).ready(function() {
    // Initialize Select2 for better dropdown experience if available
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: 'Select an option',
            allowClear: true
        });
    }
});
</script>