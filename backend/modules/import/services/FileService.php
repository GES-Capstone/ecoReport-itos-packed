<?php
namespace backend\modules\import\services;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * File Service class that handles file upload verification and cleanup
 */
class FileService extends Component
{
    /**
     * Verifies if an upload key is valid and belongs to the current user
     * 
     * @param string $uploadKey Upload key to verify
     * @return array|false File data if valid, false otherwise
     */
    public function verifyUpload($uploadKey)
    {
        if (empty($uploadKey)) {
            return false;
        }
        
        $sessionKey = 'uploadedFile_' . $uploadKey;
        $fileData = Yii::$app->session->get($sessionKey);
        
        if (!$fileData || 
            !isset($fileData['user_id']) || 
            $fileData['user_id'] != Yii::$app->user->id ||
            !isset($fileData['path']) || 
            !file_exists($fileData['path']) ||
            (time() - $fileData['timestamp']) > 3600) { // 1 hour validity
            return false;
        }
        
        return $fileData;
    }
    
    /**
     * Removes a temporary file after processing
     * 
     * @param string $uploadKey Upload key
     * @return bool
     */
    public function cleanupFile($uploadKey)
    {
        $sessionKey = 'uploadedFile_' . $uploadKey;
        $fileData = Yii::$app->session->get($sessionKey);
        
        if ($fileData && isset($fileData['path']) && file_exists($fileData['path'])) {
            @unlink($fileData['path']);
        }
        
        Yii::$app->session->remove($sessionKey);
        return true;
    }
}