<?php
namespace backend\modules\import\services;

/**
 * Interfaz que todos los servicios de importación deben implementar
 */
interface ImportServiceInterface
{
    /**
     * Procesa un archivo Excel
     */
    public function processFile($filePath, $userId,$miningGroupId);

    public function generateTemplate($path);
}