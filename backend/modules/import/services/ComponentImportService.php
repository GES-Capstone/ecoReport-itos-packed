<?php
namespace backend\modules\import\services;
use Yii;
use backend\modules\import\services\ImportServiceInterface;
use common\models\User;
use common\models\Machinery;
use common\models\Location;
use common\models\Component;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ComponentImportService implements ImportServiceInterface
{
    public function processFile($filePath, $userId)
    {
        $miningGroup = $this->getMiningGroup($userId);
        if (!$miningGroup) {
            throw new \Exception('User not associated to mining group');
        }
        $stats = [
            "components_ created" => 0,
            'errors' => [],
        ];
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $machineryTag = trim($sheet->getCell('A' . $row)->getValue() ?? '');
                $componentName = trim($sheet->getCell('B' . $row)->getValue() ?? '');
                $componentTag = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $model = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                $startedOperations = trim($sheet->getCell('E' . $row)->getValue() ?? '');
                $usefulLifeYear = trim($sheet->getCell('F' . $row)->getValue() ?? '');
                $usefulLifeHours = trim($sheet->getCell('G' . $row)->getValue() ?? '');
                $supplier = trim($sheet->getCell('H' . $row)->getValue() ?? '');
                $componentCost = trim($sheet->getCell('I' . $row)->getValue() ?? '');
                $maintenancePlan = trim($sheet->getCell('J' . $row)->getValue() ?? '');
                $inspectionPlan = trim($sheet->getCell('K' . $row)->getValue() ?? '');
                $location = trim($sheet->getCell('L' . $row)->getValue() ?? '');

                $result = $this->processRow(
                    $machineryTag,
                    $componentName,
                    $componentTag,
                    $model,
                    $startedOperations,
                    $usefulLifeYear,
                    $usefulLifeHours,
                    $supplier,
                    $componentCost,
                    $maintenancePlan,
                    $inspectionPlan,
                    $location,
                    $miningGroup->id
                );
                if ($result['success']) {
                    if ($result['isNew']) {
                        $stats['components_created']++;
                    } else {
                        $stats['components_updated']++;
                    }
                } else {
                    $stats['errors'][] = "Fila $row: " . $result['error'];
                }
                return $stats;

            }
        } catch (\Exception $e) {
            throw new \Exception('Error procesando archivo: ' . $e->getMessage());
        }
    }
    public function processRow(
        $machineryTag,
        $componentName,
        $componentTag,
        $model,
        $startedOperations,
        $usefulLifeYear,
        $usefulLifeHours,
        $supplier,
        $componentCost,
        $maintenancePlan,
        $inspectionPlan,
        $location,
        $miningGroupId
    ) {
        $result = [
            'success' => false,
            'isNew' => true,
            'error' => null,
        ];

        // Validar campos uno por uno
        if (empty($machineryTag)) {
            $result['error'] = 'El campo Tag Equipo es requerido';
            return $result;
        }

        if (empty($componentName)) {
            $result['error'] = 'El campo Nombre Componente es requerido';
            return $result;
        }

        if (empty($componentTag)) {
            $result['error'] = 'El campo Tag Componente es requerido';
            return $result;
        }

        if (empty($model)) {
            $result['error'] = 'El campo Modelo es requerido';
            return $result;
        }

        if (empty($startedOperations)) {
            $result['error'] = 'El campo Inicio de Operaciones es requerido';
            return $result;
        }

        if (empty($usefulLifeYear)) {
            $result['error'] = 'El campo Vida Útil (Años) es requerido';
            return $result;
        }

        if (empty($usefulLifeHours)) {
            $result['error'] = 'El campo Vida Útil (Horas) es requerido';
            return $result;
        }

        if (empty($supplier)) {
            $result['error'] = 'El campo Proveedor es requerido';
            return $result;
        }

        if (empty($componentCost)) {
            $result['error'] = 'El campo Costo Componente es requerido';
            return $result;
        }

        if (empty($maintenancePlan)) {
            $result['error'] = 'El campo Plan de Mantenimiento es requerido';
            return $result;
        }

        if (empty($inspectionPlan)) {
            $result['error'] = 'El campo Plan de Inspección es requerido';
            return $result;
        }

        if (empty($location)) {
            $result['error'] = 'El campo Ubicación es requerido';
            return $result;
        }

        $machinery = $this->findMachinery($machineryTag, $miningGroupId);
        if (!$machinery) {
            $result['error'] = 'El equipo no existe';
            return $result;
        }
        
        $component = $this->createComponent($machinery, $componentName, $componentTag, $model, $startedOperations, $usefulLifeYear, $usefulLifeHours, $supplier, $componentCost, $maintenancePlan, $inspectionPlan, $location);

        return $result;
    }
    //por ahora asi, hay que cambiar el tag del equipo por el tag personalizado
    private function findMachinery($machineryTag,$miningGroupId)
    {
        $machinery = Machinery::findOne(['tag' => $machineryTag], ['mining_group_id' => $miningGroup->id]);
        if ($machinery) {
            return $machinery;
        }
        return null;
    }

    private function createComponent($machinery, $componentName, $componentTag, $model, $startedOperations, $usefulLifeYear, $usefulLifeHours, $supplier, $componentCost, $maintenancePlan, $inspectionPlan, $location){
        $component = new Component();
        $component->machinery_id = $machinery->id;
        $component->name = $componentName;
        $component->tag = $componentTag;
        $component->model = $model;
       
        $component->useful_life_years = $usefulLifeYear;
        $component->useful_life_hours = $usefulLifeHours;
        $component->supplier = $supplier;
        $component->cost = $componentCost;
        $component->maintenance_plan = $maintenancePlan;
        $component->inspection_plan = $inspectionPlan;

        $startedOperationsFormatted = $this->formatDate($startedOperations);
        $component->started_operations = $startedOperationsFormatted;
       
        $locationFormatted = $this->generateLocation($location);
        $component->location_id = $locationFormatted->id;


    }
    private function generateLocation($locationData)
    {
        $coordinates = explode(',', $locationData);
        if (count($coordinates) != 2) {
            throw new \Exception("Formato de ubicación inválido");
        }
        $latitude = (float) trim($coordinates[0] ?? '');
        $longitude = (float) trim($coordinates[1] ?? '');

        $location = new Location();
        $location->longitude = $longitude;
        $location->latitude = $latitude;

        if (!$location->save()) {
            throw new \Exception('Error saving location');
        }
        return $location;
    }
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);

        // Intentar formato DD-MM-YYYY
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Si es numérico, tratar como número de serie de Excel
        if (is_numeric($date)) {
            // La fecha base de Excel es 30/12/1899 para sistemas Windows
            $unixTimestamp = ($date - 25569) * 86400;
            $phpDate = date('Y-m-d', $unixTimestamp);

            // Verificar que la fecha producida es válida
            $dateParts = explode('-', $phpDate);
            if (count($dateParts) == 3) {
                $year = (int) $dateParts[0];
                $month = (int) $dateParts[1];
                $day = (int) $dateParts[2];

                if (checkdate($month, $day, $year)) {
                    return $phpDate;
                }
            }
        }

        return null;
    }
    public function generateTemplate($path)
    {

    }
    private function getMiningGroup($userId)
    {
        $user = User::findOne($userId);
        if ($user && $user->miningGroup) {
            return $user->miningGroup()->one();
        }
        return null;
    }
}