<?php

if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
$script = <<<JS
document.querySelectorAll('.alert').forEach(alert => {
  // use the data-timeout attribute if set, else default to 3000ms
  const timeout = parseInt(alert.dataset.timeout, 10) || 3000;
  setTimeout(() => {
    alert.classList.remove('show');
    alert.classList.add('fade');
    setTimeout(() => alert.remove(), 500);
  }, timeout);
});
JS;
$this->registerJs($script);
?>