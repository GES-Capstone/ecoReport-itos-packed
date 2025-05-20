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

/**
 * Controller for handling Excel data imports
 */
class ImportDataController extends Controller
{
    /**
     * Index action for file upload
     *
     * @param string $type Tipo de importación (company, fleet, machinery, component)
     * @return mixed
     */
    public function actionIndex($type = 'company')
    {
        // Validar el tipo de importación
        $validTypes = ['company', 'machinery', 'component'];
        if (!in_array($type, $validTypes)) {
            $type = 'company';
        }

        $model = new ExcelUploadForm();
        $model->type = $type; // Asignar el tipo al modelo

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

            // Obtener el tipo del formulario enviado
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

    /**
     * Processes a previously uploaded Excel file
     *
     * @param string $key Upload key generated during upload
     * @return mixed
     */
    public function actionProcess($key)
    {
        // Verificar archivo subido usando FileService
        $fileService = new FileService();
        $fileData = $fileService->verifyUpload($key);

        if (!$fileData) {
            Yii::$app->session->setFlash('error', 'Archivo inválido o expirado');
            return $this->redirect(['index']);
        }

        try {
            if (!isset($fileData['type'])) {
                throw new \Exception('Tipo de importación no especificado');
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
            $errorMsg = 'Error procesando archivo: ' . $e->getMessage();
            Yii::$app->session->setFlash('error', $errorMsg);

            // Limpiar el archivo en caso de error usando FileService
            $fileService->cleanupFile($key);

            return $this->redirect(['index']);
        }
    }

    /**
     * Action para descargar plantillas según el tipo
     *
     * @param string $type Tipo de importación
     * @return \yii\web\Response
     */
    public function actionTemplate($type)
    {
        $validTypes = ['company', 'machinery', 'component'];
        if (!in_array($type, $validTypes)) {
            $type = 'company';
        }

        // Generar la plantilla usando el servicio adecuado
        $importService = ImportServiceFactory::create($type);

        // Obtener el nombre del archivo de plantilla
        $filename = $type . '_template.xlsx';
        $templatePath = Yii::getAlias('@backend/web/templates/' . $filename);

        // Asegurarse que el directorio existe
        $dir = dirname($templatePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generar la plantilla si no existe
        if (!file_exists($templatePath) && method_exists($importService, 'generateTemplate')) {
            $importService->generateTemplate($templatePath);
        }

        return Yii::$app->response->sendFile($templatePath, $filename);
    }

    /**
     * Genera URLs para las plantillas
     *
     * @return array URLs de plantillas
     */
    private function getTemplateUrls()
    {
        return [
            'company' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'company']),
            'machinery' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'machinery']),
            'component' => Yii::$app->urlManager->createUrl(['import/import-data/template', 'type' => 'component']),
        ];
    }
}