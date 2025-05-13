<?php

use common\models\MiningGroup;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Mining Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mining-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mining Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'location_id',
            'name',
            'ges_name',
            'description:ntext',
            //'commercial_address:ntext',
            //'operational_address:ntext',
            //'phone',
            //'email:email',
            //'created_at',
            //'updated_at',
            //'logo_path',
            //'logo_base_url:url',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, MiningGroup $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
