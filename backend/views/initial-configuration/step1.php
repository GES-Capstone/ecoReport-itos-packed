<?php

use common\models\GrupoMinero;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

$this->title = 'Nueva Carga';

?>
<?php $form = ActiveForm::begin([
    'id' => 'grupo-minero-form',
    'options' => ['enctype' => 'multipart/form-data']
]); ?>

<div class="container-profile">
    <p class="text-center fw-bold display-4 my-4">Carga de Datos Grupo Minero</p>

    <div class="d-flex flex-wrap justify-content-center" style="gap: 40px; width: 90%;">
        
        <div class="col-md-7">
            <?= $form->field($modelGM, 'name')->textInput(['maxlength' => 255, 'style' => 'font-size: 18px;']) ?>
            <?= $form->field($modelGM, 'description')->textarea(['rows' => 3, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelGM, 'commercial_address')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelGM, 'operational_address')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelGM, 'phone')->textInput(['maxlength' => true, 'style' => 'font-size: 16px;']) ?>
            <?= $form->field($modelGM, 'email')->textInput(['type' => 'email', 'style' => 'font-size: 16px;']) ?>
        </div>

        <!-- Carga de Logo -->
        <div class="col-md-4 mt-7">
            <?= $form->field($modelLocation, 'location_url')->textInput([
                'placeholder' => 'Ejemplo: -12.0464,-77.0428',
                'style' => 'font-size: 16px;',
            ]) ?>
            <div class="d-flex align-items-start" style="gap: 20px;">
                <?= $form->field($modelGM, 'picture')->widget(\trntv\filekit\widget\Upload::class, [
                    'url' => ['picture']
                ]) ?>
                
                <?= Html::img($modelGM->getLogo('/img/anonymous.png'), [
                    'id' => 'preview-image',
                    'class' => 'img-thumbnail mt-5',
                    'style' => 'width: 120px; height: 120px; object-fit: cover; cursor: zoom-in;',
                    'alt' => 'Avatar'
                ]) ?>
            </div>
        </div>
    </div>               
    <!-- Botones centrados -->
    <div class="text-center mt-4">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success px-4']) ?>
        <?= Html::a('Siguiente', ['initial-configuration/step2'], ['class' => 'btn btn-primary ms-2 px-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Modal para ver imagen en grande -->
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
