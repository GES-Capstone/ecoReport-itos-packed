<?php
namespace backend\modules\import\services;

/**
 * Factory para crear servicios de importación según el tipo
 */
class ImportServiceFactory
{
    /**
     * Crea el servicio apropiado según el tipo de importación
     */
    public static function create($type)
    {
        switch ($type) {

            case 'component':
                return new ComponentImportService();
            case 'machinery':
                return new MachineryImportService();
            default:
                return new CompanyImportService();
        }
    }
}