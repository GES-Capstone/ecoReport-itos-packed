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

                // Procesar la fila y obtener resultado
                $result = $this->processCompanyRow($company, $locationData, $miningGroup->id);

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

    private function processCompanyRow($companyData,$locationData,$miningGroupId){
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
            //
            $company = Company::findOne(['name' => $companyData,'mining_group_id' => $miningGroupId]);
            $isNew = false;
            if (!$company){
                $isNew = true;
                $company = new Company();
                $company->name = $companyData;
                $company->mining_group_id = $miningGroupId;
            }
            $company->location_id = $location->id;
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
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4'],
        ],
    ]);

    // Auto-ajustar anchos de columna
    foreach(range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Ejemplo de datos
    $sheet->setCellValue('A2', 'Empresa Ejemplo');
    $sheet->setCellValue('B2', '-33.4489, -70.6693');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($path);

    return true;
    }
}
