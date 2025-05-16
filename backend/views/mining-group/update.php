<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\MiningGroup $model */

$this->title = 'Update Mining Group: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Mining Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mining-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
