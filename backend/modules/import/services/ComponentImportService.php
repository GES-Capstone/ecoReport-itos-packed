<?php

namespace backend\modules\import\services;

use Yii;
use backend\modules\import\services\ImportServiceInterface;
use common\models\User;
use common\models\Machinery;
use common\models\Component;
use common\models\MiningGroup;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ComponentImportService implements ImportServiceInterface
{
    public function processFile($filePath, $userId, $miningGroupId)
    {
        if (Yii::$app->user->can('super-administrator') && $miningGroupId !== null) {
            $miningGroup = $this->getMiningGroup($miningGroupId);
        } else {
            $miningGroup = $this->getMiningGroupByUser($userId);
            if (!$miningGroup) {
                throw new \Exception('User not associated to mining group');
            }
        }

        $stats = [
            "components_created" => 0,  // Corregido: removido espacio extra
            'components_updated' => 0,  // Agregado: faltaba inicialización
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
            }

            // Movido fuera del bucle
            return $stats;
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
        $miningGroupId
    ) {
        $result = [
            'success' => false,
            'isNew' => true,
            'error' => null,
        ];

        // Validaciones mejoradas
        $validationErrors = $this->validateRow([
            'machineryTag' => $machineryTag,
            'componentName' => $componentName,
            'componentTag' => $componentTag,
            'model' => $model,
            'startedOperations' => $startedOperations,
            'usefulLifeYear' => $usefulLifeYear,
            'usefulLifeHours' => $usefulLifeHours,
            'supplier' => $supplier,
            'componentCost' => $componentCost,
        ]);

        if (!empty($validationErrors)) {
            $result['error'] = implode(', ', $validationErrors);
            return $result;
        }

        $machinery = $this->findMachinery($machineryTag, $miningGroupId);
        if (!$machinery) {
            $result['error'] = 'El equipo no existe';
            return $result;
        }

        // Verificar si el componente ya existe
        $existingComponent = Component::findOne(['tag' => $componentTag]);
        if ($existingComponent) {
            $result['isNew'] = false;
            $component = $existingComponent;
        } else {
            $component = new Component();
        }

        try {
            $createResult = $this->createComponent(
                $component,
                $machinery,
                $componentName,
                $componentTag,
                $model,
                $startedOperations,
                $usefulLifeYear,
                $usefulLifeHours,
                $supplier,
                $componentCost,
            );

            if ($createResult) {
                $result['success'] = true;
            } else {
                $result['error'] = 'Error al guardar el componente';
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function validateRow($data)
    {
        $errors = [];
        $requiredFields = [
            'machineryTag' => 'El campo Tag Equipo es requerido',
            'componentName' => 'El campo Nombre Componente es requerido',
            'componentTag' => 'El campo Tag Componente es requerido',
            'model' => 'El campo Modelo es requerido',
            'startedOperations' => 'El campo Inicio de Operaciones es requerido',
            'usefulLifeYear' => 'El campo Vida Útil (Años) es requerido',
            'usefulLifeHours' => 'El campo Vida Útil (Horas) es requerido',
            'supplier' => 'El campo Proveedor es requerido',
            'componentCost' => 'El campo Costo Componente es requerido',
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($data[$field])) {
                $errors[] = $message;
            }
        }

        return $errors;
    }

    private function findMachinery($machineryTag, $miningGroupId)
    {
        // Corregida la sintaxis del findOne
        $machinery = Machinery::findOne([
            'unique_tag' => $machineryTag,
            'mining_group_id' => $miningGroupId
        ]);

        return $machinery;
    }

    private function createComponent($component, $machinery, $componentName, $componentTag, $model, $startedOperations, $usefulLifeYear, $usefulLifeHours, $supplier, $componentCost)
    {
        $component->machinery_id = $machinery->id;
        $component->name = $componentName;
        $component->tag = $componentTag;
        $component->model = $model;
        $component->useful_life_years = $usefulLifeYear;
        $component->useful_life_hours = $usefulLifeHours;
        $component->supplier = $supplier;
        $component->cost = $componentCost;

        $startedOperationsFormatted = $this->formatDate($startedOperations);
        if ($startedOperationsFormatted === null) {
            throw new \Exception('Formato de fecha inválido');
        }
        $component->started_operations = $startedOperationsFormatted;


        // Guardar el componente
        if (!$component->save()) {
            $errors = implode(', ', $component->getFirstErrors());
            throw new \Exception('Error al guardar componente: ' . $errors);
        }

        return true;
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer las cabeceras con los nuevos campos
        $sheet->setCellValue('A1', 'TAG EQUIPO (ID)*');
        $sheet->setCellValue('B1', 'COMPONENTE*');
        $sheet->setCellValue('C1', 'TAG COMPONENTE*');
        $sheet->setCellValue('D1', 'MODELO');
        $sheet->setCellValue('E1', 'INICIO OPERACIONES (DD-MM-YYYY)*');
        $sheet->setCellValue('F1', 'VIDA ÚTIL COMPONENTE (años)');
        $sheet->setCellValue('G1', 'VIDA ÚTIL COMPONENTE (horas)');
        $sheet->setCellValue('H1', 'PROVEEDOR');
        $sheet->setCellValue('I1', 'COSTO COMPONENTE (MUSD)');

        // Aplicar estilos a las cabeceras
        $sheet->getStyle('A1:J1')->applyFromArray([
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
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }


        // Dar formato a los datos de ejemplo
        $sheet->getStyle('A2:J3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
        ]);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        return true;
    }


    private function getMiningGroupByUser($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            return null;
        }
        return $user->getMiningGroup()->one();
    }
    private function getMiningGroup($miningGroupId)
    {
        $miningGroup = MiningGroup::findOne($miningGroupId);
        if (!$miningGroup) {
            throw new \Exception('Mining group not found');
        }
        return $miningGroup;
    }
}
