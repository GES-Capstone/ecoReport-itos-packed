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

<?php $this->endBody() ?>
<?= Html::endTag('body') ?>

</html>
<?php $this->endPage() ?>