<?php
namespace backend\modules\import\controllers;

use backend\modules\import\models\ExcelUploadForm;
use backend\modules\import\services\FileService;
use backend\modules\import\services\ImportService;
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
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new ExcelUploadForm();

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

            if ($model->upload()) {
                return $this->redirect(['process', 'key' => $model->getUploadKey()]);
            }
        }

        return $this->render('index', [
            'model' => $model,
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
        // No longer checking if it's POST, because the redirect always arrives as GET
        
        // Verify file and get path
        $fileService = new FileService();
        $fileData = $fileService->verifyUpload($key);
        
        if (!$fileData) {
            Yii::$app->session->setFlash('error', 'Invalid file or expired session');
            return $this->redirect(['index']);
        }
        
        try {
            // Process file
            $importService = new ImportService();
            $stats = $importService->processFile($fileData['path'], Yii::$app->user->id);
            
            // Clean up temporary file
            $fileService->cleanupFile($key);
            
            // Show results
            return $this->render('result', [
                'stats' => $stats,
                'fileName' => $fileData['original_name']
            ]);
            
        } catch (\Exception $e) {
            $errorMsg = 'Error processing file: ' . $e->getMessage();
            Yii::$app->session->setFlash('error', $errorMsg);
            
            // Clean up temporary file in case of error
            $fileService->cleanupFile($key);
            
            return $this->redirect(['index']);
        }
    }
}