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
use common\models\MiningProcess;
use common\models\Area;
use common\models\Fleet;
use trntv\filekit\actions\UploadAction;
use Intervention\Image\ImageManagerStatic;
use yii\helpers\ArrayHelper;


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
        $config = InitialConfiguration::findOne(['mining_group_id' => $modelGM->id]);
        if (!$modelGM) {
            throw new NotFoundHttpException('Grupo Minero no encontrado.');
        }

        if ($modelGM->load(Yii::$app->request->post())) {
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
            'config' => $config,
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

        $modelCompany = new Company(['mining_group_id' => $modelGM->id]);
        $modelLocation = new Location();
        $modelLocation->scenario = 'optional';

        $companies = Company::find()
            ->where(['mining_group_id' => $modelGM->id])
            ->all();

        $selectedCompanyId = Yii::$app->request->get('company_id'); // nombre en el formulario actual
        if ($selectedCompanyId) {
            $modelCompany = Company::findOne(['id' => $selectedCompanyId, 'mining_group_id' => $modelGM->id]);
            if (!$modelCompany) {
                throw new NotFoundHttpException('Compañía no encontrada.');
            }

            $modelLocation = $modelCompany->location ?? new Location();
            $modelLocation->scenario = 'optional';
        } else {
            $modelCompany = new Company(['mining_group_id' => $modelGM->id]);
            $modelLocation = new Location();
            $modelLocation->scenario = 'optional';
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if (!empty($post['Location']['location_url']) || !empty($post['Location']['latitude']) || !empty($post['Location']['longitude'])) {
                if ($modelLocation->load($post) && $modelLocation->save()) {
                    $modelCompany->location_id = $modelLocation->id;
                }
            }

            $config->step = max($config->step, 3);
            $config->save(false);

            if ($modelCompany->load($post)) {
                if ($modelCompany->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'Company saved successfully.'));
                    return $this->redirect(['initial-configuration/step2']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Failed to save company.'));
                    Yii::error($modelCompany->getErrors());
                }
            }
        }

        return $this->render('step2', [
            'modelCompany' => $modelCompany,
            'modelLocation' => $modelLocation,
            'companies' => $companies,
            'config' => $config,
            'modelGM' => $modelGM,
        ]);
    }

    
    public function actionStep3()
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

        if ($config->step < 3 || $config->status !== 'in progress') {
            throw new ForbiddenHttpException('No tienes permiso para acceder a esta página.');
        }

        $modelProcess = new MiningProcess();
        $modelProcess->mining_group_id = $modelGM->id;

        $modelLocation = new Location();
        $modelLocation->scenario = 'optional';
        $processes = MiningProcess::find()
            ->where(['mining_group_id' => $modelGM->id])
            ->all();
        $companies = Company::find()
            ->where(['mining_group_id' => $modelGM->id])
            ->all();
          
        $selectedProcessId = Yii::$app->request->get('process_id');
        if ($selectedProcessId) {
            $modelProcess = MiningProcess::findOne(['id' => $selectedProcessId, 'mining_group_id' => $modelGM->id]);
            if (!$modelProcess) {
                throw new NotFoundHttpException('Proceso minero no encontrado.');
            }
            $modelLocation = $modelProcess->location ?? new Location();
            $modelLocation->scenario = 'optional';
        } else {
            $modelProcess = new MiningProcess(['mining_group_id' => $modelGM->id]);
            $modelLocation = new Location();
            $modelLocation->scenario = 'optional';
        }
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if (!empty($post['Location']['location_url']) || !empty($post['Location']['latitude']) || !empty($post['Location']['longitude'])) {
                if ($modelLocation->load($post) && $modelLocation->save()) {
                    $modelProcess->location_id = $modelLocation->id;
                }
            }

            $config->step = max($config->step, 4);
            $config->save(false);

            if ($modelProcess->load($post)) {
                if ($modelProcess->save()) {
                    Yii::$app->session->setFlash('success', 'Proceso minero guardado correctamente.');
                    return $this->redirect(['initial-configuration/step3']);
                } else {
                    Yii::$app->session->setFlash('error', 'Error al guardar el proceso minero.');
                    Yii::error($modelProcess->getErrors());
                }
            }
        }

        return $this->render('step3', [
            'modelProcess' => $modelProcess,
            'modelLocation' => $modelLocation,
            'companies' => $companies,
            'config' => $config,
            'processes' => $processes,
        ]);
    }

    public function actionStep4()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new ForbiddenHttpException('Usuario no autenticado.');
        }

        $modelGM = MiningGroup::findOne($user->mining_group_id);
        if (!$modelGM) {
            throw new NotFoundHttpException('Grupo Minero no encontrado.');
        }

        $config = InitialConfiguration::findOne(['mining_group_id' => $modelGM->id]);
        if (!$config || $config->step < 4) {
            throw new ForbiddenHttpException('No tienes permiso para acceder a esta página.');
        }

        $company = Company::findOne(['mining_group_id' => $modelGM->id]);
        if (!$company) {
            throw new NotFoundHttpException('Compañía no encontrada.');
        }
        $modelArea = new Area();
        $modelLocation = new Location();
        $modelLocation->scenario = 'optional';
        $companies = Company::find()->where(['mining_group_id' => $modelGM->id])->all();
        $processes = MiningProcess::find()->where(['company_id' => $company->id])->all();
        $areas = Area::find()
        ->where(['mining_process_id' => ArrayHelper::getColumn($processes, 'id')])
        ->all();

        $selectedAreaId = Yii::$app->request->get('area_id');
        if ($selectedAreaId) {
            $modelArea = Area::findOne(['id' => $selectedAreaId]);
            if (!$modelArea) {
                throw new NotFoundHttpException('Área no encontrada.');
            }
            $modelLocation = $modelArea->location ?? new Location();
            $modelLocation->scenario = 'optional';
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

           if ($modelLocation->load($post) && !empty($modelLocation->location_url)) {
                if ($modelLocation->save()) {
                    $modelArea->location_id = $modelLocation->id;
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Error saving the location.'));
                    Yii::error($modelLocation->getErrors());
                }
            }

            if ($modelArea->load($post)) {

                $process = MiningProcess::findOne($modelArea->mining_process_id);
                if ($process) {
                    $modelArea->mining_process_id = $process->id;
                }

                if ($modelArea->load($post)) {
                    if ($modelArea->save()) {
                        $config->step = max($config->step, 5);
                        $config->save(false);

                        Yii::$app->session->setFlash('success', Yii::t('backend', 'Área guardada correctamente.'));
                        return $this->redirect(['initial-configuration/step4']);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('backend', 'Error al guardar el área.'));
                        Yii::error($modelArea->getErrors());
                    }
                }

            }
        }

        return $this->render('step4', [
            'modelArea' => $modelArea,
            'modelLocation' => $modelLocation,
            'areas' => $areas,
            'companies' => $companies,
            'processes' => $processes,
            'config' => $config,
        ]);
    }

    public function actionStep5()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new ForbiddenHttpException('Usuario no autenticado.');
        }

        $modelGM = MiningGroup::findOne($user->mining_group_id);
        if (!$modelGM) {
            throw new NotFoundHttpException('Grupo Minero no encontrado.');
        }

        $config = InitialConfiguration::findOne(['mining_group_id' => $modelGM->id]);
        if (!$config || $config->step < 5) { 
            throw new ForbiddenHttpException('No tienes permiso para acceder a esta página.');
        }

        $company = Company::findOne(['mining_group_id' => $modelGM->id]);
        if (!$company) {
            throw new NotFoundHttpException('Compañía no encontrada.');
        }
        $modelFleet = new Fleet();
        $modelLocation = new Location();
        $modelLocation->scenario = 'optional';
        $modelProcesses = MiningProcess::find()
            ->where(['company_id' => $company->id])
            ->all();
        $modelAreas = Area::find()
            ->where(['mining_process_id' => ArrayHelper::getColumn($modelProcesses, 'id')])
            ->all();
        $fleetList = Fleet::find()
            ->where(['area_id' => ArrayHelper::getColumn($modelAreas, 'id')])
            ->all();
        
        $selectedFleetId = Yii::$app->request->get('fleet_id');
        if ($selectedFleetId) {
            $modelFleet = Fleet::findOne(['id' => $selectedFleetId]);
            if (!$modelFleet) {
                throw new NotFoundHttpException('Flota no encontrada.');
            }
            $modelLocation = $modelFleet->location ?? new Location();
            $modelLocation->scenario = 'optional';
        } else {
            $modelFleet = new Fleet();
            $modelLocation = new Location();
            $modelLocation->scenario = 'optional';
        }

        $modelLocation->scenario = 'optional';

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if ($modelLocation->load($post) && !empty($modelLocation->location_url)) {
                if ($modelLocation->save()) {
                    $modelFleet->location_id = $modelLocation->id;
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Error saving the location.'));
                    Yii::error($modelLocation->getErrors());
                }
            }

            if ($modelFleet->load($post)) {
                $area = Area::findOne($modelFleet->area_id);

                if ($modelFleet->save()) {
                    $config->step = max($config->step, 6);
                    $config->save(false);

                    Yii::$app->session->setFlash('success', Yii::t('backend', 'Fleet saved successfully.'));
                    return $this->redirect(['initial-configuration/step5']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('backend', 'Error saving the fleet.'));
                    Yii::error($modelFleet->getErrors());
                }
            }
        }

        return $this->render('step5', [
            'modelFleet' => $modelFleet,
            'modelLocation' => $modelLocation,
            'config' => $config,
            'fleetList' => $fleetList,
            'areaList' => $modelAreas,
        ]);
    }

}
