<?php

namespace backend\controllers;

use yii\web\Controller;
use Yii;
use yii\imagine\Image;
use backend\models\ProcesoMineroForm;
use backend\models\FamiliaEquipoForm;
use common\models\MiningGroup;
use common\models\InitialConfiguration;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\Company;
use common\models\Location;

use trntv\filekit\actions\UploadAction;
use Intervention\Image\ImageManagerStatic;


class InitialConfigurationController extends Controller
{
    public $layout = 'main'; 
    
    public function actions(){  
        return [
            'picture' => [
                'class' => UploadAction::class,
                'deleteRoute' => false,
                'on afterSave' => function ($event) {
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read());
                    $file->put($img->encode());
                }
            ],
        ];
    }

    public function actionStep1()
    {
        $user = Yii::$app->user->identity;

        if (!$user) {
            throw new ForbiddenHttpException('Usuario no autenticado.');
        }

        $modelGM = $user->mining_group_id ? MiningGroup::findOne($user->mining_group_id) : new MiningGroup();
        $modelLocation = $modelGM->location_id ? Location::findOne($modelGM->location_id) : new Location();
        if (!$modelGM) {
            throw new NotFoundHttpException('Grupo Minero no encontrado.');
        }

        if ($modelGM->load(Yii::$app->request->post())) {

            $config = InitialConfiguration::findOne(['mining_group_id' => $modelGM->id]);
            $config->step = max($config->step, 2);
            $config->status = 'in progress';

            if ($modelLocation->load(Yii::$app->request->post()) && $modelLocation->save()) {
                $modelGM->location_id = $modelLocation->id;
            }

            if ($config->save()  && $modelGM->save()) {
                Yii::$app->session->setFlash('success', 'Grupo Minero y configuración inicial guardados correctamente.');
            } else {
                Yii::$app->session->setFlash('error', 'Error al guardar configuración inicial.');
            }
        } elseif (Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('error', 'Error al guardar el grupo minero.');
        }

        return $this->render('step1', [
            'modelGM' => $modelGM,
            'modelLocation' => $modelLocation,
        ]);
    }

public function actionStep2()
{
    $user = Yii::$app->user->identity;
    if (!$user) {
        throw new ForbiddenHttpException('Usuario no autenticado.');
    }

    $modelGM = $user->mining_group_id ? MiningGroup::findOne($user->mining_group_id) : null;
    if (!$modelGM) {
        throw new NotFoundHttpException('Grupo Minero no encontrado.');
    }

    $config = InitialConfiguration::findOne(['mining_group_id' => $modelGM->id]);
    if (!$config) {
        throw new NotFoundHttpException('Configuración inicial no encontrada.');
    }

    if ($config->step < 2 || $config->status !== 'in progress') {
        throw new ForbiddenHttpException('No tienes permiso para acceder a esta página.');
    }

    $post = Yii::$app->request->post();
    
    if (isset($post['Company'])) {
        $modelCompany = null;

        if (!empty($post['Company']['id'])) {
            $modelCompany = Company::findOne([
                'id' => $post['Company']['id'],
                'mining_group_id' => $modelGM->id
            ]);
        }

        if (!$modelCompany) {
            $modelCompany = new Company();
            $modelCompany->mining_group_id = $modelGM->id;
        }
        
        if ($modelCompany->load($post)) {
           $modelLocation = $modelCompany->location_id
            ? Location::findOne($modelCompany->location_id)
            : new Location();

        if ($modelLocation->load(Yii::$app->request->post()) && $modelLocation->save()) {
            $modelCompany->location_id = $modelLocation->id;
        }

        if ($modelCompany->load(Yii::$app->request->post()) && $modelCompany->save()) {
            Yii::$app->session->setFlash('success', 'Compañía guardada correctamente.');
        }
            
            $config->step = max($config->step, 3);
            $config->save(false);

            return $this->redirect(['initial-configuration/step2']);
        } else {
            Yii::$app->session->setFlash('error', 'Error al guardar la compañía.');
            Yii::error($modelCompany->getErrors());
        }

        return $this->render('step2', [
            'modelCompany' => $modelCompany,
        ]);
    }

    // FLUJO DE SELECCIÓN
    if (!empty($post['create_new_company'])) {
        // Crear nueva
        $modelCompany = new Company();
        $modelCompany->mining_group_id = $modelGM->id;
        $modelLocation = $modelCompany->location_id ? Location::findOne($modelCompany->location_id) : new Location();
        return $this->render('step2', 
        ['modelCompany' => $modelCompany,'modelLocation' => $modelLocation]);
    }

    if (!empty($post['selected_company_id'])) {
        // Editar existente
        $modelCompany = Company::findOne([
            'id' => $post['selected_company_id'],
            'mining_group_id' => $modelGM->id
        ]);
        $modelLocation = $modelCompany->location_id ? Location::findOne($modelCompany->location_id) : new Location();
        if (!$modelCompany) {
            Yii::$app->session->setFlash('error', 'Compañía no encontrada.');
            return $this->redirect(['initial-configuration/step2']);
        }

        return $this->render('step2', ['modelCompany' => $modelCompany,
            'modelLocation' => $modelLocation,
        ]);
    }

    // Mostrar pantalla de selección
    $companies = Company::find()->where(['mining_group_id' => $modelGM->id])->all();
    return $this->render('step2_select', ['companies' => $companies]);
}


    
}
