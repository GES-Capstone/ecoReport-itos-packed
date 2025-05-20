<?php

namespace backend\modules\import\services;

use common\models\Company;
use common\models\Location;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;

class CompanyImportService implements ImportServiceInterface{

    public function processFile($filePath, $userId): array
    {
        $miningGroup = $this->getMiningGroup($userId);
        if (!$miningGroup){
            throw new \Exception('User not associated to mining group');
        }

        $stats = [
            'companies_created' => 0,
            'companies_updated' => 0,
            'errors' => [],
        ];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++){
                $company = $sheet->getCell('A' . $row)->getValue();
                $locationData = $sheet->getCell('B' . $row)->getValue();
                $description = $sheet->getCell('C' . $row)->getValue();
                $commercialAddress = $sheet->getCell('D' . $row)->getValue();
                $operationalAddress = $sheet->getCell('E' . $row)->getValue();
                $phone = $sheet->getCell('F' . $row)->getValue();
                $email = $sheet->getCell('G' . $row)->getValue();

                // Procesar la fila y obtener resultado
                $result = $this->processCompanyRow(
                    $company, 
                    $locationData, 
                    $miningGroup->id,
                    $description,
                    $commercialAddress,
                    $operationalAddress,
                    $phone,
                    $email
                );

                // Actualizar estadísticas según el resultado
                if ($result['success']) {
                    if ($result['isNew']) {
                        $stats['companies_created']++;
                    } else {
                        $stats['companies_updated']++;
                    }
                } else {
                    $stats['errors'][] = "Fila $row: " . $result['error'];
                }
            }

            return $stats;

        } catch (\Exception $e) {
            throw new \Exception('Error procesando archivo: ' . $e->getMessage());
        }
    }

    private function processCompanyRow(
        $companyData,
        $locationData,
        $miningGroupId,
        $description = null,
        $commercialAddress = null,
        $operationalAddress = null,
        $phone = null,
        $email = null
    ){
        $result = [
            'success' => false,
            'isNew' => false,
            'error' => null
        ];
        
        if(empty($companyData) || empty($locationData)){
            $result['error'] = "Empty company or location name";
            return $result;
        }
        
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $coordinates = explode(',', $locationData);
            if (count($coordinates) != 2) {
                throw new \Exception("Formato de ubicación inválido");
            }
            $latitude = (float) trim(is_null($coordinates[0]) ? '' : $coordinates[0]);
            $longitude = (float) trim(is_null($coordinates[1]) ? '' : $coordinates[1]);

            $location = new Location();
            $location->longitude = $longitude;
            $location->latitude = $latitude;

            if(!$location->save()){
                throw new \Exception('Error saving location');
            }
            
            $company = Company::findOne(['name' => $companyData, 'mining_group_id' => $miningGroupId]);
            $isNew = false;
            
            if (!$company){
                $isNew = true;
                $company = new Company();
                $company->name = $companyData;
                $company->mining_group_id = $miningGroupId;
            }
            
            $company->location_id = $location->id;
            
            // Asignar los campos adicionales
            if ($description !== null) {
                $company->description = $description;
            }
            
            if ($commercialAddress !== null) {
                $company->commercial_address = $commercialAddress;
            }
            
            if ($operationalAddress !== null) {
                $company->operational_address = $operationalAddress;
            }
            
            if ($phone !== null) {
                $company->phone = $phone;
            }
            
            if ($email !== null) {
                $company->email = $email;
            }
            
            if (!$company->save()) {
                throw new \Exception("Error guardando compañía");
            }

            $transaction->commit();

            $result['success'] = true;
            $result['isNew'] = $isNew;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function getMiningGroup($userId){
        $user = User::findOne($userId);
        if (!$user){
            return null;
        }
        return $user->getMiningGroup()->One();
    }
    
    public function generateTemplate($path): bool
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer títulos de columnas
        $sheet->setCellValue('A1', 'Compania');
        $sheet->setCellValue('B1', 'Location (lat,lng)');
        $sheet->setCellValue('C1', 'Descripcion');
        $sheet->setCellValue('D1', 'Direccion Comercial');
        $sheet->setCellValue('E1', 'Direccion Operacional');
        $sheet->setCellValue('F1', 'Telefono');
        $sheet->setCellValue('G1', 'Email');

        // Dar formato a encabezados
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '99CCFF'],
            ],
        ]);

        // Auto-ajustar anchos de columna
        foreach(range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Ejemplo de datos
        $sheet->setCellValue('A2', 'Empresa Ejemplo');
        $sheet->setCellValue('B2', '-33.4489, -70.6693');
        $sheet->setCellValue('C2', 'Descripción de la empresa');
        $sheet->setCellValue('D2', 'Av. Comercial 123');
        $sheet->setCellValue('E2', 'Ruta Operacional 456');
        $sheet->setCellValue('F2', '+56 9 1234 5678');
        $sheet->setCellValue('G2', 'contacto@empresa.cl');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        return true;
    }
}