<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]); ?>

<div class="container-profile">
    <p class="text-center fw-bold display-4 my-4"><?= Yii::t('backend', 'Company Data Entry') ?></p>
    <div class="d-flex flex-wrap justify-content-center" style="gap: 40px; width: 90%;">
        <?php if (!$modelCompany->isNewRecord): ?>
            <?= Html::activeHiddenInput($modelCompany, 'id') ?>
        <?php endif; ?>

        <div class="col-md-7">
            
            <?= $form->field($modelCompany, 'name')->textInput(['maxlength' => true, 'style' => 'font-size: 18px;']) ?>
            <?= $form->field($modelCompany, 'description')->textarea(['rows' => 3, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelCompany, 'commercial_address')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelCompany, 'operational_address')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelCompany, 'phone')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelCompany, 'email')->textInput(['type' => 'email', 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelLocation, 'location_url')->textInput([
                'placeholder' => Yii::t('backend', 'Example: -12.0464,-77.0428'),
                'style' => 'font-size: 16px;',
            ]) ?>

        </div>


        <div class="col-md-4 mt-4">
            <div class="d-flex align-items-start" style="gap: 20px;">
                <?= $form->field($modelCompany, 'picture')->widget(\trntv\filekit\widget\Upload::class, [
                    'url' => ['picture'],
                ]) ?>
                
                <?= Html::img($modelCompany->getLogo('/img/anonymous.png'), [
                    'id' => 'preview-image',
                    'class' => 'img-thumbnail mt-5',
                    'style' => 'width: 120px; height: 120px; object-fit: cover; cursor: zoom-in;',
                    'alt' => 'Avatar'
                ]) ?>
            </div>

        </div>
    </div>
    <div class="text-center mt-4">
        <?= Html::a(Yii::t('backend', 'Back'), ['initial-configuration/step2'], ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<div id="image-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.85); z-index:1050; align-items:center; justify-content:center;">
    <span id="close-modal" style="position:absolute; top:20px; right:30px; font-size:30px; color:white; cursor:pointer;">&times;</span>
    <img id="modal-image" src="" style="max-height:90%; max-width:90%; border-radius:10px; box-shadow:0 0 30px rgba(255,255,255,0.2);">
</div>

<?php
$this->registerJs(<<<JS
    const preview = document.getElementById('preview-image');
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-image');
    const closeBtn = document.getElementById('close-modal');

    if (preview) {
        preview.addEventListener('click', function () {
            modal.style.display = 'flex';
            modalImg.src = this.src;
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
JS);
?>

