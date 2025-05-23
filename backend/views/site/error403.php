<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Access Error';
?>

<div class="site-error" style="text-align: center; padding: 80px 20px;">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Yii::t('backend', 'You are not allowed to access this page or it doesn\'t exist') ?></p>
    <p><?= Yii::t('backend', 'If you believe this is an error, please contact the administrator.') ?></p>
    <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary"><?= Yii::t('backend', 'Go to Main Page') ?></a>
    <!-- Or log out -->
    <a href="<?= Url::to(['/sign-in/logout']) ?>" data-method="post" class="btn btn-secondary"><?= Yii::t('backend', 'Logout') ?></a>

</div>