<?php
namespace backend\modules\import\services;

class ImportServiceFactory
{

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