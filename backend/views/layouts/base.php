<?php

use backend\assets\BackendAsset;
use yii\helpers\Html;


$bundle = BackendAsset::register($this);

$this->params['body-class'] = $this->params['body-class'] ?? null;
$keyStorage = Yii::$app->keyStorage;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<?php echo Html::beginTag('body', [
    'class' => implode(' ', [
        'sidebar-collapse',
        $this->params['body-class']
    ]),
]) ?>
<?php $this->beginBody() ?>

<?= $content ?>

<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center"
    style="background-color: rgba(0,0,0,0.6); z-index: 9999;">
    <div class="text-center text-white">
        <div class="spinner-grow text-light mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden"><?= Yii::t('backend', 'Loading, please wait...') ?></span>
        </div>
        <div class="fs-5"><?= Yii::t('backend', 'Loading, please wait...') ?></div>
    </div>
</div>

<?php $this->endBody() ?>
<?= Html::endTag('body') ?>

</html>
<?php $this->endPage() ?>