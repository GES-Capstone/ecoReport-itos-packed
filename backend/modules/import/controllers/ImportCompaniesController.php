<?php

namespace backend\modules\import\controllers;

use backend\modules\import\models\ExcelUploadForm;
use backend\modules\import\services\FileService;
use backend\modules\import\services\ImportServiceFactory;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ImportCompaniesController extends Controller
{
    public function actionIndex($miningGroupId = null)
    {
        $model = new ExcelUploadForm();
        $model->type = 'company';

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

            if ($model->upload()) {
                return $this->redirect(['process', 'key' => $model->getUploadKey(), 'miningGroupId' => $miningGroupId]);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'templateUrl' => $this->getCompanyTemplateUrl(),    
            'miningGroupId' => $miningGroupId,
        ]);
    }

    public function actionProcess($key, $miningGroupId = null)
    {
        $fileService = new FileService();
        $fileData = $fileService->verifyUpload($key);

        if (!$fileData) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid or expired file'));
            return $this->redirect(['index', 'miningGroupId' => $miningGroupId]);
        }

        try {
            // Validar que sea tipo company
            if (!isset($fileData['type']) || $fileData['type'] !== 'company') {
                throw new \Exception(Yii::t('app', 'Invalid file type for company import'));
            }
            
            // Reutilizar servicio existente
            $importService = ImportServiceFactory::create('company');

            $stats = $importService->processFile($fileData['path'], Yii::$app->user->id, $miningGroupId);

            $fileService->cleanupFile($key);

            return $this->render('result', [
                'stats' => $stats,
                'fileName' => $fileData['original_name'],
                'type' => 'company',
                'miningGroupId' => $miningGroupId,
            ]);

        } catch (\Exception $e) {
            $errorMsg = Yii::t('app', 'Error processing file: {error}', ['error' => $e->getMessage()]);
            Yii::$app->session->setFlash('error', $errorMsg);

            $fileService->cleanupFile($key);

            return $this->redirect(['index', 'miningGroupId' => $miningGroupId]);
        }
    }

    public function actionTemplate()
    {
        // Reutilizar servicio existente
        $importService = ImportServiceFactory::create('company');

        $filename = 'company_template.xlsx';
        $templatePath = Yii::getAlias('@backend/web/templates/' . $filename);

        $dir = dirname($templatePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists($templatePath) && method_exists($importService, 'generateTemplate')) {
            $importService->generateTemplate($templatePath);
        }

        return Yii::$app->response->sendFile($templatePath, $filename);
    }

    private function getCompanyTemplateUrl()
    {
        return Yii::$app->urlManager->createUrl(['import/import-companies/template']);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Solo usuarios autenticados
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'process' => ['GET'],
                    'template' => ['GET'],
                ],
            ],
        ];
    }
}