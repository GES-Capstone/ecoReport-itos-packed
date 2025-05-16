<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\MiningGroup $model */

$this->title = 'Create Mining Group';
$this->params['breadcrumbs'][] = ['label' => 'Mining Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mining-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>