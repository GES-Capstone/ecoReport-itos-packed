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

class ImportDataController extends Controller
{
    public function actionIndex($type = 'company')
    {
        $validTypes = ['company', 'machinery', 'component'];
        if (!in_array($type, $validTypes)) {
            $type = 'company';
        }

        $model = new ExcelUploadForm();
        $model->type = $type;

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

            if (isset(Yii::$app->request->post('ExcelUploadForm')['type'])) {
                $model->type = Yii::$app->request->post('ExcelUploadForm')['type'];
            }

            if ($model->upload()) {
                return $this->redirect(['process', 'key' => $model->getUploadKey()]);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'type' => $type,
            'templates' => $this->getTemplateUrls(),
        ]);
    }

    public function actionProcess($key)
    {
        $fileService = new FileService();
        $fileData = $fileService->verifyUpload($key);

        if (!$fileData) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid or expired file'));
            return $this->redirect(['index']);
        }

        try {
            if (!isset($fileData['type'])) {
                throw new \Exception(Yii::t('app', 'Import type not specified'));
            }
            $type = $fileData['type'];
            
            $importService = ImportServiceFactory::create($type);

            $stats = $importService->processFile($fileData['path'], Yii::$app->user->id);

            $fileService->cleanupFile($key);

            return $this->render('result', [
                'stats' => $stats,
                'fileName' => $fileData['original_name'],
                'type' => $type,
            ]);

        } catch (\Exception $e) {
            $errorMsg = Yii::t('app', 'Error processing file: {error}', ['error' => $e->getMessage()]);
            Yii::$app->session->setFlash('error', $errorMsg);

            $fileService->cleanupFile($key);

            return $this->redirect(['index']);
        }
    }

    public function actionTemplate($type)
    {
        $validTypes = ['company', 'machinery', 'component'];
        if (!in_array($type, $validTypes)) {
            $type = 'company';
        }

        $importService = ImportServiceFactory::create($type);

        $filename = $type . '_template.xlsx';
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

    private function getTemplateUrls()
    {
        return [
            'company' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'company']),
            'machinery' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'machinery']),
            'component' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'component']),
        ];
    }
}